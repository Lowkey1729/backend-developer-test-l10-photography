<?php

namespace App\Enums;

enum LessonAchievementNameEnum: string
{
    case FIRST_LESSON_WATCHED = 'First Lesson Watched';

    case FIVE_LESSONS_WATCHED = '5 Lessons Watched';

    case TEN_LESSONS_WATCHED = '10 Lessons Watched';

    case TWENTY_FIVE_LESSONS_WATCHED = '25 Lessons Watched';

    case FIFTY_LESSONS_WATCHED = '50 Lessons Watched';

    public static function fromName(string $name)
    {
        $enumClass = 'App\Enums\LessonAchievementNameEnum';
        $constName = $enumClass.'::'.$name;

        return constant($constName);

    }
}
