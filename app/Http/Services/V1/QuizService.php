<?php

namespace App\Http\Services\V1;

use App\Models\QuizConfiguration;
use App\Models\Question;
use App\Models\Lesson;
use App\Models\Certification;
use Illuminate\Support\Facades\DB;

class QuizService
{
    public function createQuizConfiguration(array $data)
    {
        try {
            DB::beginTransaction();

            // Vérifier que la somme des pourcentages est égale à 100
            $totalPercentage = $data['difficulty_distribution']['easy'] +
                $data['difficulty_distribution']['medium'] +
                $data['difficulty_distribution']['hard'];

            if ($totalPercentage !== 100) {
                throw new \InvalidArgumentException('La somme des pourcentages de difficulté doit être égale à 100');
            }

            // Déterminer le type de modèle
            $modelClass = match ($data['configurable_type']) {
                'lesson' => Lesson::class,
                'certification' => Certification::class,
                default => throw new \InvalidArgumentException('Invalid configurable type')
            };

            // Vérifier si l'entité existe
            $configurable = $modelClass::findOrFail($data['configurable_id']);

            // Créer la configuration
            $quizConfig = QuizConfiguration::create([
                'configurable_type' => $modelClass,
                'configurable_id' => $configurable->id,
                'total_questions' => $data['total_questions'],
                'difficulty_distribution' => $data['difficulty_distribution'],
                'passing_score' => $data['passing_score'],
                'time_limit' => $data['time_limit']
            ]);

            DB::commit();
            return $quizConfig;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function createQuestion(array $data)
    {
        try {
            DB::beginTransaction();

            // Correction du type de modèle
            $modelClass = match ($data['questionable_type']) {
                'lesson' => Lesson::class,
                'certification' => Certification::class,
                default => throw new \InvalidArgumentException('Invalid questionable type')
            };

            // Vérifier si l'entité existe
            $questionable = $modelClass::findOrFail($data['questionable_id']);

            // Créer la question avec le type correct
            $question = Question::create([
                'questionable_type' => $modelClass,  // Pas de changement ici
                'questionable_id' => $questionable->id,
                'question' => $data['question'],
                'answers' => $data['answers'],
                'difficulty' => $data['difficulty'],
                'points' => $data['points']
            ]);

            DB::commit();
            return $question;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateQuizConfiguration($id, array $data)
    {
        try {
            DB::beginTransaction();

            $quizConfig = QuizConfiguration::findOrFail($id);

            if (isset($data['difficulty_distribution'])) {
                $totalPercentage = $data['difficulty_distribution']['easy'] +
                    $data['difficulty_distribution']['medium'] +
                    $data['difficulty_distribution']['hard'];

                if ($totalPercentage !== 100) {
                    throw new \InvalidArgumentException('La somme des pourcentages de difficulté doit être égale à 100');
                }
            }

            $quizConfig->update($data);

            DB::commit();
            return $quizConfig;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteQuizConfiguration($id)
    {
        try {
            DB::beginTransaction();

            $quizConfig = QuizConfiguration::findOrFail($id);
            $quizConfig->delete();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function updateQuestion($id, array $data)
    {
        try {
            DB::beginTransaction();

            $question = Question::findOrFail($id);
            $question->update($data);

            DB::commit();
            return $question;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteQuestion($id)
    {
        try {
            DB::beginTransaction();

            $question = Question::findOrFail($id);
            $question->delete();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function getQuizConfiguration($id)
    {
        return QuizConfiguration::with('configurable')->findOrFail($id);
    }

    public function getQuestion($id)
    {
        return Question::with('questionable')->findOrFail($id);
    }

    public function getAllQuizConfigurations()
    {
        return QuizConfiguration::with('configurable')->get();
    }

    public function getAllQuestions()
    {
        return Question::with('questionable')->get();
    }
}
