<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Quiz;
use Illuminate\Database\Seeder;

class QuizzesSeeder extends Seeder
{
    public function run(): void
    {
        $blueprint = [
            [
                'title' => 'Mid-Course Quiz',
                'type' => 'quiz',
                'description' => 'A short quiz covering the first half of the course. You need 60% to pass.',
                'passing_score' => 60,
                'duration_minutes' => 20,
                'max_attempts' => 3,
                'shuffle_questions' => false,
                'shuffle_options' => true,
                'questions' => [
                    [
                        'question' => 'Which of the following best describes the primary goal of this module?',
                        'points' => 1,
                        'options' => [
                            ['Memorizing syntax without practice', false],
                            ['Building practical, real-world skills step by step', true],
                            ['Skipping fundamentals to save time', false],
                            ['Only watching videos without projects', false],
                        ],
                    ],
                    [
                        'question' => 'What is the recommended way to reinforce what you learn in each lesson?',
                        'points' => 1,
                        'options' => [
                            ['Rewatch lessons without practicing', false],
                            ['Copy-paste code from forums', false],
                            ['Complete the hands-on exercises and small projects', true],
                            ['Read the transcript once', false],
                        ],
                    ],
                    [
                        'question' => 'True or False: Consistent daily practice is more effective than cramming before a deadline.',
                        'points' => 2,
                        'options' => [
                            ['True', true],
                            ['False', false],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Final Exam',
                'type' => 'final_exam',
                'description' => 'Comprehensive final exam covering all modules. Score at least 70% to earn your certificate.',
                'passing_score' => 70,
                'duration_minutes' => 45,
                'max_attempts' => 2,
                'shuffle_questions' => true,
                'shuffle_options' => true,
                'questions' => [
                    [
                        'question' => 'Which statement about the core concepts covered in this course is correct?',
                        'points' => 3,
                        'options' => [
                            ['Core concepts are optional when building real projects', false],
                            ['A solid grasp of fundamentals is essential for advanced topics', true],
                            ['Advanced topics replace the need for fundamentals', false],
                            ['Fundamentals only matter for certification exams', false],
                        ],
                    ],
                    [
                        'question' => 'When debugging a problem, what is the best first step?',
                        'points' => 2,
                        'options' => [
                            ['Randomly change code until it works', false],
                            ['Reproduce the issue and isolate the smallest failing case', true],
                            ['Delete the feature causing issues', false],
                            ['Restart the computer and hope for the best', false],
                        ],
                    ],
                    [
                        'question' => 'Which approach demonstrates professional best practices?',
                        'points' => 2,
                        'options' => [
                            ['Writing everything in one large file', false],
                            ['Avoiding documentation to move faster', false],
                            ['Breaking work into clear, testable pieces with meaningful names', true],
                            ['Skipping code reviews', false],
                        ],
                    ],
                    [
                        'question' => 'True or False: Version control is an essential tool for collaborative projects.',
                        'points' => 3,
                        'options' => [
                            ['True', true],
                            ['False', false],
                        ],
                    ],
                ],
            ],
        ];

        foreach (Course::all() as $course) {
            foreach ($blueprint as $entry) {
                $quiz = Quiz::firstOrCreate(
                    ['course_id' => $course->id, 'title' => $entry['title']],
                    [
                        'type' => $entry['type'],
                        'description' => $entry['description'],
                        'passing_score' => $entry['passing_score'],
                        'duration_minutes' => $entry['duration_minutes'],
                        'max_attempts' => $entry['max_attempts'],
                        'shuffle_questions' => $entry['shuffle_questions'],
                        'shuffle_options' => $entry['shuffle_options'],
                        'is_active' => true,
                    ]
                );

                foreach ($entry['questions'] as $i => $q) {
                    $question = $quiz->questions()
                        ->firstOrCreate(
                            ['question' => $q['question'], 'sort_order' => $i],
                            [
                                'type' => count($q['options']) === 2 ? 'true_false' : 'multiple_choice',
                                'points' => $q['points'],
                                'sort_order' => $i,
                            ]
                        );

                    foreach ($q['options'] as $opt) {
                        $question->options()->firstOrCreate(
                            ['option_text' => $opt[0]],
                            ['is_correct' => $opt[1]]
                        );
                    }
                }
            }
        }
    }
}