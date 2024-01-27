<?php

namespace App\Actions;

use App\Enums\BadgeNameEnum;
use App\Enums\BadgeStatusEnum;
use App\Events\BadgeUnlocked;
use App\Models\Badge;
use App\Models\User;

class UnlockBadgeAction
{
    public static function execute(User $user): void
    {

        $achievementsCount = $user->achievements()->count();

        match ($achievementsCount) {
            0 => self::resolveBadges(BadgeNameEnum::BEGINNER->name, $user),
            4 => self::resolveBadges(BadgeNameEnum::INTERMEDIATE->name, $user),
            8 => self::resolveBadges(BadgeNameEnum::ADVANCED->name, $user),
            10 => self::resolveBadges(BadgeNameEnum::MASTER->name, $user),
            default => null
        };
    }

    protected static function resolveBadges(string $badgeName, User $user): void
    {
        $badge = Badge::query()
            ->where('name', $badgeName)
            ->first();

        \DB::transaction(function () use ($user, $badge) {

            $badgeExists = $user->badges()->where('badge_id', $badge->id)->exists();

            if (! $badgeExists) {

                $ids = $user->activeBadge()->pluck('badge_id')->toArray();

                $user->activeBadge()->syncWithPivotValues($ids, ['status' => BadgeStatusEnum::INACTIVE->value]);

                $user->badges()->attach($badge->id, ['status' => BadgeStatusEnum::ACTIVE->value, 'order' => $badge->order]);
            }
        });

        event(new BadgeUnlocked($badgeName, $user));

    }
}
