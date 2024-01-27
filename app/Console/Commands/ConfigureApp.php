<?php

namespace App\Console\Commands;

use App\Enums\BadgeNameEnum;
use App\Enums\BadgeStatusEnum;
use App\Models\Badge;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Console\Command;

class ConfigureApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:configure-app';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $user = User::query()->first();

        $this->assignDefaultBadge($user);

        $this->assignWatchedLessons($user);

    }

    protected function assignWatchedLessons(User $user): void
    {
        $lessons = Lesson::query()
            ->get()
            ->map(function ($lesson) {
                return [
                    $lesson->id => ['watched' => false],
                ];
            })
            ->toArray();

        $lessons = (call_user_func_array('array_replace_recursive', $lessons));

        $user->lessons()->attach($lessons);
    }

    protected function assignDefaultBadge(User $user): void
    {
        $badge = Badge::query()
            ->where('name', BadgeNameEnum::BEGINNER->name)
            ->first();

        $user->badges()->attach($badge->id, ['status' => BadgeStatusEnum::ACTIVE->value, 'order' => $badge->order]);
    }
}
