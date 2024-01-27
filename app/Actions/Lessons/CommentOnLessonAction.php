<?php

namespace App\Actions\Lessons;

use App\Events\CommentWritten;
use App\Models\Comment;
use App\Models\Lesson;
use App\Models\User;

class CommentOnLessonAction
{
    public static function execute(string $body, User $user, Lesson $lesson)
    {
        $comment = Comment::query()
            ->create([
                'body' => $body,
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
            ]);

        event(new CommentWritten($comment));

        return $comment;
    }
}
