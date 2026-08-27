<?php

namespace Database\Seeders;

use App\Models\ContactMessage;
use Illuminate\Database\Seeder;

class ContactMessagesSeeder extends Seeder
{
    public function run(): void
    {
        $messages = [
            [
                'name' => 'Hassan Raza',
                'email' => 'hassan.raza@example.com',
                'subject' => 'Question about the web development bootcamp',
                'message' => "Hi, I'm a complete beginner and I wanted to ask whether the Complete Web Development Bootcamp is suitable for someone with zero programming experience. Also, do you offer any discounts for students?",
                'is_read' => false,
            ],
            [
                'name' => 'Mariam Al-Farsi',
                'email' => 'mariam.alfarsi@example.com',
                'subject' => 'Certificate verification',
                'message' => "I completed the JavaScript course and received a certificate, but the verification link on my resume seems to be missing the last character. Could you please confirm my certificate is still valid?",
                'is_read' => false,
            ],
            [
                'name' => 'Tom Baker',
                'email' => 'tom.baker@example.com',
                'subject' => 'Refund request for duplicate payment',
                'message' => "I was charged twice for the iOS course (receipts RCP-984721 and RCP-984722). Can you help me get a refund for the duplicate transaction? My credit card was charged twice on the same day.",
                'is_read' => true,
            ],
            [
                'name' => 'Aisha Khan',
                'email' => 'aisha.khan@example.com',
                'subject' => 'Bug while submitting assignment',
                'message' => "I tried to submit my final project assignment for the Laravel course but the upload fails with a 'file too large' error even though my PDF is only 4 MB. I already contacted support but haven't heard back yet.",
                'is_read' => false,
            ],
            [
                'name' => 'Daniel Costa',
                'email' => 'daniel.costa@example.com',
                'subject' => 'Can I change my account email address?',
                'message' => 'I enrolled a few months ago using an old email address. Is there any way to update the email on my account without losing my certificates and learning progress?',
                'is_read' => true,
            ],
            [
                'name' => 'Layla Haddad',
                'email' => 'layla.haddad@example.com',
                'subject' => 'Bulk plan for our team of 20 developers',
                'message' => "We'd like to onboard 20 developers on your cloud and data science tracks for a year. Do you have team or corporate pricing? Also, can you provide a sample invoice for our procurement team?",
                'is_read' => false,
            ],
        ];

        foreach ($messages as $i => $data) {
            ContactMessage::updateOrCreate(
                ['email' => $data['email'], 'subject' => $data['subject']],
                $data + ['created_at' => now()->subDays($i * 2)->subHours(rand(1, 12))]
            );
        }
    }
}