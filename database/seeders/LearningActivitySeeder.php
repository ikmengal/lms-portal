<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Discussion;
use App\Models\DiscussionUpvote;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonNote;
use App\Models\LessonProgress;
use App\Models\LessonResource;
use App\Models\User;
use App\Models\VideoProgress;
use Illuminate\Database\Seeder;

class LearningActivitySeeder extends Seeder
{
    public function run(): void
    {
        $this->seedLessonProgress();
        $this->seedVideoProgress();
        $this->seedNotes();
        $this->seedResources();
        $this->seedDiscussions();
    }

    private function seedLessonProgress(): void
    {
        $enrollments = Enrollment::with(['user', 'course.modules.lessons'])->get();

        foreach ($enrollments as $enrollment) {
            $lessons = $enrollment->course->modules
                ->flatMap->lessons
                ->sortBy(fn ($l) => [$l->module->sort_order, $l->sort_order])
                ->values();

            $completedCount = (int) floor($lessons->count() * $enrollment->progress / 100);
            if ($completedCount === 0) {
                continue;
            }

            $isDone = $enrollment->isCompleted();
            $firstCompletedAt = $isDone
                ? $enrollment->completed_at->subDays(rand(15, 30))
                : now()->subDays(rand(10, 25));

            foreach ($lessons->take($completedCount) as $i => $lesson) {
                $completedAt = $isDone
                    ? $enrollment->completed_at->subDays($completedCount - 1 - $i)
                    : $firstCompletedAt->addHours(($i + 1) * rand(18, 40));

                if ($completedAt->gt(now())) {
                    $completedAt = now()->subHours(rand(2, 20));
                }

                LessonProgress::firstOrCreate(
                    ['user_id' => $enrollment->user_id, 'lesson_id' => $lesson->id],
                    ['completed_at' => $completedAt]
                );
            }
        }
    }

    private function seedVideoProgress(): void
    {
        $enrollments = Enrollment::with(['user', 'course.modules.lessons'])->get();

        foreach ($enrollments as $enrollment) {
            $progress = $enrollment->progress;
            if ($progress <= 0) {
                continue;
            }

            $lessons = $enrollment->course->modules
                ->flatMap->lessons
                ->sortBy(fn ($l) => [$l->module->sort_order, $l->sort_order])
                ->values();

            $completedCount = (int) floor($lessons->count() * $progress / 100);

            // Full watch on the last completed lesson.
            if ($completedCount > 0) {
                $lastDone = $lessons->get($completedCount - 1);
                VideoProgress::updateOrCreate(
                    ['user_id' => $enrollment->user_id, 'lesson_id' => $lastDone->id],
                    [
                        'watched_seconds' => $lastDone->duration_minutes * 60,
                        'percentage' => 100,
                        'last_position_at' => now()->subHours(rand(1, 30)),
                    ]
                );
            }

            // Partial watch on the next lesson, if any.
            if ($nextLesson = $lessons->get($completedCount)) {
                $percentage = min(80, max(5, ($progress % 10 ?: 4) * 10));
                VideoProgress::updateOrCreate(
                    ['user_id' => $enrollment->user_id, 'lesson_id' => $nextLesson->id],
                    [
                        'watched_seconds' => (int) floor($nextLesson->duration_minutes * 60 * $percentage / 100),
                        'percentage' => $percentage,
                        'last_position_at' => now()->subMinutes(rand(5, 240)),
                    ]
                );
            }
        }
    }

    private function seedNotes(): void
    {
        $notes = $this->noteBlueprints();

        foreach ($notes as $note) {
            $user = User::where('email', $note['user'])->first();
            $lesson = Lesson::where('title', $note['lesson'])->first();
            if (!$user || !$lesson) {
                continue;
            }

            LessonNote::updateOrCreate(
                ['user_id' => $user->id, 'lesson_id' => $lesson->id, 'content' => $note['content']],
                ['content' => $note['content']]
            );
        }
    }

    private function noteBlueprints(): array
    {
        return [
            [
                'user' => 'student@lmsportal.com',
                'lesson' => 'Flexbox Layout',
                'content' => "Flexbox tips:\n- justify-content aligns on main axis\n- align-items aligns on cross axis\n- align-self overrides for one child\n\nRemember: container vs item properties!",
            ],
            [
                'user' => 'student@lmsportal.com',
                'lesson' => 'Eloquent Relationships',
                'content' => "hasMany vs belongsTo:\n- hasMany is on the parent\n- belongsTo is on the child (has the foreign key)\n\nUse ->with() to eager-load and avoid N+1 queries.",
            ],
            [
                'user' => 'emma.wilson@example.com',
                'lesson' => 'Series & DataFrames',
                'content' => "Pandas cheat sheet: df.head(), df.info(), df.describe(), df.isna().sum(). Group by -> agg(['mean','count']).",
            ],
            [
                'user' => 'david.kim@example.com',
                'lesson' => 'async/await Patterns',
                'content' => 'Async order: fetch -> await -> .json() -> render. Always wrap fetch in try/catch for network errors.',
            ],
        ];
    }

