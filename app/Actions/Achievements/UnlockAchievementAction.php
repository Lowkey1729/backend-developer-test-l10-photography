<?php

namespace App\Actions\Achievements;

use App\Actions\Badges\UnlockBadgeAction;
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

        $achievementExists = $user->achievements()->where('achievement_id', $lessonAchievement->id)->exists();

        if (! $achievementExists) {
            $user->achievements()->attach($lessonAchievement->id, ['order' => $lessonAchievement->order]);
        }

        event(new AchievementUnlocked($achievementName, $user));

        UnlockBadgeAction::execute($user);

    }
}
