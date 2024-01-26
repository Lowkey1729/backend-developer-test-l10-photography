<?php

namespace App\Listeners;

use App\Enums\LessonAchievementNameEnum;
use App\Events\AchievementUnlocked;
use App\Events\LessonWatched;
use App\Models\Achievement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProcessLessonWatchedAchievements
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(LessonWatched $event): void
    {
        $user = $event->user;

        $watchedLessonsCount = $user->watched()->count();

        match ($watchedLessonsCount) {
            1 => $this->resolveAchievement(LessonAchievementNameEnum::FIRST_LESSON_WATCHED->name, $user),
            5 => $this->resolveAchievement(LessonAchievementNameEnum::FIVE_LESSONS_WATCHED->name, $user),
            10 => $this->resolveAchievement(LessonAchievementNameEnum::TEN_LESSONS_WATCHED->name, $user),
            25 => $this->resolveAchievement(LessonAchievementNameEnum::TWENTY_FIVE_LESSONS_WATCHED->name, $user),
            50 => $this->resolveAchievement(LessonAchievementNameEnum::FIFTY_LESSONS_WATCHED->name, $user),
            default => null
        };

    }

    protected function resolveAchievement(string $lessonAchievementName, User $user): void
    {

        $lessonAchievement = Achievement::query()
            ->where('name', $lessonAchievementName)
            ->first();

        $user->achievements()->attach($lessonAchievement->id);

        event(new AchievementUnlocked($lessonAchievementName, $user));
    }
}
