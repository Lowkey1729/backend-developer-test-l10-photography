<?php

namespace App\Listeners\Achievements;

use App\Actions\Achievements\UnlockAchievementAction;
use App\Enums\LessonAchievementNameEnum;
use App\Events\LessonWatched;

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
            1 => UnlockAchievementAction::execute(LessonAchievementNameEnum::FIRST_LESSON_WATCHED->name, $user),
            5 => UnlockAchievementAction::execute(LessonAchievementNameEnum::FIVE_LESSONS_WATCHED->name, $user),
            10 => UnlockAchievementAction::execute(LessonAchievementNameEnum::TEN_LESSONS_WATCHED->name, $user),
            25 => UnlockAchievementAction::execute(LessonAchievementNameEnum::TWENTY_FIVE_LESSONS_WATCHED->name, $user),
            50 => UnlockAchievementAction::execute(LessonAchievementNameEnum::FIFTY_LESSONS_WATCHED->name, $user),
            default => null
        };

    }
}
