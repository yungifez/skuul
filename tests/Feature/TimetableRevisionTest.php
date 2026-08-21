<?php

namespace Tests\Feature;

use App\Actions\Curriculum\AssignTeacher;
use App\Actions\Timetable\PublishTimetable;
use App\Actions\Timetable\ReviseTimetable;
use App\Enums\AuditAction;
use App\Enums\Role;
use App\Enums\TimetableStatus;
use App\Exceptions\InvalidValueException;
use App\Exceptions\TimetableConflictException;
use App\Models\AuditEvent;
use App\Models\ClassGroup;
use App\Models\MyClass;
use App\Models\Subject;
use App\Models\Timetable;
use App\Models\TimetableRecord;
use App\Models\TimetableTimeSlot;
use App\Models\User;
use App\Models\Weekday;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * A published timetable is a promise, so it stops changing.
 */
class TimetableRevisionTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_new_timetable_starts_as_a_draft(): void
    {
        $timetable = $this->timetable();

        $this->assertSame(TimetableStatus::Draft, $timetable->status);
        $this->assertSame(1, $timetable->revision);
        $this->assertTrue($timetable->acceptsChanges());
    }

    public function test_publishing_puts_the_timetable_in_use(): void
    {
        $this->authorized_user([]);
        $timetable = $this->timetable();
        $actor = auth()->user();

        $published = app(PublishTimetable::class)->publish($timetable, $actor);

        $this->assertSame(TimetableStatus::Published, $published->status);
        $this->assertNotNull($published->published_at);
        $this->assertSame($actor->id, $published->published_by);
    }

    public function test_publishing_twice_changes_nothing(): void
    {
        $this->authorized_user([]);
        $action = app(PublishTimetable::class);
        $timetable = $action->publish($this->timetable());
        $publishedAt = $timetable->published_at;

        $action->publish($timetable->fresh());

        $this->assertEquals($publishedAt, $timetable->fresh()->published_at);
    }

    public function test_a_published_timetable_cannot_be_changed(): void
    {
        $this->authorized_user([]);
        $timetable = app(PublishTimetable::class)->publish($this->timetable());

        $this->expectException(InvalidValueException::class);

        $timetable->update(['name' => 'A different name']);
    }

    public function test_a_published_timetable_cannot_take_a_new_time_slot(): void
    {
        $this->authorized_user([]);
        $timetable = app(PublishTimetable::class)->publish($this->timetable());

        $this->expectException(InvalidValueException::class);

        TimetableTimeSlot::create([
            'timetable_id' => $timetable->id,
            'start_time' => '08:00',
            'stop_time' => '09:00',
        ]);
    }

    public function test_a_published_timetable_cannot_be_deleted(): void
    {
        $this->authorized_user([]);
        $timetable = app(PublishTimetable::class)->publish($this->timetable());

        $this->expectException(InvalidValueException::class);

        $timetable->delete();
    }

    public function test_a_revision_copies_the_week_into_a_draft(): void
    {
        $this->authorized_user([]);
        $timetable = $this->timetable();
        $slot = TimetableTimeSlot::create(['timetable_id' => $timetable->id, 'start_time' => '08:00', 'stop_time' => '09:00']);
        $subject = $this->subject($timetable->my_class_id);
        TimetableRecord::create([
            'timetable_time_slot_id' => $slot->id,
            'weekday_id' => Weekday::first()->id,
            'timetable_time_slot_weekdayable_id' => $subject->id,
            'timetable_time_slot_weekdayable_type' => $subject->getMorphClass(),
        ]);
        app(PublishTimetable::class)->publish($timetable);

        $draft = app(ReviseTimetable::class)->revise($timetable->fresh());

        $this->assertSame(TimetableStatus::Draft, $draft->status);
        $this->assertSame(2, $draft->revision);
        $this->assertSame($timetable->id, $draft->revision_of_id);
        $this->assertSame(1, $draft->timeSlots()->count());
        $this->assertSame(
            1,
            TimetableRecord::whereIn('timetable_time_slot_id', $draft->timeSlots()->pluck('id'))->count()
        );
    }

    public function test_publishing_a_revision_archives_the_one_it_replaces(): void
    {
        $this->authorized_user([]);
        $publish = app(PublishTimetable::class);
        $first = $publish->publish($this->timetable());
        $draft = app(ReviseTimetable::class)->revise($first->fresh());

        $publish->publish($draft);

        $this->assertSame(TimetableStatus::Archived, $first->fresh()->status);
        $this->assertSame(TimetableStatus::Published, $draft->fresh()->status);
    }

    public function test_an_archived_timetable_cannot_be_published_again(): void
    {
        $this->authorized_user([]);
        $publish = app(PublishTimetable::class);
        $timetable = $publish->publish($this->timetable());
        $publish->archive($timetable);

        $this->expectException(InvalidValueException::class);

        $publish->publish($timetable->fresh());
    }

    public function test_overlapping_time_slots_stop_publication(): void
    {
        $this->authorized_user([]);
        $timetable = $this->timetable();
        TimetableTimeSlot::create(['timetable_id' => $timetable->id, 'start_time' => '08:00', 'stop_time' => '09:00']);
        TimetableTimeSlot::create(['timetable_id' => $timetable->id, 'start_time' => '08:30', 'stop_time' => '09:30']);

        $this->expectException(TimetableConflictException::class);

        app(PublishTimetable::class)->publish($timetable);
    }

    public function test_one_teacher_cannot_teach_two_classes_at_once(): void
    {
        $this->authorized_user([]);
        $teacher = $this->teacher();

        $first = $this->timetableWithLesson($teacher, '08:00', '09:00');
        app(PublishTimetable::class)->publish($first);

        $second = $this->timetableWithLesson($teacher, '08:30', '09:30');

        $this->expectException(TimetableConflictException::class);

        app(PublishTimetable::class)->publish($second);
    }

    public function test_lessons_at_different_times_do_not_clash(): void
    {
        $this->authorized_user([]);
        $teacher = $this->teacher();
        $publish = app(PublishTimetable::class);
        $publish->publish($this->timetableWithLesson($teacher, '08:00', '09:00'));

        $second = $publish->publish($this->timetableWithLesson($teacher, '09:00', '10:00'));

        $this->assertSame(TimetableStatus::Published, $second->status);
    }

    public function test_publication_is_written_to_the_audit_log(): void
    {
        $this->authorized_user([]);
        $timetable = app(PublishTimetable::class)->publish($this->timetable());

        $this->assertNotNull(
            AuditEvent::ofAction(AuditAction::TimetablePublished)->forSubject($timetable)->first()
        );
    }

    /**
     * Create a draft timetable for a new class in the working school.
     */
    private function timetable(): Timetable
    {
        $classGroup = ClassGroup::factory()->create(['school_id' => $this->workingSchool()->id]);
        $class = MyClass::factory()->create(['class_group_id' => $classGroup->id]);

        return Timetable::create([
            'name' => 'Week plan',
            'description' => 'The normal week',
            'my_class_id' => $class->id,
            'semester_id' => current_semester_id(),
        ]);
    }

    /**
     * Create a draft timetable holding one lesson taught by the teacher.
     */
    private function timetableWithLesson(User $teacher, string $start, string $stop): Timetable
    {
        $timetable = $this->timetable();
        $subject = $this->subject($timetable->my_class_id);
        app(AssignTeacher::class)->assign($subject, $teacher);

        $slot = TimetableTimeSlot::create([
            'timetable_id' => $timetable->id,
            'start_time' => $start,
            'stop_time' => $stop,
        ]);

        TimetableRecord::create([
            'timetable_time_slot_id' => $slot->id,
            'weekday_id' => Weekday::first()->id,
            'timetable_time_slot_weekdayable_id' => $subject->id,
            'timetable_time_slot_weekdayable_type' => $subject->getMorphClass(),
        ]);

        return $timetable;
    }

    /**
     * Create a subject in the given class.
     */
    private function subject(int $classId): Subject
    {
        return Subject::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'my_class_id' => $classId,
        ]);
    }

    /**
     * Create a teacher of the working school.
     */
    private function teacher(): User
    {
        $teacher = $this->memberOf($this->workingSchool());
        $teacher->assignRole(Role::Teacher->value);

        return $teacher->fresh();
    }
}
