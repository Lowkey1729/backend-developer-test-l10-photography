<?php

namespace Database\Factories;

use App\Enums\BadgeNameEnum;
use App\Models\Badge;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;

class BadgeFactory extends Factory
{
    protected $model = Badge::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence,
        ];
    }

    public function defaultBadges()
    {
        return $this->state(new Sequence(
            [
                'name' => BadgeNameEnum::BEGINNER->name,
                'order' => 1,
            ],
            [
                'name' => BadgeNameEnum::INTERMEDIATE->name,
                'order' => 2,
            ],
            [
                'name' => BadgeNameEnum::ADVANCED->name,
                'order' => 3,
            ],
            [
                'name' => BadgeNameEnum::MASTER->name,
                'order' => 4,
            ]
        ));
    }
}
