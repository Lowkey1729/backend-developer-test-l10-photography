<?php

namespace App\ViewModels;

use App\Enums\AchievementCategoryEnum;
use App\Models\Achievement;
use App\Models\Badge;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Spatie\ViewModels\ViewModel;

class AchievementViewModel extends ViewModel
{
    protected $snakeCase = true;

    public function __construct(
        protected User $user
    ) {

    }

    public function unlockedAchievements(): array
    {
        return $this->user->achievements()
            ->pluck('name')
            ->toArray();
    }

    public function nextAvailableAchievements(): array
    {

        $latestCommentAchievement = $this->user
            ->achievements()
            ->where('category', AchievementCategoryEnum::COMMENTS->value)
            ->orderBy('order', 'desc')
            ->first();

        $latestLessonsWatchedAchievement = $this->user->achievements()
            ->where('category', AchievementCategoryEnum::LESSONS->value)
            ->orderBy('order', 'desc')
            ->first();

        $nextCommentAchievement = Achievement::query()
            ->where('category', AchievementCategoryEnum::COMMENTS->value)
            ->when($latestCommentAchievement, function ($query) use ($latestCommentAchievement) {
                $query->where('id', '>', $latestCommentAchievement->id);
            })
            ->orderBy('order')
            ->first();

        $nextLessonWatchedAchievement = Achievement::query()
            ->where('category', AchievementCategoryEnum::LESSONS->value)
            ->when($latestLessonsWatchedAchievement, function (Builder $query) use ($latestLessonsWatchedAchievement) {
                $query->where('id', '>', $latestLessonsWatchedAchievement->id);
            })
            ->orderBy('order')
            ->first();

        return [
            'comment' => $nextCommentAchievement?->name,
            'lesson' => $nextLessonWatchedAchievement?->name,
        ];
    }

    public function currentBadge(): ?string
    {
        return $this->user->activeBadge()->first()?->name;
    }

    public function nextBadge(): ?string
    {
        $latestActiveBadge = $this->user->activeBadge()
            ->orderBy('order', 'desc')
            ->first();

        $nextActiveBadge = Badge::query()
            ->when($latestActiveBadge, function (Builder $query) use ($latestActiveBadge) {
                $query->where('order', '>', $latestActiveBadge->order);
            })->first();

        return $nextActiveBadge?->name;
    }

    public function remainingToUnlockNextBadge(): int
    {
        $unlockedBadgesIds = $this->user->badges()->pluck('badge_id')->toArray();

        return Badge::query()
            ->whereNotIn('id', $unlockedBadgesIds)
            ->count();
    }
}
