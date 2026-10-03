<?php

namespace Tests\Feature;

use App\Actions\Identity\ChangeGuardianLink;
use App\Enums\AuditAction;
use App\Enums\PortalRequestStatus;
use App\Enums\PortalRequestType;
use App\Exceptions\InvalidValueException;
use App\Livewire\AssignStudentsToParent;
use App\Livewire\CreateParentForm;
use App\Livewire\EditParentForm;
use App\Livewire\ListParentsTable;
use App\Models\AuditEvent;
use App\Models\PortalRequest;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\Portal\PortalAccess;
use App\Traits\FeatureTestTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class ParentTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;
    use WithFaker;

    public function test_view_all_parents_cannot_be_accessed_by_unauthorised_users()
    {
        $this->unauthorized_user()->get('dashboard/parents/')->assertForbidden();
    }

    public function test_view_all_parents_can_be_accessed_by_authorised_users()
    {
        $this->authorized_user(['read parent'])
            ->get('dashboard/parents')
            ->assertOk()
            ->assertSee('data-slot="data-table"', false)
            ->assertSee('Search rows...')
            ->assertSee('No parents yet');
    }

    public function test_the_parent_table_searches_rows_on_the_server(): void
    {
        $matchingParent = User::factory()->create(['name' => 'Ada Lovelace']);
        $matchingParent->assignRole('parent');

        $otherParent = User::factory()->create(['name' => 'Grace Hopper']);
        $otherParent->assignRole('parent');

        $this->authorized_user(['read parent']);

        Livewire::test(ListParentsTable::class)
            ->call('updateTable', [
                'search' => 'Ada',
                'perPage' => 10,
                'page' => 1,
            ])
            ->assertSee('Ada Lovelace')
            ->assertDontSee('Grace Hopper');
    }

    public function test_create_parent_cannot_be_accessed_by_unauthorised_users()
    {
        $this->unauthorized_user()->get('dashboard/parents/create')->assertForbidden();
    }

    public function test_create_parent_can_be_accessed_by_authorised_users()
    {
        $this->authorized_user(['create parent'])->get('dashboard/parents/create')->assertOk();
    }

    public function test_authorised_users_can_view_a_parent_profile(): void
    {
        $parent = User::factory()->create(['birthday' => '1985-04-10']);
        $parent->parentRecord()->create(['user_id' => $parent->id]);
        $parent->assignRole('parent');

        $this->authorized_user(['read parent'])
            ->get("dashboard/parents/$parent->id")
            ->assertOk()
            ->assertSee('Email address')
            ->assertSee('10 Apr 1985');
    }

    public function test_a_parent_profile_lists_the_learners_linked_here(): void
    {
        $parent = User::factory()->create();
        $parentRecord = $parent->parentRecord()->create(['user_id' => $parent->id]);
        $parent->assignRole('parent');
        $here = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        $elsewhere = StudentRecord::factory()->create(['school_id' => School::factory()->create()->id]);
        $parentRecord->students()->attach([$here->user_id, $elsewhere->user_id]);

        $this->authorized_user(['read parent'])
            ->get(route('parents.show', $parent))
            ->assertOk()
            ->assertSee('Linked learners')
            ->assertSee($here->user->name)
            ->assertDontSee($elsewhere->user->name)
            ->assertDontSee('Change linked learners');

        auth()->user()->givePermissionTo('update parent');

        $this->get(route('parents.show', $parent))
            ->assertSee('Change linked learners')
            ->assertSee(route('parents.assign-student', $parent));
    }

    public function test_unauthorised_users_cannot_create_parents(): void
    {
        $this->unauthorized_user();

        Livewire::test(CreateParentForm::class)->assertForbidden();
    }

    public function test_authorized_user_can_create_parent(): void
    {
        $email = $this->faker()->unique()->freeEmail();
        $this->authorized_user(['create parent', 'read parent']);

        $component = Livewire::test(CreateParentForm::class)
            ->set('name', 'Test parent cody')
            ->set('email', $email)
            ->set('gender', 'Male')
            ->set('nationality', 'Nigerian')
            ->set('address', 'test address')
            ->set('addressLine2', 'Flat 3')
            ->set('postalCode', '100001')
            ->set('birthday', '2004-04-22')
            ->set('phone', '08080808080')
            ->call('save')
            ->assertHasNoErrors();

        $person = User::query()->where('email', $email)->sole();

        $component->assertRedirect(route('parents.show', $person));
        $this->assertTrue($person->hasRole('parent'));
        $this->assertSame('Flat 3', $person->address_line_2);
        $this->assertSame('Nigerian', $person->nationality);
        $this->assertSame('100001', $person->postal_code);
    }

    public function test_edit_parent_cannot_be_accessed_to_unauthorised_users()
    {
        $parent = User::factory()->create();
        $parent->assignRole('parent');
        $this->unauthorized_user()->get("dashboard/parents/$parent->id/edit")->assertForbidden();
    }

    public function test_edit_parent_can_be_accessed_by_authorised_users()
    {
        $parent = User::factory()->create();
        $parent->assignRole('parent');
        $this->authorized_user(['update parent'])->get("dashboard/parents/$parent->id/edit")->assertOk();
    }

    public function test_a_guardian_added_by_two_campuses_keeps_one_guardian_record(): void
    {
        $email = $this->faker()->unique()->freeEmail();
        $firstSchool = $this->workingSchool();
        $this->authorized_user(['create parent'], $firstSchool);
        Livewire::test(CreateParentForm::class)->set('name', 'Ada Bell')->set('email', $email)->call('save')->assertHasNoErrors();

        $this->authorized_user(['create parent'], School::factory()->create(['organization_id' => $firstSchool->organization_id]));
        Livewire::test(CreateParentForm::class)->set('name', 'Ada Bell')->set('email', $email)->call('save')->assertHasNoErrors();

        $guardian = User::query()->where('email', $email)->sole();
        $this->assertSame(1, $guardian->parentRecord()->count());
    }

    public function test_another_organization_cannot_take_on_a_guardian_by_email(): void
    {
        $email = $this->faker()->unique()->freeEmail();
        $this->authorized_user(['create parent'], $this->workingSchool());
        Livewire::test(CreateParentForm::class)->set('name', 'Ada Bell')->set('email', $email)->call('save')->assertHasNoErrors();

        $otherSchool = School::factory()->create();
        $this->authorized_user(['create parent'], $otherSchool);
        Livewire::test(CreateParentForm::class)->set('name', 'Ada Bell')->set('email', $email)->call('save')
            ->assertHasErrors(['email' => 'This email belongs to a person outside your organization, so it cannot be used here.']);

        $this->assertFalse(User::query()->where('email', $email)->sole()->belongsToSchool($otherSchool));
    }

    public function test_unauthorised_users_cannot_update_parents(): void
    {
        $person = User::factory()->create();
        $person->assignRole('parent');
        $this->unauthorized_user();

        Livewire::test(EditParentForm::class, ['parent' => $person])->assertForbidden();
    }

    public function test_authorised_users_can_update_parents(): void
    {
        $person = User::factory()->create(['nationality' => 'Nigerian', 'postal_code' => '100001']);
        $person->assignRole('parent');
        $email = $this->faker()->unique()->freeEmail();
        $this->authorized_user(['update parent']);

        Livewire::test(EditParentForm::class, ['parent' => $person])
            ->assertSet('nationality', 'Nigerian')
            ->set('name', 'Renamed parent')
            ->set('email', $email)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('parents.show', $person));

        $person->refresh();
        $this->assertSame('Renamed parent', $person->name);
        $this->assertSame($email, $person->email);
        $this->assertSame('Nigerian', $person->nationality);
        $this->assertSame('100001', $person->postal_code);
    }

    public function test_unauthorised_users_cannot_delete_parents()
    {
        $parent = User::factory()->create();
        $parent->assignRole('parent');
        $this->authorized_user(['read parent']);

        Livewire::test(ListParentsTable::class)
            ->call('deleteParent', $parent->id)
            ->assertForbidden();

        $this->assertNotSoftDeleted($parent);
    }

    public function test_authorised_users_can_delete_parents()
    {
        $parent = User::factory()->create();
        $parent->assignRole('parent');
        $this->authorized_user(['read parent', 'delete parent']);

        Livewire::test(ListParentsTable::class)
            ->assertSeeHtml('$wire.call(&quot;deleteParent&quot;, row.id)')
            ->call('deleteParent', $parent->id)
            ->assertDispatched('status-message', type: 'success');

        $this->assertSoftDeleted($parent);
    }

    public function test_a_parent_who_also_runs_the_campus_is_out_of_reach_of_a_parent_manager(): void
    {
        $parent = User::factory()->create();
        $parent->assignRole('parent');
        $parent->assignRole('admin');
        $this->authorized_user(['read parent', 'delete parent']);

        Livewire::test(ListParentsTable::class)
            ->call('deleteParent', $parent->id)
            ->assertForbidden();

        $this->assertNotSoftDeleted($parent);
    }

    public function test_a_parent_shared_with_a_sibling_campus_is_only_removed_from_this_one(): void
    {
        $parent = User::factory()->create();
        $parent->assignRole('parent');
        $this->authorized_user(['read parent', 'delete parent']);
        $sibling = School::factory()->create(['organization_id' => $this->workingSchool()->organization_id]);
        $this->memberOf($sibling, $parent);

        Livewire::test(ListParentsTable::class)
            ->call('deleteParent', $parent->id)
            ->assertDispatched('status-message', message: "{$parent->name} was removed from this school.");

        $this->assertNotSoftDeleted($parent);
        $this->assertFalse($parent->belongsToSchool($this->workingSchool()));
        $this->assertTrue($parent->belongsToSchool($sibling));
    }

    public function test_a_parent_removed_here_no_longer_reads_this_schools_children(): void
    {
        $parent = $this->guardian();
        $this->authorized_user(['read parent', 'delete parent']);
        $sibling = School::factory()->create(['organization_id' => $this->workingSchool()->organization_id]);
        $this->memberOf($sibling, $parent);
        $here = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        $there = StudentRecord::factory()->create(['school_id' => $sibling->id]);
        $parent->parentRecord->students()->attach([$here->user_id, $there->user_id]);

        Livewire::test(ListParentsTable::class)
            ->call('deleteParent', $parent->id)
            ->assertDispatched('status-message', message: "{$parent->name} was removed from this school.");

        $this->assertSame([$there->user_id], $parent->parentRecord->students()->pluck('users.id')->all());
        $this->assertFalse(app(PortalAccess::class)->canRead($parent->refresh(), $here));
    }

    public function test_a_parent_of_another_school_cannot_be_deleted()
    {
        $parent = User::factory()->create();
        $parent->assignRole('parent');
        $parent->schoolMemberships()->delete();
        $this->memberOf(School::factory()->create(), $parent->refresh());
        $this->authorized_user(['read parent', 'delete parent']);

        try {
            Livewire::test(ListParentsTable::class)->call('deleteParent', $parent->id);
            $this->fail('A parent of another school was reached.');
        } catch (ModelNotFoundException) {
        }

        $this->assertNotSoftDeleted($parent);
    }

    public function test_unauthorised_users_cannot_assign_student_to_parent()
    {
        $parent = $this->guardian();
        $this->unauthorized_user();

        Livewire::test(AssignStudentsToParent::class, ['parent' => $parent])->assertForbidden();
    }

    public function test_authorised_users_can_assign_student_to_parent()
    {
        $student = StudentRecord::factory()->create();
        $parent = $this->guardian();
        $this->authorized_user(['update parent']);
        $auditsBefore = AuditEvent::query()->where('action', AuditAction::GuardianLinkChanged)->count();

        Livewire::test(AssignStudentsToParent::class, ['parent' => $parent])
            ->set('academicCycleSectionId', $student->academic_cycle_section_id)
            ->set('studentId', $student->user_id)
            ->call('add')
            ->assertHasNoErrors()
            ->assertSee($student->user->name);

        $this->assertDatabaseHas('parent_record_user', [
            'parent_record_id' => $parent->parentRecord->id,
            'user_id' => $student->user_id,
        ]);
        $this->assertSame($auditsBefore + 1, AuditEvent::query()->where('action', AuditAction::GuardianLinkChanged)->count());
    }

    public function test_linking_the_same_learner_twice_changes_nothing(): void
    {
        $student = StudentRecord::factory()->create();
        $parent = $this->guardian();
        $this->authorized_user(['update parent']);

        $component = Livewire::test(AssignStudentsToParent::class, ['parent' => $parent])
            ->set('academicCycleSectionId', $student->academic_cycle_section_id)
            ->set('studentId', $student->user_id);
        $auditsBefore = AuditEvent::query()->where('action', AuditAction::GuardianLinkChanged)->count();

        $component->call('add')->call('add');

        $this->assertSame(1, $parent->parentRecord->students()->count());
        $this->assertSame($auditsBefore + 1, AuditEvent::query()->where('action', AuditAction::GuardianLinkChanged)->count());
    }

    public function test_a_linked_learner_can_be_unlinked(): void
    {
        $student = StudentRecord::factory()->create();
        $parent = $this->guardian();
        $parent->parentRecord->students()->attach($student->user_id);
        $this->authorized_user(['update parent']);

        Livewire::test(AssignStudentsToParent::class, ['parent' => $parent])
            ->assertSee($student->user->name)
            ->call('remove', $student->user_id)
            ->assertDispatched('status-message');

        $this->assertSame(0, $parent->parentRecord->students()->count());
    }

    public function test_unlinking_a_guardian_cancels_what_they_still_asked_about_the_learner(): void
    {
        $student = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        $sibling = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        $parent = $this->guardian();
        $parent->parentRecord->students()->attach([$student->user_id, $sibling->user_id]);
        $open = $this->portalRequest($student, $parent, PortalRequestStatus::InReview);
        $answered = $this->portalRequest($student, $parent, PortalRequestStatus::Answered);
        $aboutTheSibling = $this->portalRequest($sibling, $parent, PortalRequestStatus::Submitted);
        $this->authorized_user(['update parent']);

        app(ChangeGuardianLink::class)->unlink($parent, $student->user, auth()->user());

        $this->assertSame(PortalRequestStatus::Cancelled, $open->fresh()->status);
        $this->assertSame('The guardian link to this learner ended.', $open->fresh()->response);
        $this->assertSame(PortalRequestStatus::Answered, $answered->fresh()->status);
        $this->assertSame(PortalRequestStatus::Submitted, $aboutTheSibling->fresh()->status);
    }

    public function test_a_guardian_with_a_child_at_another_school_keeps_that_link_out_of_view(): void
    {
        $otherSchool = School::factory()->create();
        $otherChild = StudentRecord::factory()->create(['school_id' => $otherSchool->id]);
        $child = StudentRecord::factory()->create();
        $parent = $this->guardian();
        $parent->parentRecord->students()->attach([$otherChild->user_id, $child->user_id]);
        $this->authorized_user(['update parent']);

        $component = Livewire::test(AssignStudentsToParent::class, ['parent' => $parent])
            ->assertSee($child->user->name)
            ->assertDontSee($otherChild->user->name)
            ->assertDontSee($otherChild->user->email);

        $component->call('remove', $otherChild->user_id)->assertNotFound();

        $this->assertSame(2, $parent->parentRecord->students()->count());
    }

    public function test_authorised_users_can_open_the_assign_students_page(): void
    {
        StudentRecord::factory()->create();

        $parent = User::factory()->create();
        $parent->parentRecord()->create(['user_id' => $parent->id]);
        $parent->assignRole('parent');

        $this->authorized_user(['update parent'])
            ->get("dashboard/parents/$parent->id/assign-student-to-parent")
            ->assertOk();
    }

    public function test_a_parent_cannot_be_assigned_a_student_from_another_school(): void
    {
        $otherSchool = School::factory()->create();
        $student = StudentRecord::factory()->create(['school_id' => $otherSchool->id]);
        $parent = $this->guardian();
        $this->authorized_user(['update parent']);

        Livewire::test(AssignStudentsToParent::class, ['parent' => $parent])
            ->set('studentId', $student->user_id)
            ->call('add')
            ->assertHasErrors('studentId');

        $this->assertThrows(
            fn () => app(ChangeGuardianLink::class)->link($parent, $student->user, auth()->user()),
            InvalidValueException::class,
        );
        $this->assertDatabaseMissing('parent_record_user', [
            'parent_record_id' => $parent->parentRecord->id,
            'user_id' => $student->user_id,
        ]);
    }

    private function portalRequest(StudentRecord $enrollment, User $requester, PortalRequestStatus $status): PortalRequest
    {
        return PortalRequest::create([
            'school_id' => $enrollment->school_id,
            'student_record_id' => $enrollment->id,
            'requested_by' => $requester->id,
            'type' => PortalRequestType::Document,
            'status' => $status,
            'subject' => 'Report card',
        ]);
    }

    /**
     * Make a guardian of the working school.
     */
    private function guardian(): User
    {
        $parent = User::factory()->create();
        $parent->parentRecord()->create(['user_id' => $parent->id]);
        $parent->assignRole('parent');

        return $parent;
    }
}
