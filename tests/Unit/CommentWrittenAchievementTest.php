<?php

namespace Tests\Unit;

use App\Actions\Lessons\CommentOnLessonAction;
use App\Enums\CommentAchievementNameEnum;
use App\Enums\LessonAchievementNameEnum;
use App\Events\AchievementUnlocked;
use App\Events\CommentWritten;
use App\Listeners\Achievements\ProcessCommentedLessonAchievement;
use App\Models\Achievement;
use App\Models\Comment;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CommentWrittenAchievementTest extends TestCase
{
    public User $user;

    public string $body;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        Achievement::factory()
            ->count(10)
            ->defaultAchievements()
            ->create();

        $this->body = fake()->sentence;

    }

    public function test_it_dispatches_required_events_and_listeners()
    {

        Event::fake();

        $lesson = Lesson::factory()->create();

        $comment = CommentOnLessonAction::execute(
            'Test this lesson',
            $this->user,
            $lesson
        );

        Event::assertDispatched(CommentWritten::class);

        Event::assertListening(CommentWritten::class, ProcessCommentedLessonAchievement::class);

        resolve(ProcessCommentedLessonAchievement::class)
            ->handle(new CommentWritten($comment));

        Event::assertDispatched(AchievementUnlocked::class);

    }

    public function test_it_unlocks_first_comment_written_achievement(): void
    {
        $lesson = Lesson::factory()->create();

        $comment = CommentOnLessonAction::execute(
            $this->body,
            $this->user,
            $lesson
        );

        $this->assertCommentWasWritten($comment);

        $this->assertCommentAchievementWasUnlocked($comment, CommentAchievementNameEnum::FIRST_COMMENT_WRITTEN->name);

    }

    public function test_it_unlocks_three_comments_written_achievement(): void
    {
        $lesson = Lesson::factory()->create();

        Comment::factory()->count(2)
            ->state(['lesson_id' => $lesson->id, 'user_id' => $this->user->id])->create();

        $comment = CommentOnLessonAction::execute(
            $this->body,
            $this->user,
            $lesson
        );

        $this->assertCommentWasWritten($comment);

        $this->assertCommentAchievementWasUnlocked($comment, CommentAchievementNameEnum::THREE_COMMENTS_WRITTEN->name);

    }

    public function test_it_unlocks_five_comments_written_achievement(): void
    {
        $lesson = Lesson::factory()->create();

        Comment::factory()->count(4)
            ->state(['lesson_id' => $lesson->id, 'user_id' => $this->user->id])->create();

        $comment = CommentOnLessonAction::execute(
            $this->body,
            $this->user,
            $lesson
        );

        $this->assertCommentWasWritten($comment);

        $this->assertCommentAchievementWasUnlocked($comment, CommentAchievementNameEnum::FIVE_COMMENTS_WRITTEN->name);

    }

    public function test_it_unlocks_ten_comments_written_achievement(): void
    {
        $lesson = Lesson::factory()->create();

        Comment::factory()->count(9)
            ->state(['lesson_id' => $lesson->id, 'user_id' => $this->user->id])->create();

        $comment = CommentOnLessonAction::execute(
            $this->body,
            $this->user,
            $lesson
        );

        $this->assertCommentWasWritten($comment);

        $this->assertCommentAchievementWasUnlocked($comment, CommentAchievementNameEnum::TEN_COMMENTS_WRITTEN->name);

    }

    public function test_it_unlocks_twenty_comments_written_achievement(): void
    {
        $lesson = Lesson::factory()->create();

        Comment::factory()->count(19)
            ->state(['lesson_id' => $lesson->id, 'user_id' => $this->user->id])->create();

        $comment = CommentOnLessonAction::execute(
            $this->body,
            $this->user,
            $lesson
        );

        $this->assertCommentWasWritten($comment);

        $this->assertCommentAchievementWasUnlocked($comment, CommentAchievementNameEnum::TWENTY_COMMENTS_WRITTEN->name);

    }

    public function test_it_does_not_unlock_comment_written_achievement_when_criteria_is_not_met()
    {
        $lesson = Lesson::factory()->create();

        Comment::factory()->count(11)
            ->state(['lesson_id' => $lesson->id, 'user_id' => $this->user->id])->create();

        $comment = CommentOnLessonAction::execute(
            $this->body,
            $this->user,
            $lesson
        );

        $this->assertCommentWasWritten($comment);

        $fiveLessonsAchievement = Achievement::query()
            ->where('name', (LessonAchievementNameEnum::TWENTY_FIVE_LESSONS_WATCHED->name))
            ->first();

        $userAchievement = $this->user->achievements()->where('achievement_id', $fiveLessonsAchievement->id)->first();

        $this->assertNull($userAchievement);

        Event::fake();

        Event::assertNotDispatched(AchievementUnlocked::class);
    }

    protected function assertCommentWasWritten(Comment $comment): void
    {
        $this->assertEquals($this->body, $comment->body);

    }

    protected function assertCommentAchievementWasUnlocked(Comment $comment, string $achievementName): void
    {
        $fiveLessonsAchievement = Achievement::query()
            ->where('name', $achievementName)
            ->first();

        $userAchievement = $this->user->achievements()->where('achievement_id', $fiveLessonsAchievement->id)->first();

        $this->assertNotNull($userAchievement);

        Event::fake();

        resolve(ProcessCommentedLessonAchievement::class)
            ->handle(new CommentWritten($comment));

        Event::assertDispatched(AchievementUnlocked::class);
    }
}
