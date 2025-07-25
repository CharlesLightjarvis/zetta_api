<?php

namespace Database\Seeders;

use App\Models\Certification;
use App\Models\Chapter;
use App\Models\Question;
use Illuminate\Database\Seeder;

class ChapterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $certifications = Certification::all();

        if ($certifications->isEmpty()) {
            // Create some sample certifications if none exist
            $certifications = Certification::factory(3)->create();
        }

        foreach ($certifications as $certification) {
            $this->seedChaptersForCertification($certification);
        }
    }

    /**
     * Seed chapters and questions for a specific certification.
     */
    private function seedChaptersForCertification(Certification $certification): void
    {
        // Define realistic chapter topics based on common certification areas
        $chapterTopics = [
            [
                'name' => 'Fundamentals and Core Concepts',
                'description' => 'Basic principles and foundational knowledge required for certification.',
                'question_count' => 15
            ],
            [
                'name' => 'Security and Best Practices',
                'description' => 'Security protocols, authentication, authorization, and industry best practices.',
                'question_count' => 12
            ],
            [
                'name' => 'Implementation and Configuration',
                'description' => 'Practical implementation steps, configuration management, and setup procedures.',
                'question_count' => 18
            ],
            [
                'name' => 'Troubleshooting and Maintenance',
                'description' => 'Problem-solving techniques, debugging methods, and system maintenance.',
                'question_count' => 10
            ],
            [
                'name' => 'Advanced Topics and Integration',
                'description' => 'Advanced features, third-party integrations, and complex scenarios.',
                'question_count' => 8
            ]
        ];

        foreach ($chapterTopics as $index => $topic) {
            $chapter = Chapter::factory()
                ->withOrder($index + 1)
                ->create([
                    'certification_id' => $certification->id,
                    'name' => $topic['name'],
                    'description' => $topic['description'],
                ]);

            // Create questions for this chapter
            $this->seedQuestionsForChapter($chapter, $topic['question_count']);
        }
    }

    /**
     * Seed questions for a specific chapter.
     */
    private function seedQuestionsForChapter(Chapter $chapter, int $questionCount): void
    {
        // Create a variety of question types and difficulties
        $difficulties = ['easy', 'medium', 'hard'];
        $choiceTypes = ['single', 'multiple']; // single or multiple correct answers

        for ($i = 0; $i < $questionCount; $i++) {
            $difficulty = $difficulties[$i % count($difficulties)];
            $choiceType = $choiceTypes[$i % count($choiceTypes)];
            
            Question::factory()
                ->forChapter($chapter->id)
                ->create([
                    'difficulty' => $difficulty,
                    'type' => 'certification',
                    'question' => $this->generateRealisticQuestion($chapter->name, $difficulty),
                    'answers' => $this->generateRealisticAnswers($choiceType),
                    'points' => $this->getPointsForDifficulty($difficulty),
                ]);
        }
    }

    /**
     * Generate realistic questions based on chapter topic and difficulty.
     */
    private function generateRealisticQuestion(string $chapterName, string $difficulty): string
    {
        $questionStarters = [
            'easy' => [
                'What is the primary purpose of',
                'Which of the following describes',
                'What does the term',
                'Which statement is true about'
            ],
            'medium' => [
                'How would you implement',
                'What is the best approach to',
                'Which configuration would you use for',
                'What are the key considerations when'
            ],
            'hard' => [
                'In a complex scenario where',
                'How would you troubleshoot',
                'What advanced technique would you use to',
                'Analyze the following situation and determine'
            ]
        ];

        $topics = [
            'Fundamentals and Core Concepts' => ['basic principles', 'core functionality', 'system architecture', 'data structures'],
            'Security and Best Practices' => ['authentication', 'authorization', 'encryption', 'security protocols'],
            'Implementation and Configuration' => ['system setup', 'configuration management', 'deployment strategies', 'environment setup'],
            'Troubleshooting and Maintenance' => ['error diagnosis', 'performance optimization', 'system monitoring', 'maintenance procedures'],
            'Advanced Topics and Integration' => ['complex integrations', 'advanced features', 'scalability solutions', 'custom implementations']
        ];

        $starters = $questionStarters[$difficulty] ?? $questionStarters['medium'];
        $chapterTopics = $topics[$chapterName] ?? ['general concepts'];

        $starter = $starters[array_rand($starters)];
        $topic = $chapterTopics[array_rand($chapterTopics)];

        return $starter . ' ' . $topic . '?';
    }

    /**
     * Generate realistic answers based on choice type.
     */
    private function generateRealisticAnswers(string $choiceType): array
    {
        $correctAnswers = [
            'Enable proper configuration and follow best practices',
            'Implement security measures and validate input',
            'Use appropriate design patterns and architecture',
            'Follow industry standards and documentation',
            'Configure system parameters correctly'
        ];

        $incorrectAnswers = [
            'Ignore security considerations for simplicity',
            'Use deprecated methods and outdated practices',
            'Skip validation and error handling',
            'Implement without proper testing',
            'Use hardcoded values and configurations',
            'Bypass authentication mechanisms',
            'Ignore performance implications',
            'Use insecure communication protocols'
        ];

        $answers = [];
        
        if ($choiceType === 'multiple') {
            // Add two correct answers for multiple choice
            $answers[] = [
                'id' => 1,
                'text' => $correctAnswers[array_rand($correctAnswers)],
                'correct' => true
            ];
            $answers[] = [
                'id' => 2,
                'text' => $correctAnswers[array_rand($correctAnswers)],
                'correct' => true
            ];
            
            // Add two incorrect answers
            $shuffledIncorrect = $incorrectAnswers;
            shuffle($shuffledIncorrect);
            
            for ($i = 3; $i <= 4; $i++) {
                $answers[] = [
                    'id' => $i,
                    'text' => $shuffledIncorrect[$i - 3],
                    'correct' => false
                ];
            }
        } else {
            // Single choice - one correct answer
            $answers[] = [
                'id' => 1,
                'text' => $correctAnswers[array_rand($correctAnswers)],
                'correct' => true
            ];

            // Add three incorrect answers
            $shuffledIncorrect = $incorrectAnswers;
            shuffle($shuffledIncorrect);
            
            for ($i = 2; $i <= 4; $i++) {
                $answers[] = [
                    'id' => $i,
                    'text' => $shuffledIncorrect[$i - 2],
                    'correct' => false
                ];
            }
        }

        return $answers;
    }

    /**
     * Get points based on difficulty level.
     */
    private function getPointsForDifficulty(string $difficulty): int
    {
        return match($difficulty) {
            'easy' => rand(1, 3),
            'medium' => rand(3, 6),
            'hard' => rand(6, 10),
            default => 5
        };
    }
}