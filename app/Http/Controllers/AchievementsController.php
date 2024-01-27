<?php

namespace App\Http\Controllers;

use App\Actions\CommentOnLessonAction;
use App\Actions\ProcessWatchedLessonAction;
use App\Enums\AchievementCategoryEnum;
use App\Models\Achievement;
use App\Models\Badge;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class AchievementsController extends Controller
{
    public function index(User $user)
    {
        return response()->json([
            'unlocked_achievements' => $this->unlockedAchievements($user),
            'next_available_achievements' => $this->nextAvailableAchievements($user),
            'current_badge' => $this->currentBadge($user),
            'next_badge' => $this->nextBadge($user),
            'remaining_to_unlock_next_badge' => $this->remainingToUnlockNextBadge($user),
        ]);
    }

    protected function unlockedAchievements(User $user): array
    {
        return $user->achievements()
            ->pluck('name')
            ->toArray();
    }

    protected function nextAvailableAchievements(User $user)
    {

        $latestCommentAchievement = $user
            ->achievements()
            ->where('category', AchievementCategoryEnum::COMMENTS->value)
            ->orderBy('order', 'desc')
            ->first();

        $latestLessonsWatchedAchievement = $user->achievements()
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

    protected function currentBadge(User $user): ?string
    {
        return $user->activeBadge()->first()?->name;
    }

    protected function nextBadge(User $user): ?string
    {
        $latestActiveBadge = $user->activeBadge()
            ->orderBy('order', 'desc')
            ->first();

        $nextActiveBadge = Badge::query()
            ->when($latestActiveBadge, function (Builder $query) use ($latestActiveBadge) {
                $query->where('order', '>', $latestActiveBadge->order);
            })->first();

        return $nextActiveBadge?->name;
    }

    protected function remainingToUnlockNextBadge(User $user): int
    {
        $unlockedBadgesIds = $user->badges()->pluck('badge_id')->toArray();

        return Badge::query()
            ->whereNotIn('id', $unlockedBadgesIds)
            ->count();
    }
}
