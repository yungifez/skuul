<?php

namespace Tests\Feature;

use App\Actions\Notice\PublishNotice;
use App\Actions\Notice\ReviseNotice;
use App\Actions\School\GrantSchoolMembership;
use App\Enums\AuditAction;
use App\Enums\NoticeRecipientState;
use App\Enums\NoticeStatus;
use App\Enums\Role;
use App\Exceptions\InvalidValueException;
use App\Jobs\SendNoticeEmails;
use App\Models\AuditEvent;
use App\Models\Notice;
use App\Models\NoticeRecipient;
use App\Models\School;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schedule;
use Tests\TestCase;

/**
 * A notice says who it is for, and the school can prove who was told.
 */
class NoticePublicationTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_new_notice_is_a_draft(): void
    {
        $this->assertSame(NoticeStatus::Draft, $this->notice()->status);
    }

    public function test_publishing_writes_a_record_for_every_reader(): void
    {
        $this->authorized_user([]);
        $teacher = $this->personWithRole(Role::Teacher);
        $notice = $this->notice(['audience' => ['roles' => [Role::Teacher->value]]]);

        $published = app(PublishNotice::class)->publish($notice);

        $this->assertSame(NoticeStatus::Published, $published->status);
        $this->assertNotNull($published->published_at);
        $this->assertTrue($published->recipients()->where('user_id', $teacher->id)->exists());
        $this->assertSame(
            NoticeRecipientState::Delivered,
            $published->recipients()->where('user_id', $teacher->id)->firstOrFail()->state
        );
    }

    public function test_a_notice_can_go_to_named_people_only(): void
    {
        $this->authorized_user([]);
        $chosen = $this->memberOf($this->workingSchool());
        $other = $this->memberOf($this->workingSchool());
        $notice = $this->notice(['audience' => ['user_ids' => [$chosen->id]]]);

        app(PublishNotice::class)->publish($notice);

        $this->assertTrue($notice->recipients()->where('user_id', $chosen->id)->exists());
        $this->assertFalse($notice->recipients()->where('user_id', $other->id)->exists());
    }

    public function test_a_notice_never_reaches_another_school(): void
    {
        $this->authorized_user([]);
        // A person who works only in the other school.
        $stranger = $this->nonMember();
        $otherSchool = School::factory()->create();
        $this->assertInstanceOf(School::class, $otherSchool);
        app(GrantSchoolMembership::class)->grant($stranger, $otherSchool, primary: true);
        $notice = $this->notice();

        app(PublishNotice::class)->publish($notice);

        $this->assertFalse($notice->recipients()->where('user_id', $stranger->id)->exists());
    }

    public function test_publishing_twice_does_not_send_it_again(): void
    {
        $this->authorized_user([]);
        $this->personWithRole(Role::Teacher);
        $notice = $this->notice();
        $action = app(PublishNotice::class);
        $action->publish($notice);
        $count = $notice->recipients()->count();

        $action->publish($notice->fresh());

        $this->assertSame($count, $notice->fresh()->recipients()->count());
    }

    public function test_a_scheduled_notice_waits_for_its_day(): void
    {
        $this->authorized_user([]);
        $notice = app(PublishNotice::class)->schedule($this->notice(), now()->addDay());

        $this->assertSame(NoticeStatus::Scheduled, $notice->status);
        $this->assertSame(0, $notice->recipients()->count());
    }

    public function test_the_scheduler_publishes_a_notice_whose_day_arrived(): void
    {
        $this->authorized_user([]);
        $this->memberOf($this->workingSchool());
        $notice = app(PublishNotice::class)->schedule($this->notice(), now()->subMinute());

        $this->artisan('skuul:process-notices')->assertSuccessful();

        $this->assertSame(NoticeStatus::Published, $notice->fresh()->status);
    }

    public function test_the_scheduler_takes_down_a_notice_that_ran_out(): void
    {
        $this->authorized_user([]);
        $notice = $this->notice(['stop_date' => now()->subDays(2)->toDateString()]);
        app(PublishNotice::class)->publish($notice);

        $this->artisan('skuul:process-notices')->assertSuccessful();

        $this->assertSame(NoticeStatus::Expired, $notice->fresh()->status);
    }

    public function test_an_expired_notice_stays_readable(): void
    {
        $this->authorized_user([]);
        $this->memberOf($this->workingSchool());
        $notice = $this->notice(['stop_date' => now()->subDays(2)->toDateString()]);
        app(PublishNotice::class)->publish($notice);
        $recipients = $notice->recipients()->count();

        app(PublishNotice::class)->expire($notice->fresh());

        $this->assertSame($recipients, $notice->fresh()->recipients()->count());
    }

    public function test_an_archived_notice_cannot_be_published(): void
    {
        $this->authorized_user([]);
        $notice = $this->notice(['status' => NoticeStatus::Archived]);

        $this->expectException(InvalidValueException::class);

        app(PublishNotice::class)->publish($notice);
    }

    public function test_email_is_queued_only_when_the_notice_asks_for_it(): void
    {
        Queue::fake();
        $this->authorized_user([]);
        $this->memberOf($this->workingSchool());

        app(PublishNotice::class)->publish($this->notice());
        Queue::assertNothingPushed();

        app(PublishNotice::class)->publish($this->notice(['send_email' => true]));
        Queue::assertPushed(SendNoticeEmails::class);
    }

    public function test_a_reader_can_mark_a_notice_as_read(): void
    {
        $this->authorized_user([]);
        $reader = $this->memberOf($this->workingSchool());
        $notice = $this->notice(['audience' => ['user_ids' => [$reader->id]]]);
        app(PublishNotice::class)->publish($notice);

        $recipient = NoticeRecipient::where('notice_id', $notice->id)->firstOrFail()->markRead();

        $this->assertSame(NoticeRecipientState::Read, $recipient->state);
        $this->assertNotNull($recipient->read_at);
    }

    public function test_publication_is_written_to_the_audit_log(): void
    {
        $this->authorized_user([]);
        $notice = $this->notice();

        app(PublishNotice::class)->publish($notice);

        $this->assertNotNull(AuditEvent::ofAction(AuditAction::NoticePublished)->forSubject($notice)->first());
    }

    public function test_a_published_notice_is_revised_as_an_auditable_draft_without_changing_the_sent_version(): void
    {
        $this->authorized_user([]);
        $actor = auth()->user();
        $notice = app(PublishNotice::class)->publish($this->notice());

        $this->assertInstanceOf(User::class, $actor);

        $revision = app(ReviseNotice::class)->revise($notice, ['content' => 'Sports day is now on Monday.'], $actor);

        $this->assertSame('Sports day is on Friday.', $notice->fresh()->content);
        $this->assertSame(NoticeStatus::Published, $notice->fresh()->status);
        $this->assertSame(NoticeStatus::Draft, $revision->status);
        $this->assertSame(2, $revision->revision);
        $this->assertSame($notice->id, $revision->revision_of_id);
        $this->assertSame('Sports day is now on Monday.', $revision->content);
        $this->assertSame(0, $revision->recipients()->count());
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::NoticeRevised)->forSubject($revision)->first());
    }

    public function test_publishing_a_notice_revision_supersedes_its_previous_version(): void
    {
        $this->authorized_user([]);
        $notice = app(PublishNotice::class)->publish($this->notice());
        $revision = app(ReviseNotice::class)->revise($notice);

        app(PublishNotice::class)->publish($revision);

        $this->assertSame(NoticeStatus::Superseded, $notice->fresh()->status);
        $this->assertFalse($notice->fresh()->active);
        $this->assertSame(NoticeStatus::Published, $revision->fresh()->status);
    }

    public function test_only_a_published_notice_can_be_revised(): void
    {
        $this->authorized_user([]);

        $this->expectException(InvalidValueException::class);

        app(ReviseNotice::class)->revise($this->notice());
    }

    public function test_the_notice_work_is_scheduled(): void
    {
        $commands = collect(Schedule::events())->map(fn ($event): string => $event->command ?? '');

        $this->assertTrue($commands->contains(fn (string $command): bool => str_contains($command, 'skuul:process-notices')));
    }

    /**
     * Create a notice in the working school.
     *
     * @param  array<string, mixed>  $attributes
     */
    private function notice(array $attributes = []): Notice
    {
        return Notice::create($attributes + [
            'title' => 'Sports day',
            'content' => 'Sports day is on Friday.',
            'start_date' => now()->toDateString(),
            'stop_date' => now()->addWeek()->toDateString(),
            'school_id' => $this->workingSchool()->id,
        ]);
    }

    /**
     * Create a member of the working school who holds the given role.
     */
    private function personWithRole(Role $role): User
    {
        $person = $this->memberOf($this->workingSchool());
        $person->assignRole($role->value);

        return $person->fresh();
    }
}
