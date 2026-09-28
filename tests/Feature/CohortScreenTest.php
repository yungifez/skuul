<?php

namespace Tests\Feature;

use App\Actions\Cohort\ChangeCohortMembership;
use App\Actions\Cohort\ChangeProgramParticipation;
use App\Enums\AuditAction;
use App\Enums\CohortType;
use App\Enums\EnrollmentStatus;
use App\Enums\ParticipationStatus;
use App\Enums\ProgramType;
use App\Livewire\CohortDirectory as CohortDirectoryComponent;
use App\Livewire\CohortRecord;
use App\Livewire\CreateCohortForm;
use App\Livewire\CreateProgramForm;
use App\Livewire\ProgramDirectory as ProgramDirectoryComponent;
use App\Livewire\ProgramRecord;
use App\Models\AuditEvent;
use App\Models\Cohort;
use App\Models\Program;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * The group and programme screens follow a set of learners across classes,
 * and record who takes part in what.
 */
class CohortScreenTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_the_group_list_starts_empty(): void
    {
        $this->authorized_user(['read cohort', 'create cohort']);

        $this->get(route('cohorts.index'))
            ->assertOk()
            ->assertSee('No groups yet')
            ->assertSee(route('cohorts.create'));
    }

    public function test_a_group_is_made_from_the_screen(): void
    {
        $this->authorized_user(['read cohort', 'create cohort']);

        $this->get(route('cohorts.create'))->assertOk()->assertSeeLivewire(CreateCohortForm::class);

        Livewire::test(CreateCohortForm::class)
            ->set('name', '  Class of 2030 ')
            ->set('type', CohortType::GraduationYear->value)
            ->set('description', 'Everybody due to finish in 2030.')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('cohorts.show', Cohort::inSchool()->sole()));

        $cohort = Cohort::inSchool()->sole();
        $this->assertSame('Class of 2030', $cohort->name);
        $this->assertFalse($cohort->is_restricted);
        $this->assertTrue(AuditEvent::query()->where('action', AuditAction::CohortChanged->value)->exists());
    }

    public function test_two_groups_cannot_share_a_name_whatever_the_capitals(): void
    {
        $this->authorized_user(['read cohort', 'create cohort', 'update cohort']);
        Cohort::create(['school_id' => $this->workingSchool()->id, 'name' => 'Class of 2030']);
        $other = $this->cohort(CohortType::Club, 'Chess club');

        Livewire::test(CreateCohortForm::class)
            ->set('name', 'class OF 2030')
            ->call('save')
            ->assertHasErrors('name')
            ->assertSee('This school already has a group with that name.');

        Livewire::test(CohortRecord::class, ['cohort' => $other])
            ->call('startEditing')
            ->set('name', 'CLASS of 2030')
            ->call('save')
            ->assertHasErrors('name');

        $this->assertSame(2, Cohort::inSchool()->count());
        $this->assertSame('Chess club', $other->fresh()->name);
    }

    public function test_a_learner_joins_and_leaves_a_group(): void
    {
        $this->authorized_user(['read cohort', 'create cohort', 'update cohort']);
        $cohort = $this->cohort();
        $enrollment = $this->enrollment(User::factory()->create(['name' => 'Ada Bell']));

        $this->get(route('cohorts.show', $cohort))->assertOk()->assertSeeLivewire(CohortRecord::class);

        $component = Livewire::test(CohortRecord::class, ['cohort' => $cohort])
            ->set('studentRecordId', (string) $enrollment->id)
            ->call('addMember')
            ->assertHasNoErrors()
            ->assertSee('Ada Bell')
            ->assertDontSee('Who has left');

        $member = $cohort->members()->sole();

        $component->call('removeMember', $member->id)
            ->assertSee('Who has left')
            ->call('removeMember', $member->id)
            ->assertDispatched('status-message', type: 'danger', message: 'This person already left the group.');

        $this->assertSame(today()->toDateString(), $member->fresh()->left_on->toDateString());
        $this->assertSame(2, AuditEvent::query()->where('action', AuditAction::CohortMembershipChanged->value)->count());
    }

    public function test_a_learner_cannot_join_on_a_day_that_has_not_come(): void
    {
        $this->authorized_user(['read cohort', 'update cohort']);
        $cohort = $this->cohort();
        $enrollment = $this->enrollment();

        Livewire::test(CohortRecord::class, ['cohort' => $cohort])
            ->set('studentRecordId', (string) $enrollment->id)
            ->set('joinedOn', today()->addWeek()->toDateString())
            ->call('addMember')
            ->assertHasErrors('joinedOn');

        $this->assertSame(0, $cohort->members()->count());
    }

    public function test_a_closed_group_takes_nobody_new(): void
    {
        $this->authorized_user(['read cohort', 'update cohort']);
        $cohort = $this->cohort();
        $enrollment = $this->enrollment();
        $component = Livewire::test(CohortRecord::class, ['cohort' => $cohort]);

        $cohort->update(['is_active' => false]);

        $component->set('studentRecordId', (string) $enrollment->id)
            ->call('addMember')
            ->assertHasErrors('studentRecordId')
            ->assertSee('is closed');

        $this->assertSame(0, $cohort->members()->count());

        Livewire::test(CohortRecord::class, ['cohort' => $cohort])
            ->assertSee('The group is closed, so nobody new joins.')
            ->assertDontSeeHtml('wire:submit="addMember"');
    }

    public function test_a_watchlist_is_hidden_from_a_person_who_may_not_read_it(): void
    {
        $this->authorized_user(['read cohort', 'read restricted cohort', 'create cohort']);
        $watchlist = $this->cohort(CohortType::Watchlist, 'Children we are watching');

        $this->authorized_user(['read cohort', 'create cohort']);
        $ordinary = $this->cohort();

        $this->get(route('cohorts.index'))
            ->assertOk()
            ->assertSee(route('cohorts.show', $ordinary))
            ->assertDontSee(route('cohorts.show', $watchlist));

        $this->get(route('cohorts.show', $watchlist))->assertForbidden();
    }

    public function test_group_filters_update_without_a_reload_and_keep_query_filter_compatibility(): void
    {
        $this->authorized_user(['read cohort']);
        $graduationGroup = $this->cohort(CohortType::GraduationYear, 'Class of 2030');
        $club = $this->cohort(CohortType::Club, 'Chess club');
        $club->update(['is_active' => false]);
        $activeClub = $this->cohort(CohortType::Club, 'Debate club');

        Livewire::test(CohortDirectoryComponent::class)
            ->set('type', CohortType::Club->value)
            ->assertSee(route('cohorts.show', $activeClub))
            ->assertDontSee(route('cohorts.show', $graduationGroup))
            ->set('activeOnly', true)
            ->assertSee(route('cohorts.show', $activeClub))
            ->assertDontSee(route('cohorts.show', $club))
            ->call('clearFilters')
            ->assertSet('type', '')
            ->assertSet('activeOnly', false)
            ->assertSee(route('cohorts.show', $graduationGroup));

        $this->get(route('cohorts.index', ['type' => CohortType::Club->value, 'active' => 1]))
            ->assertOk()
            ->assertSee(route('cohorts.show', $activeClub))
            ->assertDontSee(route('cohorts.show', $graduationGroup));
    }

    public function test_programme_filters_update_without_a_reload_and_keep_query_filter_compatibility(): void
    {
        $this->authorized_user(['read program']);
        $club = $this->program(ProgramType::Club, 'Chess club');
        $closedClub = $this->program(ProgramType::Club, 'Former club');
        $closedClub->update(['is_active' => false]);
        $support = $this->program(ProgramType::Intervention, 'Reading support');

        Livewire::test(ProgramDirectoryComponent::class)
            ->set('type', ProgramType::Club->value)
            ->assertSee(route('programs.show', $club))
            ->assertDontSee(route('programs.show', $support))
            ->set('activeOnly', true)
            ->assertSee(route('programs.show', $club))
            ->assertDontSee(route('programs.show', $closedClub))
            ->call('clearFilters')
            ->assertSet('type', '')
            ->assertSet('activeOnly', false)
            ->assertSee(route('programs.show', $support));

        $this->get(route('programs.index', ['type' => ProgramType::Club->value, 'active' => 1]))
            ->assertOk()
            ->assertSee(route('programs.show', $club))
            ->assertDontSee(route('programs.show', $support));
    }

    public function test_the_group_is_renamed_and_closed_from_the_screen(): void
    {
        $this->authorized_user(['read cohort', 'create cohort', 'update cohort']);
        $cohort = $this->cohort();

        Livewire::test(CohortRecord::class, ['cohort' => $cohort])
            ->call('startEditing')
            ->set('name', 'Class of 2031')
            ->set('isActive', false)
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('isEditing', false)
            ->assertSee('Closed');

        $this->assertSame('Class of 2031', $cohort->fresh()->name);
        $this->assertFalse($cohort->fresh()->is_active);
    }

    public function test_a_reader_changes_nothing(): void
    {
        $this->authorized_user(['read cohort']);
        $cohort = $this->cohort();
        $enrollment = $this->enrollment();

        Livewire::test(CohortRecord::class, ['cohort' => $cohort])
            ->assertDontSeeHtml('wire:click="startEditing"')
            ->call('startEditing')
            ->assertForbidden();

        Livewire::test(CohortRecord::class, ['cohort' => $cohort])
            ->set('studentRecordId', (string) $enrollment->id)
            ->call('addMember')
            ->assertForbidden();

        Livewire::test(CreateCohortForm::class)->assertForbidden();

        $this->assertSame(0, $cohort->members()->count());
    }

    public function test_the_programme_list_starts_empty(): void
    {
        $this->authorized_user(['read program', 'create program']);

        $this->get(route('programs.index'))
            ->assertOk()
            ->assertSee('No programmes yet')
            ->assertSee(route('programs.create'));
    }

    public function test_a_programme_is_opened_from_the_screen(): void
    {
        $this->authorized_user(['read program', 'create program']);
        $this->program(ProgramType::Club, 'Chess club');

        $this->get(route('programs.create'))->assertOk()->assertSeeLivewire(CreateProgramForm::class);

        Livewire::test(CreateProgramForm::class)
            ->set('name', 'CHESS CLUB')
            ->call('save')
            ->assertHasErrors('name')
            ->set('name', ' Reading support ')
            ->set('type', ProgramType::Intervention->value)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('programs.show', Program::inSchool()->where('name', 'Reading support')->sole()));

        $this->assertTrue(AuditEvent::query()->where('action', AuditAction::ProgramChanged->value)->exists());
    }

    public function test_a_programme_gives_a_learner_a_place(): void
    {
        $this->authorized_user(['read program', 'create program', 'update program']);
        $program = $this->program();
        $enrollment = $this->enrollment(User::factory()->create(['name' => 'Ada Bell']));
        $coach = $this->memberOf($this->workingSchool(), User::factory()->create(['name' => 'Coach Obi']));

        $this->get(route('programs.show', $program))->assertOk()->assertSeeLivewire(ProgramRecord::class);

        Livewire::test(ProgramRecord::class, ['program' => $program])
            ->set('studentRecordId', (string) $enrollment->id)
            ->set('schedule', 'Tuesday, 15:30')
            ->set('staffId', (string) $coach->id)
            ->call('givePlace')
            ->assertHasNoErrors()
            ->assertSee('Ada Bell')
            ->assertSee('Tuesday, 15:30')
            ->assertSee('Coach Obi');

        $this->assertSame(1, $program->participations()->count());
        $this->assertTrue(AuditEvent::query()->where('action', AuditAction::ProgramParticipationChanged->value)->exists());
    }

    public function test_a_learner_never_runs_a_programme(): void
    {
        $this->authorized_user(['read program', 'update program']);
        $program = $this->program();
        $learner = $this->enrollment();

        Livewire::test(ProgramRecord::class, ['program' => $program])
            ->set('studentRecordId', (string) $learner->id)
            ->set('staffId', (string) $learner->user_id)
            ->call('givePlace')
            ->assertHasErrors('staffId');

        $this->assertSame(0, $program->participations()->count());
    }

    public function test_a_place_moves_to_another_state(): void
    {
        $this->authorized_user(['read program', 'create program', 'update program']);
        $program = $this->program();
        $place = app(ChangeProgramParticipation::class)->join($program, $this->enrollment());

        Livewire::test(ProgramRecord::class, ['program' => $program])
            ->call('movePlace', $place->id, ParticipationStatus::Withdrawn->value, ParticipationStatus::Requested->value)
            ->assertDispatched('status-message', type: 'success');

        $this->assertSame(ParticipationStatus::Withdrawn, $place->fresh()->status);
        $this->assertNotNull($place->fresh()->ends_on);
    }

    public function test_a_second_person_moving_the_same_place_is_told_it_already_moved(): void
    {
        $this->authorized_user(['read program', 'update program']);
        $program = $this->program();
        $place = app(ChangeProgramParticipation::class)->join($program, $this->enrollment());

        Livewire::test(ProgramRecord::class, ['program' => $program])
            ->call('movePlace', $place->id, ParticipationStatus::Active->value, ParticipationStatus::Requested->value);

        Livewire::test(ProgramRecord::class, ['program' => $program])
            ->call('movePlace', $place->id, ParticipationStatus::Withdrawn->value, ParticipationStatus::Requested->value)
            ->assertDispatched('status-message', type: 'danger', message: 'Somebody else already moved this place to Taking part.');

        $this->assertSame(ParticipationStatus::Active, $place->fresh()->status);
    }

    public function test_a_withdrawn_place_reopens_only_while_the_door_is_open(): void
    {
        $this->authorized_user(['read program', 'update program']);
        $program = $this->program();
        $enrollment = $this->enrollment();
        $action = app(ChangeProgramParticipation::class);
        $first = $action->changeStatus($action->join($program, $enrollment), ParticipationStatus::Withdrawn);
        $second = $action->join($program, $enrollment);

        Livewire::test(ProgramRecord::class, ['program' => $program])
            ->call('movePlace', $first->id, ParticipationStatus::Active->value, ParticipationStatus::Withdrawn->value)
            ->assertDispatched('status-message', type: 'danger', message: 'The learner already holds another place in this programme.');

        $action->changeStatus($second, ParticipationStatus::Withdrawn);
        $program->update(['is_active' => false]);

        Livewire::test(ProgramRecord::class, ['program' => $program])
            ->call('movePlace', $first->id, ParticipationStatus::Active->value, ParticipationStatus::Withdrawn->value)
            ->assertDispatched('status-message', type: 'danger', message: 'This programme is closed.');

        $this->assertSame(ParticipationStatus::Withdrawn, $first->fresh()->status);
    }

    public function test_a_closed_programme_gives_no_new_places(): void
    {
        $this->authorized_user(['read program', 'create program', 'update program']);
        $program = $this->program();
        $enrollment = $this->enrollment();
        $component = Livewire::test(ProgramRecord::class, ['program' => $program])
            ->call('startEditing')
            ->set('isActive', false)
            ->call('save')
            ->assertSee('The programme is closed, so it gives no new places.');

        $component->set('studentRecordId', (string) $enrollment->id)
            ->call('givePlace')
            ->assertHasErrors('studentRecordId');

        $this->assertFalse($program->fresh()->is_active);
        $this->assertSame(0, $program->participations()->count());
    }

    public function test_a_place_of_another_programme_is_out_of_reach(): void
    {
        $this->authorized_user(['read program', 'update program']);
        $program = $this->program();
        $other = $this->program(ProgramType::Intervention, 'Reading support');
        $theirPlace = app(ChangeProgramParticipation::class)->join($other, $this->enrollment());

        $this->assertThrows(
            fn () => Livewire::test(ProgramRecord::class, ['program' => $program])
                ->call('movePlace', $theirPlace->id, ParticipationStatus::Withdrawn->value, ParticipationStatus::Requested->value),
            ModelNotFoundException::class,
        );

        $this->assertSame(ParticipationStatus::Requested, $theirPlace->fresh()->status);
    }

    public function test_a_learner_who_left_is_neither_offered_nor_added(): void
    {
        $this->authorized_user(['read cohort', 'update cohort']);
        $cohort = $this->cohort();
        $this->enrollment(User::factory()->create(['name' => 'Ada Bell']));
        $leaver = $this->enrollment(User::factory()->create(['name' => 'Ben Gone']));
        $leaver->update(['status' => EnrollmentStatus::Withdrawn]);

        Livewire::test(CohortRecord::class, ['cohort' => $cohort])
            ->assertSee('Ada Bell')
            ->assertDontSee('Ben Gone')
            ->set('studentRecordId', (string) $leaver->id)
            ->call('addMember')
            ->assertHasErrors('studentRecordId');

        $this->assertSame(0, $cohort->members()->count());
    }

    public function test_a_learner_of_another_school_never_joins_the_group(): void
    {
        $this->authorized_user(['read cohort', 'create cohort', 'update cohort']);
        $cohort = $this->cohort();
        $otherSchool = School::factory()->create();
        $outsider = StudentRecord::factory()->create(['school_id' => $otherSchool->id]);
        $theirGroup = Cohort::create(['school_id' => $otherSchool->id, 'name' => 'Their group']);
        $theirMember = app(ChangeCohortMembership::class)->addStudent($theirGroup, $outsider);

        Livewire::test(CohortRecord::class, ['cohort' => $cohort])
            ->set('studentRecordId', (string) $outsider->id)
            ->call('addMember')
            ->assertHasErrors('studentRecordId');

        $this->assertThrows(
            fn () => Livewire::test(CohortRecord::class, ['cohort' => $cohort])->call('removeMember', $theirMember->id),
            ModelNotFoundException::class,
        );

        $this->assertSame(0, $cohort->members()->count());
        $this->assertNull($theirMember->fresh()->left_on);
    }

    public function test_the_screens_need_permission(): void
    {
        $this->unauthorized_user();

        $this->get(route('cohorts.index'))->assertForbidden();
        $this->get(route('programs.index'))->assertForbidden();
    }

    /**
     * Make a group in the working school.
     */
    private function cohort(CohortType $type = CohortType::GraduationYear, string $name = 'Class of 2030'): Cohort
    {
        return Cohort::create([
            'school_id' => $this->workingSchool()->id,
            'name' => $name,
            'type' => $type,
        ]);
    }

    /**
     * Open a programme in the working school.
     */
    private function program(ProgramType $type = ProgramType::Club, string $name = 'Chess club'): Program
    {
        return Program::create([
            'school_id' => $this->workingSchool()->id,
            'name' => $name,
            'type' => $type,
        ]);
    }

    /**
     * Make an enrollment in the working school.
     */
    private function enrollment(?User $user = null): StudentRecord
    {
        return StudentRecord::factory()->create([
            'school_id' => $this->workingSchool()->id,
            ...($user === null ? [] : ['user_id' => $user->id]),
        ]);
    }
}
