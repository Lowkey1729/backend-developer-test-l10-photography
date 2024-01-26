<?php

namespace Tests\Unit;

use App\Actions\ProcessWatchedLessonAction;
use App\Enums\LessonAchievementNameEnum;
use App\Events\AchievementUnlocked;
use App\Events\LessonWatched;
use App\Listeners\Achievements\ProcessLessonWatchedAchievements;
use App\Models\Achievement;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class LessonWatchedAchievementUnlockedEventTest extends TestCase
{

    public User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        Achievement::factory()
            ->count(10)
            ->defaultAchievements()
            ->create();

    }

    /**
     * @throws \Exception
     */
    public function test_it_dispatches_required_events_and_listeners()
    {
        $this->lessons(1);

        Event::fake();

        /** @var Lesson $lesson */
        $lesson = Lesson::query()->first();

        ProcessWatchedLessonAction::execute($this->user, $lesson);

        Event::assertDispatched(LessonWatched::class);

        Event::assertListening(LessonWatched::class, ProcessLessonWatchedAchievements::class);

        resolve(ProcessLessonWatchedAchievements::class)
            ->handle(new LessonWatched($lesson, $this->user));

        Event::assertDispatched(AchievementUnlocked::class);

    }


    public function test_it_unlocks_first_lesson_watched_achievement(): void
    {
        $this->lessons(1);

        /** @var Lesson $lesson */
        $lesson = Lesson::query()->first();

        ProcessWatchedLessonAction::execute($this->user, $lesson);

        $this->assertLessonIsWatched($lesson);

        $this->assertAchievementWasUnlocked(LessonAchievementNameEnum::FIRST_LESSON_WATCHED->name, $lesson);

    }

    public function test_it_unlocks_five_lessons_watched_achievement(): void
    {
        $this->lessons(4, true);

        $lesson = Lesson::factory()->create()->first();

        $lesson->users()->attach($this->user->id, ['watched' => false]);

        ProcessWatchedLessonAction::execute($this->user, $lesson);

        $this->assertLessonIsWatched($lesson);

        $this->assertAchievementWasUnlocked(LessonAchievementNameEnum::FIVE_LESSONS_WATCHED->name, $lesson);

    }

    public function test_it_unlocks_ten_lessons_watched_achievement(): void
    {
        $this->lessons(9, true);

        $lesson = Lesson::factory()->create()->first();

        $lesson->users()->attach($this->user->id, ['watched' => false]);

        ProcessWatchedLessonAction::execute($this->user, $lesson);

        $this->assertLessonIsWatched($lesson);

        $this->assertAchievementWasUnlocked(LessonAchievementNameEnum::TEN_LESSONS_WATCHED->name, $lesson);
    }

    public function test_it_unlocks_twenty_lessons_watched_achievement(): void
    {
        $this->lessons(24, true);

        $lesson = Lesson::factory()->create()->first();

        $lesson->users()->attach($this->user->id, ['watched' => false]);

        ProcessWatchedLessonAction::execute($this->user, $lesson);

        $this->assertLessonIsWatched($lesson);

        $this->assertAchievementWasUnlocked(LessonAchievementNameEnum::TWENTY_FIVE_LESSONS_WATCHED->name, $lesson);

    }

    public function test_it_unlocks_fifty_lessons_watched_achievement(): void
    {
        $this->lessons(49, true);

        $lesson = Lesson::factory()->create()->first();

        $lesson->users()->attach($this->user->id, ['watched' => false]);

        ProcessWatchedLessonAction::execute($this->user, $lesson);

        $this->assertLessonIsWatched($lesson);

        $this->assertAchievementWasUnlocked(LessonAchievementNameEnum::FIFTY_LESSONS_WATCHED->name, $lesson);

    }

    public function test_it_does_not_unlock_lessons_watched_achievement(): void
    {

        $this->lessons(10, true);

        $lesson = Lesson::factory()->create()->first();

        $lesson->users()->attach($this->user->id, ['watched' => false]);

        ProcessWatchedLessonAction::execute($this->user, $lesson);

        $this->assertLessonIsWatched($lesson);

        $fiveLessonsAchievement = Achievement::query()
            ->where('name', (LessonAchievementNameEnum::FIFTY_LESSONS_WATCHED->name))
            ->first();

        $userAchievement = $this->user->achievements()->where('achievement_id', $fiveLessonsAchievement->id)->first();

        $this->assertNull($userAchievement);

        Event::fake();

        Event::assertNotDispatched(AchievementUnlocked::class);
    }

    protected function lessons(int $count, bool $watched = false): void
    {
        Lesson::factory()
            ->count($count)
            ->create()
            ->each(function (Lesson $lesson) use ($watched) {
                $this->user->lessons()->attach($lesson->id, ['watched' => $watched]);
            });
    }

    protected function assertLessonIsWatched(Lesson $lesson): void
    {

        $lessonIsWatched = $this->user->lessons()->where('lesson_id', $lesson->id)
            ->first()->pivot->watched;

        $this->assertTrue((bool)$lessonIsWatched);

    }

    protected function assertAchievementWasUnlocked(string $achievementName, $lesson): void
    {
        $fiveLessonsAchievement = Achievement::query()
            ->where('name', $achievementName)
            ->first();

        $userAchievement = $this->user->achievements()->where('achievement_id', $fiveLessonsAchievement->id)->first();

        $this->assertNotNull($userAchievement);

        Event::fake();

        resolve(ProcessLessonWatchedAchievements::class)
            ->handle(new LessonWatched($lesson, $this->user));

        Event::assertDispatched(AchievementUnlocked::class);
    }
}
