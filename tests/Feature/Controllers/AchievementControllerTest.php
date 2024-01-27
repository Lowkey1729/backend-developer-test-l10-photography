<?php

namespace Tests\Feature\Controllers;

use App\Actions\Lessons\CommentOnLessonAction;
use App\Actions\Lessons\ProcessWatchedLessonAction;
use App\Enums\BadgeNameEnum;
use App\Enums\CommentAchievementNameEnum;
use App\Enums\LessonAchievementNameEnum;
use App\Models\Badge;
use App\Models\Lesson;
use App\Models\User;
use Tests\TestCase;

class AchievementControllerTest extends TestCase
{
    public User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate:fresh --seed');

        $this->artisan('app:configure-app');

        $this->user = User::query()->first();

    }

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {

        $response = $this->get("/users/{$this->user->id}/achievements");

        $response->assertStatus(200);
    }

    public function test_it_returns_404_for_an_invalid_user_selected()
    {
        $id = fake()->sentence;

        $response = $this->get("/users/$id/achievements");

        $response->assertStatus(404);
    }

    public function test_it_returns_the_required_response()
    {
        $response = $this->get("/users/{$this->user->id}/achievements");

        $unlockedBadgesIds = $this->user->badges()->pluck('badge_id')->toArray();

        $acquiredBadgesCount = Badge::query()
            ->whereNotIn('id', $unlockedBadgesIds)
            ->count();

        $response->assertStatus(200);

        $response->assertJson([
            'unlocked_achievements' => [],
            'next_available_achievements' => [
                'comment' => CommentAchievementNameEnum::FIRST_COMMENT_WRITTEN->value,
                'lesson' => LessonAchievementNameEnum::FIRST_LESSON_WATCHED->value,
            ],
            'current_badge' => BadgeNameEnum::BEGINNER->name,
            'next_badge' => BadgeNameEnum::INTERMEDIATE->name,
            'remaining_to_unlock_next_badge' => $acquiredBadgesCount,
        ]);

    }

    public function test_it_returns_the_lessons_watched_achievements()
    {
        $lesson = Lesson::query()->first();

        ProcessWatchedLessonAction::execute($this->user, $lesson);

        $response = $this->get("/users/{$this->user->id}/achievements");

        $response->assertStatus(200);

        $data = $response->json();

        $this->assertArrayHasKey('unlocked_achievements', $data);
        $this->assertContains(LessonAchievementNameEnum::FIRST_LESSON_WATCHED->value, $data['unlocked_achievements']);
    }

    public function test_it_returns_the_comments_written_achievements()
    {
        $lesson = Lesson::query()->first();

        CommentOnLessonAction::execute(fake()->sentence, $this->user, $lesson);

        $response = $this->get("/users/{$this->user->id}/achievements");

        $response->assertStatus(200);

        $data = $response->json();

        $this->assertArrayHasKey('unlocked_achievements', $data);
        $this->assertContains(CommentAchievementNameEnum::FIRST_COMMENT_WRITTEN->value, $data['unlocked_achievements']);
    }

    public function test_it_returns_more_than_one_comments_written_achievements()
    {
        $lesson = Lesson::query()->first();

        for ($i = 0; $i <= 2; $i++) {
            CommentOnLessonAction::execute(fake()->sentence, $this->user, $lesson);
        }

        $response = $this->get("/users/{$this->user->id}/achievements");

        $response->assertStatus(200);

        $data = $response->json();

        $this->assertArrayHasKey('unlocked_achievements', $data);
        $this->assertContains(CommentAchievementNameEnum::FIRST_COMMENT_WRITTEN->value, $data['unlocked_achievements']);
        $this->assertContains(CommentAchievementNameEnum::THREE_COMMENTS_WRITTEN->value, $data['unlocked_achievements']);
    }

    public function test_it_returns_more_than_one_lessons_watched_achievements()
    {
        $lessons = Lesson::query()->take(10)->get();

        foreach ($lessons as $lesson) {
            ProcessWatchedLessonAction::execute($this->user, $lesson);
        }

        $response = $this->get("/users/{$this->user->id}/achievements");

        $response->assertStatus(200);

        $data = $response->json();

        $this->assertArrayHasKey('unlocked_achievements', $data);
        $this->assertContains(LessonAchievementNameEnum::FIRST_LESSON_WATCHED->value, $data['unlocked_achievements']);
        $this->assertContains(LessonAchievementNameEnum::FIVE_LESSONS_WATCHED->value, $data['unlocked_achievements']);
        $this->assertContains(LessonAchievementNameEnum::TEN_LESSONS_WATCHED->value, $data['unlocked_achievements']);
    }

    public function test_it_returns_next_available_lesson_watched_achievement()
    {
        $lesson = Lesson::query()->first();

        ProcessWatchedLessonAction::execute($this->user, $lesson);

        $response = $this->get("/users/{$this->user->id}/achievements");

        $response->assertStatus(200);

        $data = $response->json();

        $this->assertArrayHasKey('next_available_achievements', $data);
        $this->assertArrayHasKey('lesson', $data['next_available_achievements']);
        $this->assertEquals(LessonAchievementNameEnum::FIVE_LESSONS_WATCHED->value, $data['next_available_achievements']['lesson']);
    }

    public function test_it_returns_next_available_comment_written_achievement()
    {
        $lesson = Lesson::query()->first();

        CommentOnLessonAction::execute(fake()->sentence, $this->user, $lesson);

        $response = $this->get("/users/{$this->user->id}/achievements");

        $response->assertStatus(200);

        $data = $response->json();

        $this->assertArrayHasKey('next_available_achievements', $data);
        $this->assertArrayHasKey('comment', $data['next_available_achievements']);
        $this->assertEquals(CommentAchievementNameEnum::THREE_COMMENTS_WRITTEN->value, $data['next_available_achievements']['comment']);
    }

    public function test_it_returns_current_badge()
    {

        $response = $this->get("/users/{$this->user->id}/achievements");

        $response->assertStatus(200);

        $data = $response->json();

        $this->assertArrayHasKey('current_badge', $data);
        $this->assertEquals(BadgeNameEnum::BEGINNER->name, $data['current_badge']);
    }

    public function test_it_returns_next_badge()
    {

        $response = $this->get("/users/{$this->user->id}/achievements");

        $response->assertStatus(200);

        $data = $response->json();

        $this->assertArrayHasKey('next_badge', $data);
        $this->assertEquals(BadgeNameEnum::INTERMEDIATE->name, $data['next_badge']);
    }

    public function test_it_remaining_to_unlock_next_badge()
    {

        $response = $this->get("/users/{$this->user->id}/achievements");

        $response->assertStatus(200);

        $data = $response->json();

        $unlockedBadgesIds = $this->user->badges()->pluck('badge_id')->toArray();

        $unlockedBadgeCount = Badge::query()
            ->whereNotIn('id', $unlockedBadgesIds)
            ->count();

        $this->assertArrayHasKey('remaining_to_unlock_next_badge', $data);
        $this->assertEquals($unlockedBadgeCount, $data['remaining_to_unlock_next_badge']);
    }
}
