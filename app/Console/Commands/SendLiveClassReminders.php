<?php

namespace App\Console\Commands;

use App\Models\LiveClass;
use App\Services\Notifier;
use Illuminate\Console\Command;

class SendLiveClassReminders extends Command
{
    protected $signature = 'lms:live-class-reminders';

    protected $description = 'Send live class reminders (24 hours and 15 minutes before start) to enrolled students';

    public function handle(): int
    {
        $now = now();

        // ~24h window (23h50m → 24h10m ahead)
        LiveClass::with('course')
            ->whereNull('reminder_24h_sent_at')
            ->whereBetween('scheduled_at', [$now->copy()->addMinutes(1430), $now->copy()->addMinutes(1450)])
            ->each(function (LiveClass $class) {
                Notifier::liveClassReminder($class, 'tomorrow');
                $class->forceFill(['reminder_24h_sent_at' => now()])->save();
                $this->info("24h reminder sent: {$class->title}");
            });

        // ~15min window (13m → 17m ahead)
        LiveClass::with('course')
            ->whereNull('reminder_15m_sent_at')
            ->whereBetween('scheduled_at', [$now->copy()->addMinutes(13), $now->copy()->addMinutes(17)])
            ->each(function (LiveClass $class) {
                Notifier::liveClassReminder($class, 'starting in 15 minutes');
                $class->forceFill(['reminder_15m_sent_at' => now()])->save();
                $this->info("15m reminder sent: {$class->title}");
            });

        return self::SUCCESS;
    }
}
