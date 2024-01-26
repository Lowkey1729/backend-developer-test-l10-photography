<?php

namespace Tests\Unit;

use App\Actions\UnlockBadgeAction;
use App\Enums\BadgeNameEnum;
use App\Events\BadgeUnlocked;
use App\Models\Achievement;
use App\Models\Badge;
use App\Models\User;
use Tests\TestCase;

class BadgesAcquiredTest extends TestCase
{
    public User $user;

    public array $achievementIds;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        Badge::factory()->defaultBadges()->count(4)->create();

    }

    /**
     * A basic feature test example.
     */
    public function test_it_unlocks_the_intermediate_badge_after_unlocking_four_achievements(): void
    {
        $this->achievementIds = Achievement::factory()
            ->count(4)
            ->defaultAchievements()
            ->create()
            ->pluck('id')
            ->toArray();

        $this->user->achievements()->attach($this->achievementIds);

        \Event::fake();

        UnlockBadgeAction::execute($this->user);

        $this->assertEquals(4, $this->user->achievements()->count());


        $this->assertBadgeWasAcquired(BadgeNameEnum::INTERMEDIATE->name);

    }

    public function test_it_unlocks_the_advanced_badge_after_unlocking_eight_achievements(): void
    {
        $this->achievementIds = Achievement::factory()
            ->count(8)
            ->defaultAchievements()
            ->create()
            ->pluck('id')
            ->toArray();

        $this->user->achievements()->attach($this->achievementIds);

        \Event::fake();

        UnlockBadgeAction::execute($this->user);

        $this->assertEquals(8, $this->user->achievements()->count());

        $this->assertBadgeWasAcquired(BadgeNameEnum::ADVANCED->name);

    }

    public function test_it_unlocks_the_master_badge_after_unlocking_ten_achievements(): void
    {
        $this->achievementIds = Achievement::factory()
            ->count(10)
            ->defaultAchievements()
            ->create()
            ->pluck('id')
            ->toArray();

        $this->user->achievements()->attach($this->achievementIds);

        \Event::fake();

        UnlockBadgeAction::execute($this->user);

        $this->assertEquals(10, $this->user->achievements()->count());

        $this->assertBadgeWasAcquired(BadgeNameEnum::MASTER->name);

    }

    public function test_it_does_not_unlock_any_badge_when_number_of_achievements_is_not_met(): void
    {
        $this->achievementIds = Achievement::factory()
            ->count(3)
            ->defaultAchievements()
            ->create()
            ->pluck('id')
            ->toArray();

        $this->user->achievements()->attach($this->achievementIds);

        \Event::fake();

        UnlockBadgeAction::execute($this->user);

        $badge = Badge::query()
            ->where('name', BadgeNameEnum::MASTER->name)
            ->first();

        $acquiredBadge = $this->user->badges()->where('badge_id', $badge->id)->first();

        $this->assertNull($acquiredBadge);

        \Event::assertNotDispatched(BadgeUnlocked::class);

    }

    protected function assertBadgeWasAcquired(string $badgeName): void
    {
        $badge = Badge::query()
            ->where('name', $badgeName)
            ->first();

        $acquiredBadge = $this->user->badges()->where('badge_id', $badge->id)->first();

        $this->assertNotNull($acquiredBadge);

        \Event::assertDispatched(BadgeUnlocked::class);

    }
}
