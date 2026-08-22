<?php

namespace Tests\Feature;

use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            ->assertSee('data-resource-create-action="'.route('students.create').'"', false);
    }

    public function test_a_resource_index_hides_its_create_action_without_the_create_ability(): void
    {
        $this->authorized_user(['read student'])
            ->get(route('students.index'))
            ->assertOk()
            ->assertDontSee('data-resource-create-action="'.route('students.create').'"', false);
    }
}
