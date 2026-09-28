<?php

namespace Tests\Feature;

use App\Actions\Curriculum\AssignTeacher;
use App\Actions\School\EndSchoolMembership;
use App\Actions\School\GrantSchoolMembership;
use App\Actions\Timetable\CreateSectionTimetableOverride;
use App\Actions\Timetable\CreateTimetableSubstitution;
use App\Actions\Timetable\PublishTimetable;
use App\Actions\Timetable\ReviseTimetable;
use App\Enums\AuditAction;
use App\Enums\Role;
use App\Enums\TimetableStatus;
use App\Exceptions\InvalidValueException;
use App\Exceptions\TimetableConflictException;
use App\Livewire\TimetableCoverPanel;
use App\Livewire\TimetableStatusControl;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\AuditEvent;
use App\Models\CourseOffering;
use App\Models\Organization;
use App\Models\School;
use App\Models\Subject;
use App\Models\Timetable;
use App\Models\TimetableRecord;
use App\Models\TimetableSubstitution;
use App\Models\TimetableTimeSlot;
use App\Models\User;
use App\Models\Weekday;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
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
        $subject = $this->subject();
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

    public function test_a_section_can_start_an_override_from_a_published_template(): void
    {
        $this->authorized_user([]);
        $template = $this->timetable();
        TimetableTimeSlot::create(['timetable_id' => $template->id, 'start_time' => '08:00', 'stop_time' => '09:00']);
        app(PublishTimetable::class)->publish($template);
        $section = AcademicCycleSection::factory()->create([
            'school_id' => $template->academicCycleSection->school_id,
            'academic_year_id' => $template->academicCycleSection->academic_year_id,
            'academic_level_id' => $template->academicCycleSection->academic_level_id,
        ]);

        $override = app(CreateSectionTimetableOverride::class)->create($template->fresh(), $section, auth()->user());

        $this->assertSame(TimetableStatus::Draft, $override->status);
        $this->assertSame($template->id, $override->template_timetable_id);
        $this->assertSame($section->id, $override->academic_cycle_section_id);
        $this->assertSame(1, $override->timeSlots()->count());
    }

    public function test_a_published_timetable_can_record_dated_cover_without_changing_the_weekly_schedule(): void
    {
        $this->authorized_user([]);
        $replacementTeacher = $this->teacher();
        $timetable = $this->timetableWithLesson($this->teacher(), '08:00', '09:00');
        $slot = $timetable->timeSlots()->firstOrFail();
        $weekday = Weekday::firstOrFail();
        $date = Carbon::parse('next '.$weekday->name);
        app(PublishTimetable::class)->publish($timetable);

        $substitution = app(CreateTimetableSubstitution::class)->create(
            $timetable->fresh(),
            $slot,
            $weekday->id,
            $replacementTeacher,
            $date,
            'Teacher is attending training.',
            auth()->user(),
        );

        $this->assertDatabaseHas('timetable_substitutions', [
            'id' => $substitution->id,
            'timetable_id' => $timetable->id,
            'timetable_time_slot_id' => $slot->id,
            'weekday_id' => $weekday->id,
            'replacement_teacher_id' => $replacementTeacher->id,
            'substituted_on' => $date->toDateString(),
        ]);
        $this->assertSame(TimetableStatus::Published, $timetable->fresh()->status);
        $this->assertSame(1, $timetable->fresh()->timeSlots()->count());
        $this->assertNotNull(
            AuditEvent::ofAction(AuditAction::TimetableSubstitutionCreated)
                ->forSubject($substitution)
                ->first()
        );
    }

    public function test_an_authorized_staff_member_can_record_cover_from_the_timetable_screen(): void
    {
        $this->authorized_user(['read timetable', 'update timetable']);
        $replacementTeacher = $this->teacher();
        $timetable = $this->timetableWithLesson($this->teacher(), '08:00', '09:00');
        $slot = $timetable->timeSlots()->firstOrFail();
        $weekday = Weekday::firstOrFail();
        $date = Carbon::parse('next '.$weekday->name);
        app(PublishTimetable::class)->publish($timetable);

        $this->get(route('timetables.show', $timetable))->assertOk()->assertSee('Cover a lesson');

        Livewire::test(TimetableCoverPanel::class, ['timetable' => $timetable->fresh()])
            ->set('lesson', $slot->id.':'.$weekday->id)
            ->set('teacherId', (string) $replacementTeacher->id)
            ->set('coverDate', $date->toDateString())
            ->set('reason', 'Teacher is attending training.')
            ->call('recordCover')
            ->assertHasNoErrors()
            ->assertSee('Recorded cover');

        $this->assertDatabaseHas('timetable_substitutions', [
            'timetable_id' => $timetable->id,
            'timetable_time_slot_id' => $slot->id,
            'weekday_id' => $weekday->id,
            'replacement_teacher_id' => $replacementTeacher->id,
        ]);
    }

    public function test_one_teacher_cannot_cover_two_lessons_at_once(): void
    {
        $this->authorized_user([]);
        $teacher = $this->teacher();
        $first = $this->timetableWithLesson($this->teacher(), '08:00', '09:00');
        $second = $this->timetableWithLesson($this->teacher(), '08:30', '09:30');
        $weekday = Weekday::firstOrFail();
        $date = Carbon::parse('next '.$weekday->name);
        app(PublishTimetable::class)->publish($first);
        app(PublishTimetable::class)->publish($second);

        app(CreateTimetableSubstitution::class)->create($first->fresh(), $first->timeSlots()->firstOrFail(), $weekday->id, $teacher, $date, 'Absence', auth()->user());

        $this->expectException(InvalidValueException::class);

        app(CreateTimetableSubstitution::class)->create($second->fresh(), $second->timeSlots()->firstOrFail(), $weekday->id, $teacher, $date, 'Absence', auth()->user());
    }

    public function test_a_teacher_cannot_cover_during_their_own_lesson(): void
    {
        $this->authorized_user([]);
        $teacher = $this->teacher();
        $own = $this->timetableWithLesson($teacher, '08:00', '09:00');
        $absent = $this->timetableWithLesson($this->teacher(), '08:30', '09:30');
        app(PublishTimetable::class)->publish($own);
        app(PublishTimetable::class)->publish($absent);
        $weekday = Weekday::firstOrFail();

        $this->expectExceptionMessage("$teacher->name teaches");

        app(CreateTimetableSubstitution::class)->create($absent->fresh(), $absent->timeSlots()->firstOrFail(), $weekday->id, $teacher, Carbon::parse('next '.$weekday->name), 'Absence', auth()->user());
    }

    public function test_a_teacher_whose_own_lesson_is_covered_is_free_to_cover(): void
    {
        $this->authorized_user([]);
        $teacher = $this->teacher();
        $own = $this->timetableWithLesson($teacher, '08:00', '09:00');
        $absent = $this->timetableWithLesson($this->teacher(), '08:30', '09:30');
        app(PublishTimetable::class)->publish($own);
        app(PublishTimetable::class)->publish($absent);
        $weekday = Weekday::firstOrFail();
        $date = Carbon::parse('next '.$weekday->name);
        app(CreateTimetableSubstitution::class)->create($own->fresh(), $own->timeSlots()->firstOrFail(), $weekday->id, $this->teacher(), $date, 'Moved to cover', auth()->user());

        $cover = app(CreateTimetableSubstitution::class)->create($absent->fresh(), $absent->timeSlots()->firstOrFail(), $weekday->id, $teacher, $date, 'Absence', auth()->user());

        $this->assertSame($teacher->id, $cover->replacement_teacher_id);
    }

    public function test_a_teacher_cannot_cover_during_their_lesson_at_another_campus(): void
    {
        $this->authorized_user([]);
        $teacher = $this->teacher();
        $weekday = Weekday::firstOrFail();
        $date = Carbon::parse('next '.$weekday->name);
        $here = AcademicPeriod::query()->findOrFail(current_academic_period_id());
        $here->forceFill(['starts_on' => $date->copy()->subMonth(), 'ends_on' => $date->copy()->addMonth()])->save();
        $otherCampus = School::factory()->create(['organization_id' => $this->workingSchool()->organization_id]);
        $otherPeriod = AcademicPeriod::factory()->create([
            'school_id' => $otherCampus->id,
            'academic_year_id' => AcademicYear::factory()->create(['school_id' => $otherCampus->id])->id,
            'starts_on' => $here->starts_on,
            'ends_on' => $here->ends_on,
        ]);
        app(GrantSchoolMembership::class)->grant($teacher, $otherCampus);
        app(PublishTimetable::class)->publish($this->timetableWithLesson($teacher, '08:00', '09:00', $otherPeriod));
        $absent = $this->timetableWithLesson($this->teacher(), '08:30', '09:30');
        app(PublishTimetable::class)->publish($absent);

        $this->expectExceptionMessage("$teacher->name teaches");

        app(CreateTimetableSubstitution::class)->create($absent->fresh(), $absent->timeSlots()->firstOrFail(), $weekday->id, $teacher, $date, 'Absence', auth()->user());
    }

    public function test_a_lesson_left_behind_at_a_campus_the_teacher_left_does_not_block_cover(): void
    {
        $this->authorized_user([]);
        $teacher = $this->teacher();
        $weekday = Weekday::firstOrFail();
        $date = Carbon::parse('next '.$weekday->name);
        $here = AcademicPeriod::query()->findOrFail(current_academic_period_id());
        $here->forceFill(['starts_on' => $date->copy()->subMonth(), 'ends_on' => $date->copy()->addMonth()])->save();
        $otherCampus = School::factory()->create(['organization_id' => $this->workingSchool()->organization_id]);
        $otherPeriod = AcademicPeriod::factory()->create([
            'school_id' => $otherCampus->id,
            'academic_year_id' => AcademicYear::factory()->create(['school_id' => $otherCampus->id])->id,
            'starts_on' => $here->starts_on,
            'ends_on' => $here->ends_on,
        ]);
        app(GrantSchoolMembership::class)->grant($teacher, $otherCampus);
        app(PublishTimetable::class)->publish($this->timetableWithLesson($teacher, '08:00', '09:00', $otherPeriod));
        app(EndSchoolMembership::class)->end($teacher, $otherCampus);
        $absent = $this->timetableWithLesson($this->teacher(), '08:30', '09:30');
        app(PublishTimetable::class)->publish($absent);

        $cover = app(CreateTimetableSubstitution::class)->create($absent->fresh(), $absent->timeSlots()->firstOrFail(), $weekday->id, $teacher, $date, 'Absence', auth()->user());

        $this->assertSame($teacher->id, $cover->replacement_teacher_id);
    }

    public function test_cover_outside_the_timetables_dates_is_refused(): void
    {
        $this->authorized_user([]);
        $teacher = $this->teacher();
        $timetable = $this->timetableWithLesson($teacher, '08:00', '09:00');
        $weekday = Weekday::firstOrFail();
        $date = Carbon::parse('next '.$weekday->name);
        $timetable->forceFill(['effective_to' => $date->copy()->subDay()->toDateString()])->save();
        app(PublishTimetable::class)->publish($timetable);

        $this->expectException(InvalidValueException::class);

        app(CreateTimetableSubstitution::class)->create($timetable->fresh(), $timetable->timeSlots()->firstOrFail(), $weekday->id, $teacher, $date, 'Absence', auth()->user());
    }

    public function test_cover_recorded_by_mistake_is_withdrawn_before_the_lesson_only(): void
    {
        $this->authorized_user(['read timetable', 'update timetable']);
        $teacher = $this->teacher();
        $timetable = $this->timetableWithLesson($this->teacher(), '08:00', '09:00');
        $weekday = Weekday::firstOrFail();
        app(PublishTimetable::class)->publish($timetable);
        $coming = app(CreateTimetableSubstitution::class)->create($timetable->fresh(), $timetable->timeSlots()->firstOrFail(), $weekday->id, $teacher, Carbon::parse('next '.$weekday->name), 'Absence', auth()->user());
        $past = TimetableSubstitution::query()->create([
            'timetable_id' => $timetable->id,
            'timetable_time_slot_id' => $coming->timetable_time_slot_id,
            'weekday_id' => $weekday->id,
            'replacement_teacher_id' => $teacher->id,
            'substituted_on' => Carbon::parse('last '.$weekday->name)->toDateString(),
            'reason' => 'Absence',
            'approved_by' => auth()->id(),
        ]);

        Livewire::test(TimetableCoverPanel::class, ['timetable' => $timetable->fresh()])
            ->call('withdrawCover', $coming->id)
            ->call('withdrawCover', $past->id);

        $this->assertNull($coming->fresh());
        $this->assertNotNull($past->fresh());
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::TimetableSubstitutionWithdrawn)->first());
    }

    public function test_a_section_gets_one_version_of_a_template(): void
    {
        $this->authorized_user(['read timetable', 'update timetable']);
        $template = $this->timetable();
        TimetableTimeSlot::create(['timetable_id' => $template->id, 'start_time' => '08:00', 'stop_time' => '09:00']);
        app(PublishTimetable::class)->publish($template);
        $section = AcademicCycleSection::factory()->create([
            'school_id' => $template->academicCycleSection->school_id,
            'academic_year_id' => $template->academicCycleSection->academic_year_id,
            'academic_level_id' => $template->academicCycleSection->academic_level_id,
        ]);

        $panel = Livewire::test(TimetableCoverPanel::class, ['timetable' => $template->fresh()])
            ->set('sectionId', (string) $section->id)
            ->call('startOverride')
            ->assertHasNoErrors();

        $override = Timetable::query()->where('template_timetable_id', $template->id)->sole();
        $panel->assertRedirect(route('timetables.manage', $override));

        Livewire::test(TimetableCoverPanel::class, ['timetable' => $template->fresh()])
            ->set('sectionId', (string) $section->id)
            ->call('startOverride')
            ->assertHasErrors('sectionId');

        $this->assertSame(1, Timetable::query()->where('template_timetable_id', $template->id)->count());
    }

    public function test_the_panel_never_reaches_another_campus(): void
    {
        $this->authorized_user(['read timetable', 'update timetable']);
        $teacher = $this->teacher();
        $timetable = $this->timetableWithLesson($teacher, '08:00', '09:00');
        app(PublishTimetable::class)->publish($timetable);
        $ownSection = $timetable->academicCycleSection;
        $elsewhere = AcademicCycleSection::factory()->create([
            'school_id' => School::factory()->create()->id,
            'academic_year_id' => $ownSection->academic_year_id,
            'academic_level_id' => $ownSection->academic_level_id,
        ]);

        Livewire::test(TimetableCoverPanel::class, ['timetable' => $timetable->fresh()])
            ->set('sectionId', (string) $elsewhere->id)
            ->call('startOverride')
            ->assertStatus(404);

        $this->assertSame(0, Timetable::query()->where('academic_cycle_section_id', $elsewhere->id)->count());
    }

    public function test_a_teacher_cannot_be_timetabled_at_two_campuses_at_once(): void
    {
        $this->authorized_user([]);
        $teacher = $this->teacher();
        app(PublishTimetable::class)->publish($this->timetableWithLesson($teacher, '08:00', '09:00'));

        $here = AcademicPeriod::query()->findOrFail(current_academic_period_id());
        $here->forceFill(['starts_on' => now()->startOfMonth(), 'ends_on' => now()->addMonths(2)])->save();
        $otherCampus = School::factory()->create(['organization_id' => $this->workingSchool()->organization_id]);
        $otherYear = AcademicYear::factory()->create(['school_id' => $otherCampus->id]);
        $otherPeriod = AcademicPeriod::factory()->create([
            'school_id' => $otherCampus->id,
            'academic_year_id' => $otherYear->id,
            'starts_on' => $here->starts_on,
            'ends_on' => $here->ends_on,
        ]);
        app(GrantSchoolMembership::class)->grant($teacher, $otherCampus);
        $there = $this->timetableWithLesson($teacher, '08:30', '09:30', $otherPeriod);

        $this->expectException(TimetableConflictException::class);

        app(PublishTimetable::class)->publish($there);
    }

    public function test_a_campus_the_teacher_left_does_not_block_publishing(): void
    {
        $this->authorized_user([]);
        $teacher = $this->teacher();
        app(PublishTimetable::class)->publish($this->timetableWithLesson($teacher, '08:00', '09:00'));

        $here = AcademicPeriod::query()->findOrFail(current_academic_period_id());
        $here->forceFill(['starts_on' => now()->startOfMonth(), 'ends_on' => now()->addMonths(2)])->save();
        $otherCampus = School::factory()->create(['organization_id' => $this->workingSchool()->organization_id]);
        $otherPeriod = AcademicPeriod::factory()->create([
            'school_id' => $otherCampus->id,
            'academic_year_id' => AcademicYear::factory()->create(['school_id' => $otherCampus->id])->id,
            'starts_on' => $here->starts_on,
            'ends_on' => $here->ends_on,
        ]);
        app(GrantSchoolMembership::class)->grant($teacher, $otherCampus);
        app(EndSchoolMembership::class)->end($teacher, $this->workingSchool());
        $there = $this->timetableWithLesson($teacher, '08:30', '09:30', $otherPeriod);

        app(PublishTimetable::class)->publish($there);

        $this->assertTrue($there->fresh()->isPublished());
    }

    public function test_another_organization_s_timetable_does_not_block_publishing(): void
    {
        $this->authorized_user([]);
        $teacher = $this->teacher();
        app(PublishTimetable::class)->publish($this->timetableWithLesson($teacher, '08:00', '09:00'));

        $here = AcademicPeriod::query()->findOrFail(current_academic_period_id());
        $here->forceFill(['starts_on' => now()->startOfMonth(), 'ends_on' => now()->addMonths(2)])->save();
        $elsewhere = School::factory()->create(['organization_id' => Organization::factory()->create()->id]);
        $elsewherePeriod = AcademicPeriod::factory()->create([
            'school_id' => $elsewhere->id,
            'academic_year_id' => AcademicYear::factory()->create(['school_id' => $elsewhere->id])->id,
            'starts_on' => $here->starts_on,
            'ends_on' => $here->ends_on,
        ]);

        app(GrantSchoolMembership::class)->grant($teacher, $elsewhere);
        $published = app(PublishTimetable::class)->publish($this->timetableWithLesson($teacher, '08:30', '09:30', $elsewherePeriod));

        $this->assertSame(TimetableStatus::Published, $published->status);
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

    public function test_two_sections_cannot_use_the_same_room_at_the_same_time(): void
    {
        $this->authorized_user([]);
        $first = $this->timetableWithLesson($this->teacher(), '08:00', '09:00');
        $first->academicCycleSection->update(['room' => 'Science laboratory']);
        app(PublishTimetable::class)->publish($first);

        $second = $this->timetableWithLesson($this->teacher(), '08:00', '09:00');
        $second->academicCycleSection->update(['room' => 'Science laboratory']);

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

    public function test_the_office_publishes_a_draft_from_the_timetable_screen(): void
    {
        $this->authorized_user(['read timetable', 'update timetable']);
        $timetable = $this->timetable();

        $this->get(route('timetables.manage', $timetable))
            ->assertOk()
            ->assertSeeLivewire(TimetableStatusControl::class);

        Livewire::test(TimetableStatusControl::class, ['timetable' => $timetable])
            ->assertSee('Draft')
            ->assertSee('Publish')
            ->call('publish')
            ->assertRedirect();

        $this->assertSame(TimetableStatus::Published, $timetable->fresh()->status);
    }

    public function test_a_clash_keeps_the_timetable_a_draft_and_says_why(): void
    {
        $this->authorized_user(['update timetable']);
        $timetable = $this->timetable();
        TimetableTimeSlot::create(['timetable_id' => $timetable->id, 'start_time' => '08:00', 'stop_time' => '09:00']);
        TimetableTimeSlot::create(['timetable_id' => $timetable->id, 'start_time' => '08:30', 'stop_time' => '09:30']);

        Livewire::test(TimetableStatusControl::class, ['timetable' => $timetable])
            ->call('publish')
            ->assertNoRedirect()
            ->assertDispatched('status-message', fn (string $name, array $params): bool => $params['type'] === 'danger'
                && str_starts_with($params['message'], 'This timetable cannot be published'));

        $this->assertSame(TimetableStatus::Draft, $timetable->fresh()->status);
    }

    public function test_the_office_starts_a_revision_of_a_published_timetable(): void
    {
        $this->authorized_user(['update timetable']);
        $timetable = app(PublishTimetable::class)->publish($this->timetable());

        $component = Livewire::test(TimetableStatusControl::class, ['timetable' => $timetable])
            ->assertSee('New revision')
            ->assertDontSee('wire:click="publish"', false)
            ->call('revise');

        $draft = Timetable::query()->where('revision', 2)->sole();
        $component->assertRedirect(route('timetables.manage', $draft));
        $this->assertSame(TimetableStatus::Draft, $draft->status);
    }

    public function test_someone_without_update_access_cannot_publish(): void
    {
        $this->authorized_user(['read timetable']);
        $timetable = $this->timetable();

        Livewire::test(TimetableStatusControl::class, ['timetable' => $timetable])
            ->assertDontSee('wire:click="publish"', false)
            ->call('publish')
            ->assertForbidden();

        $this->assertSame(TimetableStatus::Draft, $timetable->fresh()->status);
    }

    /**
     * Create a draft timetable for a new class in the working school.
     */
    private function timetable(?AcademicPeriod $period = null): Timetable
    {
        $schoolId = $period->school_id ?? $this->workingSchool()->id;
        $academicYearId = $period->academic_year_id
            ?? AcademicYear::query()->where('school_id', $schoolId)->firstOrFail()->id;
        $academicLevel = AcademicLevel::factory()->create(['school_id' => $schoolId]);
        $cycleSection = AcademicCycleSection::factory()->create([
            'school_id' => $schoolId,
            'academic_year_id' => $academicYearId,
            'academic_level_id' => $academicLevel->id,
        ]);

        return Timetable::create([
            'name' => 'Week plan',
            'description' => 'The normal week',
            'academic_cycle_section_id' => $cycleSection->id,
            'academic_period_id' => $period->id ?? current_academic_period_id(),
        ]);
    }

    /**
     * Create a draft timetable holding one lesson taught by the teacher.
     */
    private function timetableWithLesson(User $teacher, string $start, string $stop, ?AcademicPeriod $period = null): Timetable
    {
        $timetable = $this->timetable($period);
        $schoolId = $timetable->academicCycleSection->school_id;
        $subject = Subject::factory()->create(['school_id' => $schoolId]);
        $cycleSection = $timetable->academicCycleSection;
        $courseOffering = CourseOffering::factory()->create([
            'school_id' => $schoolId,
            'academic_year_id' => $cycleSection->academic_year_id,
            'academic_period_id' => $timetable->academic_period_id,
            'academic_level_id' => $cycleSection->academic_level_id,
            'subject_id' => $subject->id,
        ]);
        $courseOffering->cycleSections()->attach($cycleSection);
        app(AssignTeacher::class)->assign($courseOffering, $teacher);

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

    private function subject(): Subject
    {
        return Subject::factory()->create([
            'school_id' => $this->workingSchool()->id,
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
