<?php

namespace App\Console\Commands;

use App\Actions\Lessons\CommentOnLessonAction;
use App\Actions\Lessons\ProcessWatchedLessonAction;
use App\Enums\AchievementCategoryEnum;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Console\Command;

use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class SimulateApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:simulate-app';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command simulates data for lessons watched and comments written';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $category = select(
            label: 'Which category do you want to simulate for?',
            options: [
                AchievementCategoryEnum::COMMENTS->value,
                AchievementCategoryEnum::LESSONS->value,
            ],
            required: true,
        );

        $text = match ($category) {
            AchievementCategoryEnum::COMMENTS->value => 'How many written comments do you want to generate?',
            AchievementCategoryEnum::LESSONS->value => 'How many watched lessons do you want to generate'
        };

        $count = text(
            label: $text,
            required: true,
            validate: function (int $value) {
            }
        );

        $user = User::query()->first();

        if ($category === AchievementCategoryEnum::LESSONS->value) {

            $lessons = Lesson::query()->take($count)->get();

            foreach ($lessons as $lesson) {
                ProcessWatchedLessonAction::execute($user, $lesson);
            }

        }

        if ($category === AchievementCategoryEnum::COMMENTS->value) {
            $lesson = Lesson::query()->first();

            for ($i = 0; $i <= $count; $i++) {
                CommentOnLessonAction::execute(fake()->sentence, $user, $lesson);
            }

        }

        return 0;

    }
}
