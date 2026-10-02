<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Enums\AdmissionWaitlistStatus;
use App\Enums\CampusMoveStatus;
use App\Enums\DataCategory;
use App\Enums\DataSharingStatus;
use App\Enums\ImportStatus;
use App\Enums\PortalRequestStatus;
use App\Enums\ReportStatus;
use App\Models\AccountInvitation;
use App\Models\AdmissionWaitlistEntry;
use App\Models\CampusMoveRequest;
use App\Models\DataSharingRequest;
use App\Models\ImportBatch;
use App\Models\PortalRequest;
use App\Models\ReportRun;
use App\Models\StudentRecord;
use App\Services\School\SchoolContext;
use Database\Seeders\Demo\DemoSchool;
use Database\Seeders\DemoSchoolSeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * The demo fills the office screens with records a visitor can read.
 */
class DemoAdministrationTest extends TestCase
{
    use RefreshDatabase;

    private DemoSchool $demo;

    protected function setUp(): void
    {
        parent::setUp();

        $seeder = new DemoSchoolSeeder;
        Model::unguarded(fn () => $seeder->run());
        $this->demo = $seeder->demoSchool();
    }

    public function test_the_office_screens_show_an_invitation_a_queue_an_import_and_an_export(): void
    {
        $invitation = AccountInvitation::query()->with('user')->whereRelation('user', 'name', 'Jasmine Wright')->sole();
        $this->assertSame('Jasmine Wright', $invitation->user->name);
        $this->assertSame(AccountStatus::Invited, $invitation->user->account_status);
        $this->assertNull($invitation->accepted_at);
        $this->assertTrue($invitation->created_at->isYesterday());
        $this->assertTrue($invitation->expires_at->isFuture());

        $queue = AdmissionWaitlistEntry::query()->where('academic_cycle_section_id', $this->demo->sections['9B']->id)->with('candidate')->orderBy('position')->get();
        $this->assertSame(['Jacob Morales', 'Maya Chen'], $queue->pluck('candidate.name')->all());
        $this->assertSame([AdmissionWaitlistStatus::Offered, AdmissionWaitlistStatus::Pending], $queue->pluck('status')->all());

        $import = ImportBatch::query()->where('source_name', 'spring-transfers.csv')->sole();
        $this->assertSame(ImportStatus::Checked, $import->status);
        $this->assertSame([3, 2], [$import->valid_count, $import->invalid_count]);
        $errors = $import->rows()->whereNotNull('errors')->get()->pluck('errors')->flatten()->implode(' ');
        $this->assertStringContainsString('email field is required', $errors);
        $this->assertStringContainsString('birthday', $errors);

        $export = ReportRun::query()->where('type', 'class-list')->where('requested_by', $this->demo->schoolAdmin->id)->sole();
        $this->assertSame(ReportStatus::Ready, $export->status);
        $this->assertTrue(Storage::disk('local')->exists($export->file_path));

        $office = $this->asPlatformAdmin();
        $office->get(route('users.invitations.index'))->assertOk()->assertSee('Jasmine Wright');
        $office->get(route('admissions.waitlist.index'))->assertOk()->assertSee('Jacob Morales')->assertSee('Maya Chen');
        $office->get(route('imports.show', $import))->assertOk()->assertSee('isabel.romero.rhs')->assertSee('Hailey Sutton');
        $office->get(route('reports.index'))->assertOk()->assertSee(route('reports.download', $export), false);
    }

    public function test_the_campuses_share_a_pending_move_and_two_record_requests(): void
    {
        $move = CampusMoveRequest::query()->whereRelation('studentRecord.user', 'name', 'Sofia Ramirez')->with('studentRecord.user')->sole();
        $this->assertSame(CampusMoveStatus::Requested, $move->status);
        $this->assertSame($this->demo->sisterCampus->id, $move->from_school_id);
        $this->assertSame($this->demo->campus->id, $move->to_school_id);
        $this->assertSame('Sofia Ramirez', $move->studentRecord->user->name);

        $asked = DataSharingRequest::query()->where('requesting_school_id', $this->demo->campus->id)->sole();
        $this->assertSame(DataSharingStatus::Requested, $asked->status);
        $this->assertSame([DataCategory::AcademicResults->value, DataCategory::Attendance->value, DataCategory::Health->value], $asked->categories);

        $received = DataSharingRequest::query()->where('requesting_school_id', $this->demo->sisterCampus->id)->sole();
        $this->assertSame(DataSharingStatus::Approved, $received->status);

        $office = $this->asPlatformAdmin();
        $office->get(route('campus-moves.index'))
            ->assertOk()
            ->assertSee('Sofia Ramirez')
            ->assertSee('Maple Grove Middle School');
        $office->get(route('data-sharing-requests.index'))
            ->assertOk()
            ->assertSee('Maple Grove Middle School');
        $office->get(route('organizations.index'))
            ->assertOk()
            ->assertSee('Riverside Unified School District');
    }

    public function test_the_demo_parent_reads_an_open_and_an_answered_request(): void
    {
        $requests = PortalRequest::query()
            ->where('requested_by', $this->demo->demoParent->id)
            ->whereIn('subject', ['Enrollment letter for a summer program', "Update Ethan's emergency contact"])
            ->orderBy('id')
            ->get();
        $this->assertSame([PortalRequestStatus::Answered, PortalRequestStatus::Submitted], $requests->pluck('status')->all());
        $this->assertNotNull($requests->first()->response);

        $enrollment = StudentRecord::query()
            ->where('school_id', $this->demo->campus->id)
            ->where('user_id', $this->demo->demoStudent->id)
            ->sole();

        $this->actingAs($this->demo->demoParent)
            ->withSession([SchoolContext::SESSION_KEY => $this->demo->campus->id])
            ->get(route('portal.requests.index', $enrollment))
            ->assertOk()
            ->assertSee("Update Ethan's emergency contact")
            ->assertSee('The letter is signed and ready');
    }

    /**
     * Sign in as the district administrator, working at the high school.
     */
    private function asPlatformAdmin(): static
    {
        return $this->actingAs($this->demo->platformAdmin)->withSession([SchoolContext::SESSION_KEY => $this->demo->campus->id]);
    }
}
