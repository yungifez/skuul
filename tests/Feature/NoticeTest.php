<?php

namespace Tests\Feature;

use App\Enums\NoticeStatus;
use App\Livewire\ShowNotice;
use App\Models\Notice;
use App\Models\NoticeRecipient;
use App\Models\StudentRecord;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class NoticeTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    // test unauthorized user can not view all notices

    public function test_unauthorized_user_can_not_view_all_notices()
    {
        $this->unauthorized_user()
            ->get('dashboard/notices')
            ->assertForbidden();
    }

    // test authorized user can view all notices

    public function test_authorized_user_can_view_all_notices()
    {
        $this->authorized_user(['read notice'])
            ->get('dashboard/notices')
            ->assertSuccessful()
            ->assertSee('data-slot="data-table"', false)
            ->assertSee('Search rows...')
            ->assertSee('No notices yet');
    }

    public function test_an_ordinary_reader_cannot_open_a_draft_notice(): void
    {
        $this->authorized_user(['read notice']);
        $notice = Notice::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'status' => NoticeStatus::Draft,
        ]);

        $this->get(route('notices.show', $notice))->assertForbidden();
    }

    public function test_a_notice_manager_can_open_a_draft_notice(): void
    {
        $this->authorized_user(['read notice', 'update notice']);
        $notice = Notice::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'status' => NoticeStatus::Draft,
        ]);

        $this->get(route('notices.show', $notice))
            ->assertSuccessful()
            ->assertSee('Draft');
    }

    public function test_a_student_only_sees_active_notices_delivered_to_their_account(): void
    {
        $school = $this->workingSchool();
        $studentRecord = StudentRecord::factory()->create(['school_id' => $school->id]);
        $student = $studentRecord->user;
        $student->givePermissionTo('read notice');

        $visibleNotice = Notice::factory()->create([
            'school_id' => $school->id,
            'title' => 'Learner notice',
            'status' => NoticeStatus::Published,
            'active' => true,
            'start_date' => now()->subDay()->toDateString(),
            'stop_date' => now()->addDay()->toDateString(),
        ]);
        NoticeRecipient::create([
            'notice_id' => $visibleNotice->id,
            'user_id' => $student->id,
        ]);

        $hiddenNotice = Notice::factory()->create([
            'school_id' => $school->id,
            'title' => 'Other class notice',
            'status' => NoticeStatus::Published,
            'active' => true,
            'start_date' => now()->subDay()->toDateString(),
            'stop_date' => now()->addDay()->toDateString(),
        ]);
        NoticeRecipient::create([
            'notice_id' => $hiddenNotice->id,
            'user_id' => User::factory()->create()->id,
        ]);

        $this->actingAsMemberOf($school, $student)
            ->get(route('notices.index'))
            ->assertOk()
            ->assertSee('Learner notice')
            ->assertDontSee('Other class notice');

        $this->get(route('notices.show', $hiddenNotice))->assertForbidden();
    }

    // asser user cannot view create notice

    public function test_unauthorized_user_can_not_view_create_notice()
    {
        $this->unauthorized_user()
            ->get('dashboard/notices/create')
            ->assertForbidden();
    }

    // assert user can view create notice

    public function test_authorized_user_can_view_create_notice()
    {
        $this->authorized_user(['create notice'])
            ->get('dashboard/notices/create')
            ->assertSuccessful()
            ->assertSee('data-slot="editor"', false)
            ->assertSee('data-slot="select"', false)
            ->assertSee('name="audience[scope]"', false)
            ->assertSee('name="audience[academic_level_ids][]"', false)
            ->assertSee('name="audience[academic_cycle_section_ids][]"', false)
            ->assertSee('name="content"', false);
    }

    public function test_an_authorized_user_can_publish_a_draft_notice_from_its_screen(): void
    {
        $this->authorized_user(['read notice', 'update notice']);
        $notice = Notice::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'status' => NoticeStatus::Draft,
        ]);

        Livewire::test(ShowNotice::class, ['notice' => $notice])
            ->call('publishNotice')
            ->assertHasNoErrors()
            ->assertSee('Published');

        $this->assertSame(NoticeStatus::Published, $notice->fresh()->status);
    }

    public function test_publishing_an_archived_notice_explains_the_exception(): void
    {
        $this->authorized_user(['read notice', 'update notice']);
        $notice = Notice::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'status' => NoticeStatus::Archived,
        ]);

        Livewire::test(ShowNotice::class, ['notice' => $notice])
            ->call('publishNotice')
            ->assertHasErrors(['notice' => 'This notice cannot be published from its current state.']);

        $this->assertSame(NoticeStatus::Archived, $notice->fresh()->status);
    }

    public function test_notice_content_keeps_editor_formatting_and_removes_unsafe_markup(): void
    {
        $this->authorized_user(['create notice']);

        $this->post('dashboard/notices', [
            'title' => 'Formatted Notice',
            'content' => '<p>Bring <strong>your planner</strong>.</p><script>alert(1)</script><a href="javascript:alert(2)">Unsafe</a>',
            'start_date' => '2030-01-01',
            'stop_date' => '2030-01-02',
        ])->assertRedirect();

        $notice = Notice::query()->where('title', 'Formatted Notice')->firstOrFail();

        $this->assertSame(
            '<p>Bring <strong>your planner</strong>.</p>alert(1)<a>Unsafe</a>',
            trim($notice->content),
        );
    }

    public function test_notice_content_accepts_markdown_as_safe_html(): void
    {
        $this->authorized_user(['create notice']);

        $this->post('dashboard/notices', [
            'title' => 'Markdown Notice',
            'content' => "# Bring your planner\n\n- Pencil\n- Notebook",
            'start_date' => '2030-01-01',
            'stop_date' => '2030-01-02',
        ])->assertRedirect();

        $notice = Notice::query()->where('title', 'Markdown Notice')->firstOrFail();

        $this->assertSame(
            "<h1>Bring your planner</h1>\n<ul>\n<li>Pencil</li>\n<li>Notebook</li>\n</ul>",
            trim($notice->content),
        );
    }

    public function test_notice_content_keeps_only_safe_link_destinations(): void
    {
        $notice = Notice::factory()->create([
            'content' => <<<'HTML'
                <p><a href="https://example.com" title="ignored">External</a></p>
                <p><a href="mailto:office@example.com">Email</a></p>
                <p><a href="/dashboard/notices">Internal</a></p>
                <p><a href="#details">Anchor</a></p>
                <p><a href="//evil.example">Protocol relative</a></p>
                <p><a href="javascript:alert(1)">Script</a></p>
                HTML,
        ]);

        $this->assertSame(
            '<p><a href="https://example.com">External</a></p>'.
            '<p><a href="mailto:office@example.com">Email</a></p>'.
            '<p><a href="/dashboard/notices">Internal</a></p>'.
            '<p><a href="#details">Anchor</a></p>'.
            '<p><a>Protocol relative</a></p>'.
            '<p><a>Script</a></p>',
            preg_replace('/\s+/', '', (string) $notice->content),
        );
    }

    // assert unauthorized user can not create notice

    public function test_unauthorized_user_can_not_create_notice()
    {
        $this->unauthorized_user()
            ->post('dashboard/notices', [
                'title' => 'test',
                'content' => 'test',
                'start_date' => '2019-01-01',
                'stop_date' => '2019-01-02',
            ])->assertForbidden();
    }

    // assert user can create notice

    public function test_authorized_user_can_create_notice()
    {
        $response = $this->authorized_user(['create notice'])
            ->post('dashboard/notices', [
                'title' => 'Test Notice',
                'content' => 'Test Description',
                'start_date' => '2019-01-01',
                'stop_date' => '2019-01-02',
            ]);

        $response->assertRedirect() && $this->assertDatabaseHas('notices', [
            'title' => 'Test Notice',
            'content' => "<p>Test Description</p>\n",
            'start_date' => '2019-01-01',
            'stop_date' => '2019-01-02',
        ]);
    }

    // assert user can not create notice with invalid data

    public function test_authorized_user_can_not_create_notice_with_invalid_data()
    {
        $this->authorized_user(['create notice'])
            ->post('dashboard/notices', [
                'title' => '',
                'content' => 'Test Description',
                'start_date' => '2019-01-01',
                'stop_date' => '2019-01-02',
            ])
            ->assertSessionHasErrors();
    }

    // assert user can not create notice with invalid data

    public function test_authorized_user_can_not_create_notice_with_invalid_data_2()
    {
        $this->authorized_user(['create notice'])
            ->post('dashboard/notices', [
                'title' => 'Test Notice',
                'content' => '',
                'start_date' => '2019-01-01',
                'stop_date' => '2019-01-01',
            ])
            ->assertSessionHasErrors();
    }

    // assert user can not create notice with invalid data

    public function test_authorized_user_can_not_create_notice_with_invalid_data_3()
    {
        $this->authorized_user(['create notice'])
            ->post('dashboard/notices', [
                'title' => 'Test Notice',
                'content' => 'Test Description',
                'start_date' => '2019-01-01',
                'stop_date' => '2018-01-01',
            ])
            ->assertSessionHasErrors();
    }
}
