<?php

namespace App\Console\Commands;

use App\Actions\Lessons\CommentOnLessonAction;
use App\Actions\Lessons\ProcessWatchedLessonAction;
use App\Enums\AchievementCategoryEnum;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

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
        $category = $this->ask('Which category do you want to simulate for?');
        $count = $this->ask('How many do you want to generate for '. $category);

        $validator = Validator::make([
            'category' => $category,
            'count' => $count,
        ], [
            'category' => ['required', 'in:lessons,comments'],
            'count' => ['required', 'integer'],
        ]);

        if ($validator->fails()) {
            $this->info('App not simulated. See error messages below');

            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return 1;
        }

        $user = User::query()->first();

        if ($category === AchievementCategoryEnum::LESSONS->value){

            $lessons = Lesson::query()->take($count)->get();

            foreach ($lessons as $lesson) {
                ProcessWatchedLessonAction::execute($user, $lesson);
            }

        }

        if ($category === AchievementCategoryEnum::COMMENTS->value){
            $lesson = Lesson::query()->first();

            for ($i = 0; $i <= $count; $i++) {
                CommentOnLessonAction::execute(fake()->sentence, $user, $lesson);
            }

        }

        return  0;


    }

}
