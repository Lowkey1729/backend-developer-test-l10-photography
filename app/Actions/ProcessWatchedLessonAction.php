<?php

namespace App\Actions;

use App\Enums\LessonAchievementNameEnum;
use App\Events\AchievementUnlocked;
use App\Events\LessonWatched;
use App\Models\Achievement;
use App\Models\Lesson;
use App\Models\User;

class ProcessWatchedLessonAction
{
    public static function execute(User $user, Lesson $lesson): void
    {
        $user->lessons()->updateExistingPivot($lesson->id, ['watched' => true]);

        event(new LessonWatched($lesson, $user));
    }
}
