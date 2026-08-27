<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\Seeder;

class WishlistSeeder extends Seeder
{
    private const PLANS = [
        'student@lmsportal.com' => ['uiux-design-fundamentals-with-figma'],
        'emma.wilson@example.com' => ['cloud-computing-with-aws'],
        'david.kim@example.com' => ['uiux-design-fundamentals-with-figma'],
        'sofia.garcia@example.com' => ['php-laravel-for-beginners'],
        'james.patel@example.com' => ['cloud-computing-with-aws'],
    ];

    public function run(): void
    {
        foreach (self::PLANS as $email => $slugs) {
            $user = User::where('email', $email)->first();
            if (!$user) {
                continue;
            }

            foreach ($slugs as $slug) {
                $course = Course::where('slug', $slug)->first();
                if (!$course) {
                    continue;
                }

                Wishlist::firstOrCreate(['user_id' => $user->id, 'course_id' => $course->id]);
            }
        }
    }
}