<?php

namespace App\Listeners\Achievements;

use App\Actions\Achievements\UnlockAchievementAction;
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

        match ($commentsCount) {
            1 => UnlockAchievementAction::execute(CommentAchievementNameEnum::FIRST_COMMENT_WRITTEN->name, $comment->user),
            3 => UnlockAchievementAction::execute(CommentAchievementNameEnum::THREE_COMMENTS_WRITTEN->name, $comment->user),
            5 => UnlockAchievementAction::execute(CommentAchievementNameEnum::FIVE_COMMENTS_WRITTEN->name, $comment->user),
            10 => UnlockAchievementAction::execute(CommentAchievementNameEnum::TEN_COMMENTS_WRITTEN->name, $comment->user),
            20 => UnlockAchievementAction::execute(CommentAchievementNameEnum::TWENTY_COMMENTS_WRITTEN->name, $comment->user),
            default => null
        };

    }
}
