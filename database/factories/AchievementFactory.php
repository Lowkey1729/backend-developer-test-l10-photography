<?php

namespace Database\Factories;

use App\Enums\AchievementCategoryEnum;
use App\Enums\LessonAchievementNameEnum;
use App\Models\Achievement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;

class AchievementFactory extends Factory
{
    protected $model = Achievement::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence,
            'category' => 'lessons',
            'order' => 1
        ];
    }

    public function defaultAchievements(): AchievementFactory
    {
        return $this->state(
            new Sequence(
                [
                    'name' => LessonAchievementNameEnum::FIRST_LESSON_WATCHED->name,
                    'category' => AchievementCategoryEnum::LESSONS->name,
                    'order' => 1
                ],
                [
                    'name' => LessonAchievementNameEnum::FIVE_LESSONS_WATCHED->name,
                    'category' => AchievementCategoryEnum::LESSONS->name,
                    'order' => 2
                ],
                [
                    'name' => LessonAchievementNameEnum::TEN_LESSONS_WATCHED->name,
                    'category' => AchievementCategoryEnum::LESSONS->name,
                    'order' => 3
                ],
                [
                    'name' => LessonAchievementNameEnum::TWENTY_FIVE_LESSONS_WATCHED->name,
                    'category' => AchievementCategoryEnum::LESSONS->name,
                    'order' => 4
                ],
                [
                    'name' => LessonAchievementNameEnum::FIFTY_LESSONS_WATCHED->name,
                    'category' => AchievementCategoryEnum::LESSONS->name,
                    'order' => 5
                ],
                [
                    'name' => 'First Comment Written',
                    'category' => AchievementCategoryEnum::COMMENTS->name,
                    'order' => 1
                ],
                [
                    'name' => '3 Comment Written',
                    'category' => AchievementCategoryEnum::COMMENTS->name,
                    'order' => 2
                ],
                [
                    'name' => '5 Comment Written',
                    'category' => AchievementCategoryEnum::COMMENTS->name,
                    'order' => 3
                ],
                [
                    'name' => '10 Comment Written',
                    'category' => AchievementCategoryEnum::COMMENTS->name,
                    'order' => 4
                ],
                [
                    'name' => '20 Comment Written',
                    'category' => AchievementCategoryEnum::COMMENTS->name,
                    'order' => 5
                ]
            )
        );
    }
}
