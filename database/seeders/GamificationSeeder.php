<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class GamificationSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            ['name' => 'First Steps', 'slug' => 'first-steps', 'description' => 'Earn your first XP points', 'icon' => 'star', 'color' => '#f59e0b', 'xp_reward' => 10, 'category' => 'milestones', 'sort_order' => 1],
            ['name' => 'Lesson Lover', 'slug' => 'lesson-lover', 'description' => 'Complete 5 lessons', 'icon' => 'book-open', 'color' => '#6366f1', 'xp_reward' => 25, 'category' => 'learning', 'sort_order' => 2],
            ['name' => 'Knowledge Seeker', 'slug' => 'knowledge-seeker', 'description' => 'Complete 25 lessons', 'icon' => 'academic-cap', 'color' => '#8b5cf6', 'xp_reward' => 50, 'category' => 'learning', 'sort_order' => 3],
            ['name' => 'Lesson Master', 'slug' => 'lesson-master', 'description' => 'Complete 50 lessons', 'icon' => 'check-badge', 'color' => '#a855f7', 'xp_reward' => 100, 'category' => 'learning', 'sort_order' => 4],
            ['name' => 'Quiz Champion', 'slug' => 'quiz-champion', 'description' => 'Pass your first quiz', 'icon' => 'trophy', 'color' => '#10b981', 'xp_reward' => 20, 'category' => 'quizzes', 'sort_order' => 5],
            ['name' => 'Quiz Master', 'slug' => 'quiz-master', 'description' => 'Pass 10 quizzes', 'icon' => 'fire', 'color' => '#f97316', 'xp_reward' => 75, 'category' => 'quizzes', 'sort_order' => 6],
            ['name' => 'Perfect Score', 'slug' => 'perfect-score', 'description' => 'Score 100% on any quiz', 'icon' => 'sparkles', 'color' => '#eab308', 'xp_reward' => 50, 'category' => 'quizzes', 'sort_order' => 7],
            ['name' => 'Course Graduate', 'slug' => 'course-graduate', 'description' => 'Complete your first course', 'icon' => 'graduation-cap', 'color' => '#06b6d4', 'xp_reward' => 50, 'category' => 'courses', 'sort_order' => 8],
            ['name' => 'Course Collector', 'slug' => 'course-collector', 'description' => 'Complete 5 courses', 'icon' => 'collection', 'color' => '#0891b2', 'xp_reward' => 150, 'category' => 'courses', 'sort_order' => 9],
            ['name' => 'Streak Warrior', 'slug' => 'streak-warrior', 'description' => 'Maintain a 7-day learning streak', 'icon' => 'bolt', 'color' => '#ef4444', 'xp_reward' => 50, 'category' => 'streaks', 'sort_order' => 10],
            ['name' => 'Streak Legend', 'slug' => 'streak-legend', 'description' => 'Maintain a 30-day learning streak', 'icon' => 'flame', 'color' => '#dc2626', 'xp_reward' => 200, 'category' => 'streaks', 'sort_order' => 11],
            ['name' => 'Curious Mind', 'slug' => 'curious-mind', 'description' => 'Post 5 questions or replies in discussions', 'icon' => 'chat-bubble-left-ellipsis', 'color' => '#2563eb', 'xp_reward' => 30, 'category' => 'community', 'sort_order' => 12],
            ['name' => 'Helpful Voice', 'slug' => 'helpful-voice', 'description' => 'Post 10 questions in discussions', 'icon' => 'megaphone', 'color' => '#3b82f6', 'xp_reward' => 50, 'category' => 'community', 'sort_order' => 13],
            ['name' => 'Rising Star', 'slug' => 'rising-star', 'description' => 'Earn 500 total XP', 'icon' => 'arrow-trending-up', 'color' => '#d946ef', 'xp_reward' => 25, 'category' => 'milestones', 'sort_order' => 14],
            ['name' => 'XP Legend', 'slug' => 'xp-legend', 'description' => 'Earn 5000 total XP', 'icon' => 'crown', 'color' => '#f43f5e', 'xp_reward' => 100, 'category' => 'milestones', 'sort_order' => 15],
        ];

        foreach ($badges as $badge) {
            Badge::updateOrCreate(['slug' => $badge['slug']], $badge);
        }
    }
}
