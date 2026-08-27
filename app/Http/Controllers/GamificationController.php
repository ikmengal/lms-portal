<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\User;
use App\Models\UserStreak;
use App\Models\XpTransaction;
use App\Services\GamificationService;

class GamificationController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $totalXp = GamificationService::totalXp($user);
        $level = GamificationService::currentLevel($user);
        $xpProgress = GamificationService::xpProgressInLevel($user);
        $xpForNext = GamificationService::xpForNextLevel($user);
        $rank = GamificationService::rank($user);
        $streak = UserStreak::where('user_id', $user->id)->first();
        $badges = $user->badges()->orderByPivot('earned_at', 'desc')->get();
        $recentXp = XpTransaction::where('user_id', $user->id)->latest()->take(10)->get();
        $leaderboard = GamificationService::leaderboard(10);

        return view('pages.student.gamification', compact(
            'totalXp', 'level', 'xpProgress', 'xpForNext', 'rank',
            'streak', 'badges', 'recentXp', 'leaderboard'
        ));
    }

    public function badges()
    {
        $user = auth()->user();
        $allBadges = Badge::where('is_active', true)->orderBy('category')->orderBy('sort_order')->get();
        $earnedBadgeIds = $user->badges()->pluck('badge_id')->toArray();

        return view('pages.student.badges', compact('allBadges', 'earnedBadgeIds'));
    }

    public function leaderboard()
    {
        $leaderboard = GamificationService::leaderboard(50);
        $myRank = GamificationService::rank(auth()->user());

        return view('pages.student.leaderboard', compact('leaderboard', 'myRank'));
    }
}
