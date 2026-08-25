<?php

namespace Database\Seeders;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LearningSeeder extends Seeder
{
    public function run(): void
    {
        $instructor = User::where('email', 'instructor@lmsportal.com')->first();
        $student = User::where('email', 'student@lmsportal.com')->first();

        if (!$instructor || !$student) {
            return;
        }

        $instructor->update(['bio' => 'Dr. Sarah Johnson has over 12 years of experience in software engineering and teaching. She has worked at Google and Amazon, holds a PhD in Computer Science from Stanford University, and has published over 30 research papers. Her courses have helped more than 100,000 students worldwide launch their tech careers.']);

        // Database-driven categories & levels
        $categoryIds = [];
        $levelIds = [];
        foreach ([
            'Web Development', 'Data Science', 'Artificial Intelligence', 'Mobile Development',
            'Cloud Computing', 'Cyber Security', 'DevOps', 'Project Management',
            'Software Development', 'Digital Marketing', 'Business', 'Design',
            'Programming', 'Databases',
        ] as $i => $name) {
            $cat = \App\Models\CourseCategory::withTrashed()->updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'sort_order' => $i, 'is_active' => true]
            );
            $categoryIds[$name] = $cat->id;
        }
        foreach (['Beginner', 'Intermediate', 'Advanced', 'Beginner to Advanced'] as $i => $name) {
            $lvl = \App\Models\CourseLevel::withTrashed()->updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'sort_order' => $i, 'is_active' => true]
            );
            $levelIds[$name] = $lvl->id;
        }

        $reviewers = collect([
            ['Alex Turner', 'alex.turner@example.com'],
            ['Lisa Chen', 'lisa.chen@example.com'],
            ['Marcus Brown', 'marcus.brown@example.com'],
            ['Priya Sharma', 'priya.sharma@example.com'],
        ])->map(fn ($r) => User::firstOrCreate(
            ['email' => $r[1]],
            ['name' => $r[0], 'password' => 'password']
        ));

        $courses = [
            [
                'title' => 'Complete Web Development Bootcamp',
                'category' => 'Web Development',
                'level' => 'Beginner',
                'duration_hours' => 42,
                'price' => 49.99,
                'description' => "Go from zero to a job-ready full-stack web developer. This hands-on bootcamp covers HTML, CSS, JavaScript, PHP & MySQL through real projects you will build yourself.\n\nBy the end of this course you will have built and deployed several complete websites and web applications, plus a portfolio to show employers.",
                'curriculum' => [
                    ['Web Foundations', [['Introduction to the Web', 12], ['How the Internet Works', 15], ['Setting Up Your Code Editor', 10], ['Your First Web Page', 18]]],
                    ['HTML Deep Dive', [['HTML Document Structure', 14], ['Forms & Input Elements', 22], ['Semantic HTML5 Tags', 16], ['Tables & Media', 13], ['Accessibility Basics', 19]]],
                    ['CSS & Responsive Design', [['Selectors & Specificity', 20], ['Box Model & Positioning', 24], ['Flexbox Layout', 28], ['CSS Grid', 26], ['Media Queries & Mobile First', 21]]],
                    ['JavaScript Essentials', [['Variables & Data Types', 18], ['Functions & Events', 25], ['DOM Manipulation', 30], ['Fetch API & JSON', 27]]],
                    ['Backend with PHP & MySQL', [['PHP Syntax & Functions', 26], ['Connecting to MySQL', 24], ['CRUD Operations', 32], ['User Authentication', 35]]],
                    ['Final Project: Full Website', [['Project Planning & Wireframes', 15], ['Building the Frontend', 40], ['Building the Backend', 45], ['Deployment & Going Live', 20]]],
                ],
                'reviews' => [
                    [0, 5, 'Absolutely fantastic bootcamp! The projects are real-world and the explanations are crystal clear. I landed my first junior dev job two months after finishing.'],
                    [1, 5, 'Best web development course I have taken. The PHP & MySQL section finally made backend development click for me.'],
                    [2, 4, 'Very thorough and well paced. I would love even more JavaScript practice exercises, but overall an excellent course.'],
                ],
            ],
            [
                'title' => 'PHP & Laravel for Beginners',
                'category' => 'Programming',
                'level' => 'Beginner',
                'duration_hours' => 30,
                'price' => 39.99,
                'description' => "Learn PHP from scratch and then master Laravel, the world's most popular PHP framework. Build secure, modern web applications using MVC architecture, Eloquent ORM, and Blade templating.\n\nNo prior PHP experience needed - we start from absolute zero.",
                'curriculum' => [
                    ['PHP Fundamentals', [['PHP Syntax & Variables', 14], ['Arrays & Loops', 18], ['Functions in PHP', 16], ['Working with Forms', 20]]],
                    ['Object Oriented PHP', [['Classes & Objects', 22], ['Inheritance & Interfaces', 24], ['Namespaces & Autoloading', 18]]],
                    ['Getting Started with Laravel', [['Composer & Installation', 12], ['Routing & Controllers', 25], ['Blade Templating', 22], ['Migrations & Seeding', 26]]],
                    ['Eloquent ORM', [['Models & Relationships', 30], ['Query Builder Essentials', 24], ['Validation & Security', 28]]],
                    ['Building a Complete App', [['Authentication Scaffolding', 25], ['CRUD with Laravel', 35], ['Testing & Deployment', 30]]],
                ],
                'reviews' => [
                    [3, 5, 'Laravel finally makes sense! The Eloquent section alone is worth the price. Highly recommended for anyone starting with PHP.'],
                    [0, 4, 'Great progression from plain PHP to the framework. Some sections could be slightly longer but the content quality is top notch.'],
                ],
            ],
            [
                'title' => 'JavaScript ES6+ Mastery',
                'category' => 'Programming',
                'level' => 'Intermediate',
                'duration_hours' => 24,
                'price' => 29.99,
                'description' => "Master modern JavaScript from ES6 onwards. Arrow functions, destructuring, modules, promises, async/await, the DOM, and more - explained deeply with dozens of coding challenges.\n\nPerfect for developers who know the basics and want to write clean, modern JavaScript.",
                'curriculum' => [
                    ['Modern Syntax', [['let, const & Block Scope', 15], ['Arrow Functions', 18], ['Template Literals & Destructuring', 22], ['Spread & Rest Operators', 20]]],
                    ['Advanced Functions', [['Closures Explained', 25], ['Higher Order Functions', 22], ['this & call/apply/bind', 28]]],
                    ['Asynchronous JavaScript', [['Promises from Scratch', 30], ['async/await Patterns', 26], ['Fetch & Error Handling', 24]]],
                    ['Modules & Tooling', [['ES Modules', 18], ['NPM & Bundlers Overview', 22], ['Project: Weather App', 40]]],
                ],
                'reviews' => [
                    [1, 5, 'The closures and async sections are worth their weight in gold. Everything is explained with visual examples that actually stick.'],
                    [2, 4, 'Solid intermediate JS course. The weather app project ties all concepts together nicely.'],
                    [3, 4, 'Great deep dive into modern JavaScript features. I use destructuring and spread daily now at work.'],
                ],
            ],
            [
                'title' => 'MySQL Database Design & Administration',
                'category' => 'Databases',
                'level' => 'Intermediate',
                'duration_hours' => 18,
                'price' => 34.99,
                'description' => "Learn to design, build and administer production-grade MySQL databases. Normalization, indexing, transactions, backups, performance tuning and security - everything a backend developer or DBA needs.\n\nIncludes a complete real-world e-commerce database project.",
                'curriculum' => [
                    ['Relational Database Basics', [['What is a Relational Database', 12], ['Installing MySQL', 15], ['Creating Databases & Tables', 20], ['Data Types Deep Dive', 22]]],
                    ['SQL Querying', [['SELECT, WHERE & ORDER BY', 24], ['JOINs Explained Visually', 30], ['GROUP BY & Aggregates', 26], ['Subqueries & CTEs', 28]]],
                    ['Database Design', [['Normalization 1NF to 3NF', 30], ['Primary & Foreign Keys', 20], ['ER Diagrams & Modeling', 25]]],
                    ['Administration & Performance', [['Indexing Strategies', 28], ['Transactions & Locking', 26], ['Backup & Recovery', 22], ['Users & Permissions', 18]]],
                ],
                'reviews' => [
                    [2, 5, 'The JOINs visualization is the best I have ever seen. Database design finally feels intuitive instead of scary.'],
                    [0, 4, 'Very practical DBA content. The backup/recovery module saved me at work just weeks after finishing the course.'],
                ],
            ],
            [
                'title' => 'UI/UX Design Fundamentals with Figma',
                'category' => 'Design',
                'level' => 'Beginner',
                'duration_hours' => 20,
                'price' => 44.99,
                'description' => "Learn user interface and user experience design from scratch using Figma. Design principles, color theory, typography, wireframing, prototyping and building a complete design system.\n\nFinish the course with a polished portfolio case study.",
                'curriculum' => [
                    ['Design Thinking Basics', [['What is UI vs UX', 12], ['The Design Process', 18], ['User Research Fundamentals', 22]]],
                    ['Figma Essentials', [['Interface Tour', 15], ['Frames, Shapes & Tools', 24], ['Auto Layout Mastery', 30], ['Components & Variants', 28]]],
                    ['Visual Design Principles', [['Color Theory for Interfaces', 25], ['Typography Systems', 22], ['Spacing & Grids', 20]]],
                    ['Wireframing to Prototype', [['Low-Fi Wireframes', 24], ['Interactive Prototyping', 28], ['Usability Testing', 22]]],
                    ['Portfolio Case Study', [['Project: Mobile App Redesign', 45], ['Presenting Your Work', 20]]],
                ],
                'reviews' => [
                    [3, 5, 'I went from zero design skills to a portfolio case study in three weeks. The Auto Layout lessons are outstanding.'],
                    [1, 5, 'As a developer wanting to learn design, this was perfect. Practical, visual and immediately applicable.'],
                    [2, 4, 'Great fundamentals course. Would love an advanced follow-up covering design systems at scale.'],
                ],
            ],
        ];

        $progressMap = [100, 100, 65, 30, 0];

        foreach ($courses as $index => $data) {
            $curriculum = $data['curriculum'];
            $courseReviews = $data['reviews'];
            $categoryId = $categoryIds[$data['category']] ?? null;
            $levelId = $levelIds[$data['level']] ?? null;
            unset($data['curriculum'], $data['reviews'], $data['category'], $data['level']);

            $course = Course::firstOrCreate(
                ['slug' => Str::slug($data['title'])],
                $data
                    + [
                        'course_category_id' => $categoryId,
                        'course_level_id' => $levelId,
                        'instructor_id' => $instructor->id,
                    ]
            );

            foreach ($curriculum as $mi => [$moduleTitle, $lessons]) {
                $module = CourseModule::firstOrCreate(
                    ['course_id' => $course->id, 'title' => $moduleTitle],
                    ['sort_order' => $mi]
                );

                foreach ($lessons as $li => [$lessonTitle, $minutes]) {
                    Lesson::firstOrCreate(
                        ['course_module_id' => $module->id, 'title' => $lessonTitle],
                        ['duration_minutes' => $minutes, 'sort_order' => $li]
                    );
                }
            }

            foreach ($courseReviews as [$reviewerIdx, $rating, $comment]) {
                Review::firstOrCreate(
                    ['course_id' => $course->id, 'user_id' => $reviewers[$reviewerIdx]->id],
                    ['rating' => $rating, 'comment' => $comment]
                );
            }

            $enrollment = Enrollment::firstOrCreate(
                [
                    'user_id' => $student->id,
                    'course_id' => $course->id,
                ],
                [
                    'progress' => $progressMap[$index],
                    'completed_at' => $progressMap[$index] >= 100 ? now()->subDays(rand(5, 40)) : null,
                ]
            );

            if ($enrollment->isCompleted()) {
                Certificate::firstOrCreate(
                    [
                        'user_id' => $student->id,
                        'course_id' => $course->id,
                    ],
                    [
                        'code' => 'LMS-' . strtoupper(Str::random(10)),
                        'issued_at' => $enrollment->completed_at ?? now(),
                    ]
                );
            }
        }
    }
}
