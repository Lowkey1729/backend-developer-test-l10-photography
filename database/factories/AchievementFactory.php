<?php

namespace Database\Factories;

use App\Enums\AchievementCategoryEnum;
use App\Enums\CommentAchievementNameEnum;
use App\Enums\LessonAchievementNameEnum;
use App\Models\Achievement;
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
            'order' => 1,
        ];
    }

    public function defaultAchievements(): AchievementFactory
    {
        return $this->state(
            new Sequence(
                [
                    'name' => LessonAchievementNameEnum::FIRST_LESSON_WATCHED->name,
                    'category' => AchievementCategoryEnum::LESSONS->value,
                    'order' => 1,
                ],
                [
                    'name' => LessonAchievementNameEnum::FIVE_LESSONS_WATCHED->name,
                    'category' => AchievementCategoryEnum::LESSONS->value,
                    'order' => 2,
                ],
                [
                    'name' => LessonAchievementNameEnum::TEN_LESSONS_WATCHED->name,
                    'category' => AchievementCategoryEnum::LESSONS->value,
                    'order' => 3,
                ],
                [
                    'name' => LessonAchievementNameEnum::TWENTY_FIVE_LESSONS_WATCHED->name,
                    'category' => AchievementCategoryEnum::LESSONS->value,
                    'order' => 4,
                ],
                [
                    'name' => LessonAchievementNameEnum::FIFTY_LESSONS_WATCHED->name,
                    'category' => AchievementCategoryEnum::LESSONS->value,
                    'order' => 5,
                ],
                [
                    'name' => CommentAchievementNameEnum::FIRST_COMMENT_WRITTEN->name,
                    'category' => AchievementCategoryEnum::COMMENTS->value,
                    'order' => 1,
                ],
                [
                    'name' => CommentAchievementNameEnum::THREE_COMMENTS_WRITTEN->name,
                    'category' => AchievementCategoryEnum::COMMENTS->value,
                    'order' => 2,
                ],
                [
                    'name' => CommentAchievementNameEnum::FIVE_COMMENTS_WRITTEN->name,
                    'category' => AchievementCategoryEnum::COMMENTS->value,
                    'order' => 3,
                ],
                [
                    'name' => CommentAchievementNameEnum::TEN_COMMENTS_WRITTEN->name,
                    'category' => AchievementCategoryEnum::COMMENTS->value,
                    'order' => 4,
                ],
                [
                    'name' => CommentAchievementNameEnum::TWENTY_COMMENTS_WRITTEN->name,
                    'category' => AchievementCategoryEnum::COMMENTS->value,
                    'order' => 5,
                ]
            )
        );
    }
}
