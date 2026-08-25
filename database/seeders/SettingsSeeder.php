<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        Setting::set([
            'site_name' => 'LMS Portal',
            'site_tagline' => 'Learn. Grow. Succeed.',
            'about_title' => 'About Us',
            'about_description' => 'We are a modern learning platform on a mission to make world-class education accessible to everyone. Learn from industry experts, build real projects, earn certificates and grow your career at your own pace.',
            'footer_description' => 'Empowering millions of learners worldwide with industry-relevant courses and certifications from expert instructors.',
            'contact_email' => 'info@lmsportal.com',
            'support_email' => 'support@lmsportal.com',
            'contact_phone' => '+1 (800) 123-4567',
            'contact_address' => "123 Learning Avenue, Suite 400\nSan Francisco, CA 94105",
            'office_hours' => 'Mon – Fri, 9:00 AM – 6:00 PM',
            'facebook_url' => 'https://facebook.com/lmsportal',
            'twitter_url' => 'https://x.com/lmsportal',
            'instagram_url' => 'https://instagram.com/lmsportal',
            'linkedin_url' => 'https://linkedin.com/company/lmsportal',
            'youtube_url' => 'https://youtube.com/@lmsportal',
            'meta_title' => 'LMS Portal - Online Courses & Certifications',
            'meta_description' => 'Join LMS Portal to learn in-demand skills from expert instructors. Build real projects, earn certificates and advance your career.',
            'meta_keywords' => 'online courses, e-learning, certifications, programming, data science, cloud',
            'copyright_text' => 'All rights reserved.',
            'maintenance_mode' => '0',
            'maintenance_message' => "We're performing scheduled maintenance. Some features may be unavailable.",
        ]);
    }
}
