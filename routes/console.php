<?php

use Illuminate\Support\Facades\Schedule;

// Live class reminders: check every minute for classes starting in ~24h / ~15min.
Schedule::command('lms:live-class-reminders')->everyMinute();

// Auto-sync role permissions hourly to keep role_has_permissions populated
Schedule::command('permissions:sync-roles')->hourly();
