<?php

namespace App\Actions;

use App\Events\AchievementUnlocked;
use App\Models\Achievement;
use App\Models\User;

class UnlockAchievementAction
{
    public static function execute(string $achievementName, User $user): void
    {
        $lessonAchievement = Achievement::query()
            ->where('name', $achievementName)
            ->first();

        $user->achievements()->attach($lessonAchievement->id);

        event(new AchievementUnlocked($achievementName, $user));

        UnlockBadgeAction::execute($user);

    }
}
