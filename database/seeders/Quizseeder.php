<?php

namespace Database\Seeders;

use App\Models\Certification;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\QuizConfiguration;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class QuizSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Récupérer toutes les leçons et certifications
        $lessons = Lesson::all();
        $certifications = Certification::all();

        // Questions possibles pour les quiz
        $questionTemplates = [
            'Quelle est la principale caractéristique de %s ?',
            'Comment peut-on implémenter %s ?',
            'Quel est le meilleur cas d\'utilisation pour %s ?',
            'Expliquez le concept de %s',
            'Quels sont les avantages de %s ?',
        ];

        // Créer des configurations et questions pour chaque leçon
        foreach ($lessons as $lesson) {
            QuizConfiguration::create([
                'configurable_type' => Lesson::class,
                'configurable_id' => $lesson->id,
                'total_questions' => 5,
                'difficulty_distribution' => [
                    'easy' => 40,
                    'medium' => 40,
                    'hard' => 20
                ],
                'passing_score' => 70,
                'time_limit' => 30, // 30 minutes
            ]);

            $this->createQuestions($lesson, $faker, $questionTemplates);
        }

        // Créer des configurations et questions pour chaque certification
        foreach ($certifications as $certification) {
            QuizConfiguration::create([
                'configurable_type' => Certification::class,
                'configurable_id' => $certification->id,
                'total_questions' => 20,
                'difficulty_distribution' => [
                    'easy' => 30,
                    'medium' => 40,
                    'hard' => 30
                ],
                'passing_score' => 75,
                'time_limit' => 60, // 60 minutes
            ]);

            $this->createQuestions($certification, $faker, $questionTemplates, 20);
        }
    }

    private function createQuestions($model, $faker, $questionTemplates, $count = 5): void
    {
        $difficulties = ['easy', 'medium', 'hard'];

        for ($i = 0; $i < $count; $i++) {
            $difficulty = $faker->randomElement($difficulties);
            $questionTemplate = $faker->randomElement($questionTemplates);
            $topic = $faker->words(3, true);

            // Créer 4 réponses dont une correcte
            $answers = [];
            $correctAnswer = $faker->numberBetween(0, 3);

            for ($j = 0; $j < 4; $j++) {
                $answers[] = [
                    'id' => $j,
                    'text' => $faker->sentence(),
                    'correct' => ($j === $correctAnswer)
                ];
            }

            Question::create([
                'questionable_type' => get_class($model),
                'questionable_id' => $model->id,
                'question' => sprintf($questionTemplate, $topic),
                'answers' => $answers,
                'difficulty' => $difficulty,
                'points' => 1  // Chaque question vaut 1 point
            ]);
        }
    }
}
