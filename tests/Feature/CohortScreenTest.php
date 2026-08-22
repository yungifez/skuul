<?php

namespace Tests\Feature;

use App\Actions\Cohort\ChangeProgramParticipation;
use App\Enums\CohortType;
use App\Enums\ParticipationStatus;
use App\Enums\ProgramType;
use App\Models\Cohort;
use App\Models\Program;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        $response = $this->post(route('cohorts.store'), [
            'name' => 'Class of 2030',
            'type' => CohortType::GraduationYear->value,
            'description' => 'Everybody due to finish in 2030.',
        ]);

        $cohort = Cohort::inSchool()->sole();

        $response->assertRedirect(route('cohorts.show', $cohort));

        $this->assertSame('Class of 2030', $cohort->name);
        $this->assertFalse($cohort->is_restricted);
    }

    public function test_two_groups_cannot_share_a_name(): void
    {
        $this->authorized_user(['read cohort', 'create cohort']);
        Cohort::create(['school_id' => $this->workingSchool()->id, 'name' => 'Class of 2030']);

        $this->post(route('cohorts.store'), [
            'name' => 'Class of 2030',
            'type' => CohortType::GraduationYear->value,
        ])->assertSessionHasErrors('name');

        $this->assertSame(1, Cohort::inSchool()->count());
    }

    public function test_a_learner_joins_and_leaves_a_group(): void
    {
        $this->authorized_user(['read cohort', 'create cohort', 'update cohort']);
        $cohort = $this->cohort();
        $enrollment = $this->enrollment(User::factory()->create(['name' => 'Ada Bell']));

        $this->from(route('cohorts.show', $cohort))
            ->post(route('cohorts.members.store', $cohort), ['student_record_id' => $enrollment->id])
            ->assertRedirect(route('cohorts.show', $cohort));

        $member = $cohort->members()->sole();

        $this->get(route('cohorts.show', $cohort))
            ->assertOk()
            ->assertSee('Ada Bell')
            ->assertDontSee('Who has left');

        $this->from(route('cohorts.show', $cohort))
            ->delete(route('cohorts.members.destroy', [$cohort, $member]))
            ->assertRedirect(route('cohorts.show', $cohort));

        $this->assertNotNull($member->fresh()->left_on);

        $this->get(route('cohorts.show', $cohort))
            ->assertOk()
            ->assertSee('Who has left');
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

    public function test_the_group_is_renamed_and_closed_from_the_screen(): void
    {
        $this->authorized_user(['read cohort', 'create cohort', 'update cohort']);
        $cohort = $this->cohort();

        $this->from(route('cohorts.show', $cohort))
            ->put(route('cohorts.update', $cohort), [
                'name' => 'Class of 2031',
                'is_active' => '0',
            ])
            ->assertRedirect(route('cohorts.show', $cohort));

        $this->assertSame('Class of 2031', $cohort->fresh()->name);
        $this->assertFalse($cohort->fresh()->is_active);
    }

    public function test_the_programme_list_starts_empty(): void
    {
        $this->authorized_user(['read program', 'create program']);

        $this->get(route('programs.index'))
            ->assertOk()
            ->assertSee('No programmes yet')
            ->assertSee(route('programs.create'));
    }

    public function test_a_programme_gives_a_learner_a_place(): void
    {
        $this->authorized_user(['read program', 'create program', 'update program']);
        $program = $this->program();
        $enrollment = $this->enrollment(User::factory()->create(['name' => 'Ada Bell']));

        $this->from(route('programs.show', $program))
            ->post(route('programs.participations.store', $program), [
                'student_record_id' => $enrollment->id,
                'schedule' => 'Tuesday, 15:30',
            ])
            ->assertRedirect(route('programs.show', $program));

        $this->get(route('programs.show', $program))
            ->assertOk()
            ->assertSee('Ada Bell')
            ->assertSee('Tuesday, 15:30');

        $this->assertSame(1, $program->participations()->count());
    }

    public function test_a_place_moves_to_another_state(): void
    {
        $this->authorized_user(['read program', 'create program', 'update program']);
        $program = $this->program();
        $place = app(ChangeProgramParticipation::class)->join($program, $this->enrollment());

        $this->from(route('programs.show', $program))
            ->put(route('programs.participations.update', [$program, $place]), [
                'status' => ParticipationStatus::Withdrawn->value,
                'note' => 'The learner asked to stop.',
            ])
            ->assertRedirect(route('programs.show', $program));

        $this->assertSame(ParticipationStatus::Withdrawn, $place->fresh()->status);
        $this->assertNotNull($place->fresh()->ends_on);
    }

    public function test_a_closed_programme_gives_no_new_places(): void
    {
        $this->authorized_user(['read program', 'create program', 'update program']);
        $program = $this->program();
        $program->update(['is_active' => false]);

        $this->from(route('programs.show', $program))
            ->post(route('programs.participations.store', $program), [
                'student_record_id' => $this->enrollment()->id,
            ])
            ->assertSessionHasErrors('participation');

        $this->assertSame(0, $program->participations()->count());
    }

    public function test_a_learner_of_another_school_never_joins_the_group(): void
    {
        $this->authorized_user(['read cohort', 'create cohort', 'update cohort']);
        $cohort = $this->cohort();
        $outsider = StudentRecord::factory()->create(['school_id' => School::factory()->create()->id]);

        $this->from(route('cohorts.show', $cohort))
            ->post(route('cohorts.members.store', $cohort), ['student_record_id' => $outsider->id])
            ->assertSessionHasErrors('student_record_id');

        $this->assertSame(0, $cohort->members()->count());
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
    private function program(): Program
    {
        return Program::create([
            'school_id' => $this->workingSchool()->id,
            'name' => 'Chess club',
            'type' => ProgramType::Club,
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
