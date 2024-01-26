<?php

namespace App\Listeners\Achievements\Concerns;

use App\Events\AchievementUnlocked;
use App\Models\Achievement;
use App\Models\User;

trait HandleAchievementTrait
{
    protected function resolveAchievement(string $lessonAchievementName, User $user): void
    {

        $lessonAchievement = Achievement::query()
            ->where('name', $lessonAchievementName)
            ->first();

        $user->achievements()->attach($lessonAchievement->id);

        event(new AchievementUnlocked($lessonAchievementName, $user));
    }
}