    private function seedResources(): void
    {
        $resources = [
            ['Introduction to the Web', 'Course Syllabus & Roadmap', 'https://lmsportal.com/resources/web-dev-syllabus.pdf'],
            ['Introduction to the Web', 'Recommended Code Editor Setup', 'https://code.visualstudio.com/docs/setup/setup-overview'],
            ['PHP Syntax & Variables', 'PHP Official Documentation', 'https://www.php.net/manual/en/langref.php'],
            ['Pandas & DataFrames', 'Pandas User Guide', 'https://pandas.pydata.org/docs/user_guide/index.html'],
            ['S3 Essentials', 'AWS S3 Documentation', 'https://docs.aws.amazon.com/s3/'],
            ['Layouts with Rows & Columns', 'Flutter Widget of the Week', 'https://www.youtube.com/playlist?list=PLjxrf2q8roU0o4ZVtx1RCES6uYpYBtPXx'],
        ];

        foreach ($resources as [$lessonTitle, $title, $url]) {
            $lesson = Lesson::where('title', $lessonTitle)->first();
            if (!$lesson) {
                continue;
            }

            LessonResource::updateOrCreate(
                ['lesson_id' => $lesson->id, 'title' => $title],
                ['external_url' => $url]
            );
        }
    }

    private function seedDiscussions(): void
    {
        $instructor = User::where('email', 'instructor@lmsportal.com')->first();
        $blueprint = $this->discussionBlueprints();

        foreach ($blueprint as $courseSlug => $threads) {
            $course = Course::where('slug', $courseSlug)->first();
            if (!$course) {
                continue;
            }

            $firstLesson = optional($course->modules->first())?->lessons->first();

            foreach ($threads as $thread) {
                $user = User::where('email', $thread['user'])->first();
                if (!$user) {
                    continue;
                }

                $question = Discussion::updateOrCreate(
                    ['course_id' => $course->id, 'lesson_id' => $firstLesson?->id, 'body' => $thread['question'], 'user_id' => $user->id],
                    ['is_answered' => $thread['answered'], 'answered_by' => $thread['answered'] ? $instructor?->id : null]
                );

                if (!empty($thread['reply'])) {
                    Discussion::updateOrCreate(
                        ['course_id' => $course->id, 'parent_id' => $question->id, 'user_id' => $instructor?->id],
                        ['lesson_id' => $firstLesson?->id, 'body' => $thread['reply'], 'is_answered' => true, 'answered_by' => $instructor?->id]
                    );
                }

                foreach ($thread['upvoters'] ?? [] as $voterEmail) {
                    $voter = User::where('email', $voterEmail)->first();
                    if (!$voter) {
                        continue;
                    }
                    DiscussionUpvote::firstOrCreate(
                        ['discussion_id' => $question->id, 'user_id' => $voter->id]
                    );
                }
            }
        }
    }

    private function discussionBlueprints(): array
    {
        return [
            'complete-web-development-bootcamp' => [
                [
                    'user' => 'student@lmsportal.com',
                    'question' => 'When building the final project, how should I structure the folder for separating frontend and backend code?',
                    'reply' => 'A clean approach is to keep PHP files under an app/ folder, static assets under public/, and your SQL export under a database/ folder. Always keep sensitive config outside the web root. In the course we used this exact layout in section 6.',
                    'answered' => true,
                    'upvoters' => ['emma.wilson@example.com', 'alex.turner@example.com'],
                ],
                [
                    'user' => 'david.kim@example.com',
                    'question' => 'Is it fine to skip the accessibility module for now and come back to it later?',
                    'reply' => null,
                    'answered' => false,
                    'upvoters' => ['marcus.brown@example.com'],
                ],
            ],
            'php-laravel-for-beginners' => [
                [
                    'user' => 'emma.wilson@example.com',
                    'question' => 'What is the difference between query builder and Eloquent in a real project?',
                    'reply' => 'Both are valid! Eloquent gives you models, relationships and elegant syntax for most cases. The query builder is faster to write for complex reports and gives you finer control over the generated SQL. In practice we mix them - Eloquent for CRUD, query builder for aggregated reports.',
                    'answered' => true,
                    'upvoters' => ['student@lmsportal.com', 'lisa.chen@example.com'],
                ],
            ],
            'javascript-es6-mastery' => [
                [
                    'user' => 'sofia.garcia@example.com',
                    'question' => 'How do you decide between a closure and a class for a reusable component?',
                    'reply' => null,
                    'answered' => false,
                    'upvoters' => ['david.kim@example.com'],
                ],
            ],
            'python-for-data-science-analytics' => [
                [
                    'user' => 'james.patel@example.com',
                    'question' => "Should I clean missing values by dropping rows or filling them with the mean?",
                    'reply' => 'It depends on the data. If rows are scarce or the missing values are random, dropping is simple. For time series you usually want forward-fill. Mean/median imputation is a good middle ground - the capstone lesson covers when each makes sense.',
                    'answered' => true,
                    'upvoters' => ['sofia.garcia@example.com', 'priya.sharma@example.com'],
                ],
            ],
        ];
    }
}