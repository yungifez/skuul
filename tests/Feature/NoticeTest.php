<?php

namespace Tests\Feature;

use App\Enums\NoticeStatus;
use App\Livewire\CreateNoticeForm;
use App\Livewire\ListNoticesTable;
use App\Livewire\ShowNotice;
use App\Models\AcademicLevel;
use App\Models\Notice;
use App\Models\NoticeRecipient;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Livewire\Features\SupportTesting\Testable;
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
            ->assertSee('wire:model="content"', false)
            ->assertSee('wire:model.live="audienceScope"', false);
    }

    public function test_the_message_editor_loads_from_whatever_host_serves_the_page(): void
    {
        URL::forceRootUrl('http://campus-two.test:8081');

        $this->authorized_user(['create notice'])
            ->get('dashboard/notices/create')
            ->assertSuccessful()
            ->assertSee('src="http://campus-two.test:8081/april-ui/editor', false)
            ->assertDontSee('http://localhost/april-ui/editor', false);
    }

    public function test_a_notice_is_saved_as_a_draft_and_opens_on_its_own_page(): void
    {
        $this->authorized_user(['create notice']);

        $component = Livewire::test(CreateNoticeForm::class)
            ->assertSet('startDate', now()->toDateString())
            ->assertSet('stopDate', now()->addWeeks(2)->toDateString())
            ->set('title', 'Sports day')
            ->set('content', '<p>Bring water.</p>')
            ->call('save')
            ->assertHasNoErrors();

        $notice = Notice::query()->where('title', 'Sports day')->sole();

        $component->assertRedirect(route('notices.show', $notice));
        $this->assertFalse($notice->isPublished());
        $this->assertEquals(['scope' => 'school', 'academic_level_ids' => [], 'academic_cycle_section_ids' => [], 'include_guardians' => false], $notice->audience);
    }

    public function test_a_notice_with_an_empty_message_is_refused(): void
    {
        $this->authorized_user(['create notice']);

        Livewire::test(CreateNoticeForm::class)
            ->set('title', 'Sports day')
            ->set('content', '<p><br></p>')
            ->call('save')
            ->assertHasErrors('content');

        $this->assertSame(0, Notice::query()->where('title', 'Sports day')->count());
    }

    public function test_a_notice_cannot_end_before_it_starts(): void
    {
        $this->authorized_user(['create notice']);

        Livewire::test(CreateNoticeForm::class)
            ->set('title', 'Sports day')
            ->set('content', '<p>Bring water.</p>')
            ->set('startDate', '2026-10-10')
            ->set('stopDate', '2026-10-09')
            ->call('save')
            ->assertHasErrors('stopDate');
    }

    public function test_a_notice_for_chosen_classes_needs_a_class(): void
    {
        $this->authorized_user(['create notice']);

        Livewire::test(CreateNoticeForm::class)
            ->set('title', 'Sports day')
            ->set('content', '<p>Bring water.</p>')
            ->set('audienceScope', 'class')
            ->call('save')
            ->assertHasErrors('academicLevelIds');
    }

    public function test_classes_chosen_before_switching_to_the_whole_school_are_dropped(): void
    {
        $this->authorized_user(['create notice']);
        $level = AcademicLevel::factory()->create(['school_id' => $this->workingSchool()->id]);

        Livewire::test(CreateNoticeForm::class)
            ->set('title', 'Sports day')
            ->set('content', '<p>Bring water.</p>')
            ->set('audienceScope', 'class')
            ->set('academicLevelIds', [(string) $level->id])
            ->set('audienceScope', 'school')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame([], Notice::query()->where('title', 'Sports day')->sole()->audience['academic_level_ids']);
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

        $this->writeNotice([
            'title' => 'Formatted Notice',
            'content' => '<p>Bring <strong>your planner</strong>.</p><script>alert(1)</script><a href="javascript:alert(2)">Unsafe</a>',
            'startDate' => '2030-01-01',
            'stopDate' => '2030-01-02',
        ])->assertHasNoErrors();

        $notice = Notice::query()->where('title', 'Formatted Notice')->firstOrFail();

        $this->assertSame(
            '<p>Bring <strong>your planner</strong>.</p>alert(1)<a>Unsafe</a>',
            trim($notice->content),
        );
    }

    public function test_notice_content_accepts_markdown_as_safe_html(): void
    {
        $this->authorized_user(['create notice']);

        $this->writeNotice([
            'title' => 'Markdown Notice',
            'content' => "# Bring your planner\n\n- Pencil\n- Notebook",
            'startDate' => '2030-01-01',
            'stopDate' => '2030-01-02',
        ])->assertHasNoErrors();

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

        $normalizeHtml = static fn (string $html): string => preg_replace('/>\s+</', '><', trim($html)) ?? trim($html);

        $this->assertSame(
            $normalizeHtml(
                '<p><a href="https://example.com">External</a></p>'.
                '<p><a href="mailto:office@example.com">Email</a></p>'.
                '<p><a href="/dashboard/notices">Internal</a></p>'.
                '<p><a href="#details">Anchor</a></p>'.
                '<p><a>Protocol relative</a></p>'.
                '<p><a>Script</a></p>',
            ),
            $normalizeHtml((string) $notice->content),
        );
    }

    public function test_unauthorized_user_can_not_create_notice()
    {
        $this->unauthorized_user();

        Livewire::test(CreateNoticeForm::class)->assertForbidden();
    }

    public function test_authorized_user_can_create_notice()
    {
        $this->authorized_user(['create notice']);

        $this->writeNotice([
            'title' => 'Test Notice',
            'content' => 'Test Description',
            'startDate' => '2019-01-01',
            'stopDate' => '2019-01-02',
        ])->assertHasNoErrors();

        $this->assertDatabaseHas('notices', [
            'title' => 'Test Notice',
            'content' => "<p>Test Description</p>\n",
            'start_date' => '2019-01-01',
            'stop_date' => '2019-01-02',
        ]);
    }

    public function test_authorized_user_can_not_create_notice_with_invalid_data()
    {
        $this->authorized_user(['create notice']);

        $this->writeNotice(['title' => '  '])->assertHasErrors('title');
        $this->writeNotice(['content' => ''])->assertHasErrors('content');
        $this->writeNotice(['startDate' => '2019-01-01', 'stopDate' => '2018-01-01'])->assertHasErrors('stopDate');
        $this->writeNotice(['stopDate' => ''])->assertHasErrors('stopDate');

        $this->assertSame(0, Notice::query()->where('title', 'Test Notice')->count());
    }

    /**
     * Write a notice through the form.
     *
     * @param  array<string, string>  $values
     */
    private function writeNotice(array $values): Testable
    {
        $component = Livewire::test(CreateNoticeForm::class);

        foreach ($values + ['title' => 'Test Notice', 'content' => 'Test Description'] as $property => $value) {
            $component->set($property, $value);
        }

        return $component->call('save');
    }

    public function test_a_notice_is_deleted_from_the_table(): void
    {
        $notice = Notice::factory()->create(['school_id' => $this->workingSchool()->id]);
        $this->authorized_user(['read notice', 'delete notice']);

        Livewire::test(ListNoticesTable::class)
            ->assertSeeHtml('$wire.call(&quot;deleteNotice&quot;, row.id)')
            ->call('deleteNotice', $notice->id)
            ->assertDispatched('status-message', type: 'success');

        $this->assertModelMissing($notice);
    }

    public function test_deleting_a_notice_needs_permission(): void
    {
        $notice = Notice::factory()->create(['school_id' => $this->workingSchool()->id]);
        $this->authorized_user(['read notice']);

        Livewire::test(ListNoticesTable::class)
            ->call('deleteNotice', $notice->id)
            ->assertForbidden();

        $this->assertModelExists($notice);
    }

    public function test_another_schools_notice_cannot_be_deleted(): void
    {
        $theirs = Notice::factory()->create(['school_id' => School::factory()->create()->id]);
        $this->authorized_user(['read notice', 'delete notice']);

        try {
            Livewire::test(ListNoticesTable::class)->call('deleteNotice', $theirs->id);
            $this->fail('Another school\'s notice was reached.');
        } catch (ModelNotFoundException) {
        }

        $this->assertModelExists($theirs);
    }

    public function test_the_classic_notice_write_routes_are_gone(): void
    {
        $notice = Notice::factory()->create(['school_id' => $this->workingSchool()->id]);
        $this->authorized_user(['read notice', 'update notice', 'delete notice']);

        $this->delete("/dashboard/notices/{$notice->id}")->assertStatus(405);
        $this->get("/dashboard/notices/{$notice->id}/edit")->assertNotFound();
        $this->assertFalse(Route::has('notices.destroy'));
        $this->assertFalse(Route::has('notices.update'));
        $this->assertModelExists($notice);
    }
}
