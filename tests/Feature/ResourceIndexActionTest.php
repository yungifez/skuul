<?php

namespace Tests\Feature;

use App\Livewire\ListStudentsTable;
use App\Models\StudentRecord;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ResourceIndexActionTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_resource_index_shows_its_authorized_create_action(): void
    {
        $this->authorized_user(['read student', 'create student'])
            ->get(route('students.index'))
            ->assertOk()
            ->assertSee('Add student')
            ->assertSee('No students yet')
            ->assertSee('data-slot="data-table"', false)
            ->assertSee('Search rows...')
            ->assertSee('Admission #')
            ->assertSee('data-resource-create-action="'.route('students.create').'"', false);
    }

    public function test_a_resource_index_hides_its_create_action_without_the_create_ability(): void
    {
        $this->authorized_user(['read student'])
            ->get(route('students.index'))
            ->assertOk()
            ->assertDontSee('data-resource-create-action="'.route('students.create').'"', false);
    }

    public function test_resource_table_delete_actions_include_a_confirmation_message(): void
    {
        $this->authorized_user(['read student', 'delete student'])
            ->get(route('students.index'))
            ->assertOk()
            ->assertSee('data-confirm="Delete this student?"', false);
    }

    public function test_the_shared_status_display_renders_a_session_message(): void
    {
        $this->authorized_user(['read student']);

        $this->withSession(['success' => 'The student was saved.'])
            ->get(route('students.index'))
            ->assertOk()
            ->assertSee('The student was saved.', false)
            ->assertSee('id="status-display"', false)
            ->assertSee('top-24 z-[60]', false)
            ->assertSee('shadow-lg backdrop-blur', false)
            ->assertSee('bg-background/95', false)
            ->assertSee('bg-destructive/10', false);
    }

    public function test_the_student_table_searches_rows_on_the_server(): void
    {
        $matchingStudent = StudentRecord::factory()->create();
        $matchingStudent->user->update(['name' => 'Ada Lovelace']);

        $otherStudent = StudentRecord::factory()->create();
        $otherStudent->user->update(['name' => 'Grace Hopper']);

        $this->authorized_user(['read student']);

        Livewire::test(ListStudentsTable::class)
            ->call('updateTable', [
                'search' => 'Ada',
                'perPage' => 10,
                'page' => 1,
            ])
            ->assertSee('Ada Lovelace')
            ->assertDontSee('Grace Hopper');
    }
}
