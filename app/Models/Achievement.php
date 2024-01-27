<?php

namespace App\Models;

use App\Enums\AchievementCategoryEnum;
use App\Enums\CommentAchievementNameEnum;
use App\Enums\LessonAchievementNameEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'order',
    ];

    /**
     * Get the user's first name.
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => match ($this->category) {
                AchievementCategoryEnum::COMMENTS->value => CommentAchievementNameEnum::fromName($value)->value,
                AchievementCategoryEnum::LESSONS->value => LessonAchievementNameEnum::fromName($value)->value,
            },
        );
    }
}
