<?php

namespace App\Enums;

enum CommentAchievementNameEnum: string
{
    case FIRST_COMMENT_WRITTEN = 'First Comment Written';

    case THREE_COMMENTS_WRITTEN = '3 Comments Written';
    case FIVE_COMMENTS_WRITTEN = '5 Comments Written';

    case TEN_COMMENTS_WRITTEN = '10 Comments Written';

    case TWENTY_COMMENTS_WRITTEN = '20 Comments Written';

    public static function fromName(string $name)
    {
        $enumClass = 'App\Enums\CommentAchievementNameEnum';
        $constName = $enumClass.'::'.$name;

        return constant($constName);

    }
}
