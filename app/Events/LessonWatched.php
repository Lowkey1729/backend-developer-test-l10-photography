<?php

namespace App\Events;

use App\Models\User;
use App\Models\Lesson;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class LessonWatched
{
    use Dispatchable, SerializesModels;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        public Lesson $lesson,
        public User $user
    )
    {
    }
}
