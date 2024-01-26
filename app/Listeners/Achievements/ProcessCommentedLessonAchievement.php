<?php

namespace App\Listeners\Achievements;

use App\Enums\CommentAchievementNameEnum;
use App\Events\CommentWritten;
use App\Listeners\Achievements\Concerns\HandleAchievementTrait;
use App\Models\Comment;

class ProcessCommentedLessonAchievement
{
    use HandleAchievementTrait;
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
    public function handle(CommentWritten $event): void
    {
        $comment = $event->comment;

        $commentsCount = Comment::query()
            ->where('user_id', $comment->user_id)
            ->where('lesson_id', $comment->lesson_id)
            ->count();

        match ($commentsCount){
            1 => $this->resolveAchievement(CommentAchievementNameEnum::FIRST_COMMENT_WRITTEN->name, $comment->user),
            3 => $this->resolveAchievement(CommentAchievementNameEnum::THREE_LESSONS_WRITTEN->name, $comment->user),
            5 => $this->resolveAchievement(CommentAchievementNameEnum::FIVE_LESSONS_WRITTEN->name, $comment->user),
            10 => $this->resolveAchievement(CommentAchievementNameEnum::TEN_LESSONS_WRITTEN->name, $comment->user),
            20 => $this->resolveAchievement(CommentAchievementNameEnum::TWENTY_LESSONS_WRITTEN->name, $comment->user),
            default => null
        };


    }

}
