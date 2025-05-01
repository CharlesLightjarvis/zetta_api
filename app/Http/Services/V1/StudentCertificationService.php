<?php

namespace App\Http\Services\V1;

use App\Http\Resources\v1\CertificationQuizResource;
use App\Http\Resources\v1\CertificationResource;
use App\Models\Certification;
use App\Models\ProgressTracking;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentCertificationService
{
    public function getStudentCertifications($studentId)
    {
        $user = User::findOrFail($studentId);
        $formations = $user->formations()->with('certifications.formation')->get();

        return CertificationResource::collection(
            $formations->pluck('certifications')->flatten()
        );
    }

    public function getCertificationDetails($studentId, $certificationId)
    {
        $user = User::findOrFail($studentId);
        $certification = Certification::whereHas('formation.students', function ($query) use ($studentId) {
            $query->where('users.id', $studentId);
        })->findOrFail($certificationId);

        $certification->load('formation');

        return new CertificationResource($certification);
    }

    public function getCertificationQuizQuestions($studentId, $certificationId)
    {
        $user = User::findOrFail($studentId);
        $certification = Certification::whereHas('formation.students', function ($query) use ($studentId) {
            $query->where('users.id', $studentId);
        })
            ->with(['quizConfiguration', 'questions'])
            ->findOrFail($certificationId);

        // Logique pour sélectionner les questions selon la configuration du quiz
        $questions = $this->selectQuestionsBasedOnConfig($certification);

        return new CertificationQuizResource([
            'certification' => $certification,
            'questions' => $questions,
            'time_limit' => $certification->quizConfiguration->time_limit
        ]);
    }

    public function submitQuiz($studentId, $certificationId, $answers)
    {
        DB::beginTransaction();
        try {
            // First verify the certification exists and load it with necessary relations
            $certification = Certification::with(['quizConfiguration', 'questions'])
                ->whereHas('formation.students', function ($query) use ($studentId) {
                    $query->where('users.id', $studentId);
                })
                ->findOrFail($certificationId);

            $totalScore = 0;
            $maxScore = 0;
            $answerDetails = [];

            foreach ($certification->questions as $question) {
                $maxScore += $question->points;
                $studentAnswer = $answers[$question->id] ?? null;

                $correct = false;
                if ($studentAnswer !== null) {
                    $correctAnswer = collect($question->answers)->first(function ($answer) {
                        return $answer['correct'] === true;
                    });
                    $correct = $studentAnswer === $correctAnswer['id'];
                    if ($correct) {
                        $totalScore += $question->points;
                    }
                }

                $answerDetails[] = [
                    'question_id' => $question->id,
                    'student_answer' => $studentAnswer,
                    'correct' => $correct,
                    'points_earned' => $correct ? $question->points : 0,
                    'points_possible' => $question->points
                ];
            }

            $percentageScore = ($totalScore / $maxScore) * 100;
            $passed = $percentageScore >= $certification->quizConfiguration->passing_score;

            // Create progress tracking with explicit certification relationship
            $progress = new ProgressTracking([
                'user_id' => $studentId,
                'trackable_type' => Certification::class,
                'trackable_id' => $certificationId,
                'answer_details' => $answerDetails,
                'score' => $percentageScore,
                'passed' => $passed,
                'attempt_number' => $this->getAttemptNumber($studentId, $certificationId),
                'completed_at' => now()
            ]);

            // Save and refresh to load relationships
            $progress->save();
            $progress->load('trackable.questions');

            DB::commit();
            return $this->formatQuizResult($progress);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getQuizResult($studentId, $certificationId, $progressTrackingId)
    {
        $progress = ProgressTracking::with('trackable.questions')
            ->where('user_id', $studentId)
            ->where('trackable_id', $certificationId)
            ->where('trackable_type', Certification::class)
            ->findOrFail($progressTrackingId);  // Ceci retourne un seul modèle

        return $this->formatQuizResult($progress);
    }


    private function formatQuizResult($progress)
    {
        $certification = $progress->trackable;

        if (!$certification) {
            Log::error('Missing certification for progress tracking', [
                'progress_id' => $progress->id,
                'trackable_id' => $progress->trackable_id,
                'trackable_type' => $progress->trackable_type
            ]);
            throw new ModelNotFoundException('Certification not found');
        }

        $questions = $certification->questions;

        if (!$questions) {
            Log::error('No questions found for certification', [
                'certification_id' => $certification->id,
                'progress_id' => $progress->id
            ]);
            return [
                'certification_name' => $certification->name,
                'score' => $progress->score,
                'passing_score' => $certification->quizConfiguration->passing_score,
                'passed' => $progress->passed,
                'attempt_number' => $progress->attempt_number,
                'completed_at' => $progress->completed_at->format('Y-m-d H:i:s'),
                'questions' => [],
                'total_questions' => 0,
                'correct_answers' => 0,
            ];
        }

        $detailedQuestions = $questions->map(function ($question) use ($progress) {
            $answerDetail = collect($progress->answer_details)
                ->firstWhere('question_id', $question->id);

            if (!$answerDetail) {
                return null;
            }

            $correctAnswer = collect($question->answers)
                ->first(function ($answer) {
                    return $answer['correct'] === true;
                });

            if (!$correctAnswer) {
                return null;
            }

            return [
                'question' => $question->question,
                'student_answer' => $answerDetail['student_answer'] !== null
                    ? ($question->answers[$answerDetail['student_answer']]['text'] ?? 'Réponse non trouvée')
                    : 'Non répondu',
                'correct_answer' => $correctAnswer['text'],
                'is_correct' => $answerDetail['correct'],
                'points_earned' => $answerDetail['points_earned'],
                'points_possible' => $answerDetail['points_possible'],
                'difficulty' => $question->difficulty
            ];
        })->filter();

        return [
            'certification_name' => $certification->name,
            'score' => $progress->score,
            'passing_score' => $certification->quizConfiguration->passing_score,
            'passed' => $progress->passed,
            'attempt_number' => $progress->attempt_number,
            'completed_at' => $progress->completed_at->format('Y-m-d H:i:s'),
            'questions' => $detailedQuestions,
            'total_questions' => $questions->count(),
            'correct_answers' => collect($progress->answer_details)->where('correct', true)->count(),
        ];
    }

    private function getAttemptNumber($studentId, $certificationId)
    {
        return ProgressTracking::where('user_id', $studentId)
            ->where('trackable_type', Certification::class)
            ->where('trackable_id', $certificationId)
            ->count() + 1;
    }

    private function selectQuestionsBasedOnConfig($certification)
    {
        $config = $certification->quizConfiguration;
        $questions = $certification->questions;

        // Logique de sélection des questions selon la difficulté et le nombre total
        // À implémenter selon vos besoins spécifiques
        return $questions->random($config->total_questions);
    }
}
