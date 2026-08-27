<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Quiz;
use Illuminate\Database\Seeder;

class AssignmentsSeeder extends Seeder
{
    public function run(): void
    {
        $assignmentData = [
            // index 0
            ['title' => 'Portfolio Website Project', 'description' => 'Build and deploy a complete multi-page portfolio website using the HTML, CSS, JavaScript, PHP and MySQL skills from this course. Submit a PDF documenting your approach plus the live URL.', 'due' => -6],
            // index 1
            ['title' => 'Laravel CRUD Web App', 'description' => 'Build a small task manager as a Laravel application with authentication, Eloquent models, validation and Blade views. Zip and submit your project along with setup instructions.', 'due' => -3],
            // index 2
            ['title' => 'Weather App with ES6 Modules', 'description' => 'Refactor the course weather app to use ES modules and async/await, add error handling and persist city preferences in localStorage. Submit your code as a single zip file.', 'due' => -1],
            // index 3
            ['title' => 'E-Commerce Database Design', 'description' => "Design a normalized e-commerce schema (1NF–3NF), write the DDL, populate sample data and include five meaningful queries with EXPLAIN plans. Submit a single PDF, SQL or DOC file.", 'due' => -4],
            // index 4
            ['title' => 'Mobile App Redesign Case Study', 'description' => 'Produce a Figma redesign of the assigned app including wireframes, hi-fi screens, a design system and a clickable prototype. Export your case study as a PDF.', 'due' => 7],
            // index 5
            ['title' => 'Data Analysis Capstone Report', 'description' => "Analyze a provided real-world dataset with Pandas, produce at least five visualizations and write a short report summarizing your findings and recommendations. Submit as PDF.", 'due' => 5],
            // index 6
            ['title' => 'Serverless App Cloud Deployment', 'description' => 'Deploy the course capstone to AWS using Lambda, API Gateway and S3, then document the architecture with a diagram and cost estimate. Submit as PDF plus the live endpoint.', 'due' => 9],
            // index 7
            ['title' => 'Task Manager App Submission', 'description' => 'Finalize and package your Flutter task manager app for both iOS and Android, including screenshots of each screen. Compress and submit the project folder.', 'due' => 12],
        ];

        foreach (Course::orderBy('id')->get() as $i => $course) {
            $data = $assignmentData[$i] ?? $assignmentData[array_rand($assignmentData)];

            Quiz::firstOrCreate(
                ['course_id' => $course->id, 'title' => $data['title']],
                [
                    'type' => 'assignment',
                    'description' => $data['description'],
                    'passing_score' => 70,
                    'duration_minutes' => null,
                    'due_date' => now()->addDays($data['due']),
                    'max_file_size_mb' => 10,
                    'allowed_extensions' => 'pdf,doc,docx,png,jpg,jpeg,zip',
                    'shuffle_questions' => false,
                    'shuffle_options' => true,
                    'is_active' => true,
                ]
            );
        }
    }
}