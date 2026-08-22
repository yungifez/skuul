<?php

namespace Tests\Feature;

use App\Enums\Feature;
use App\Enums\NoticeRecipientState;
use App\Enums\NoticeStatus;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\Notice;
use App\Models\NoticeRecipient;
use App\Models\ParentRecord;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NoticeAttachmentTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_notice_attachment_is_stored_on_the_private_disk(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        $this->authorized_user(['create notice']);

        $this->post(route('notices.store'), [
            'title'      => 'Family guide',
            'content'    => 'Please read the guide before the first day.',
            'start_date' => now()->toDateString(),
            'stop_date'  => now()->addWeek()->toDateString(),
            'attachment' => UploadedFile::fake()->create('family-guide.pdf', 120, 'application/pdf'),
        ])->assertRedirect();

        $notice = Notice::query()->where('title', 'Family guide')->latest('id')->firstOrFail();

        $this->assertSame('local', $notice->attachment_disk);
        $this->assertSame('family-guide.pdf', $notice->attachment_name);
        $this->assertSame('application/pdf', $notice->attachment_mime_type);
        $this->assertNotNull($notice->attachment_size);
        $this->assertNotNull($notice->attachment);
        Storage::disk('local')->assertExists($notice->attachment);
        Storage::disk('public')->assertMissing($notice->attachment);
    }

    public function test_an_authorized_staff_member_can_download_a_managed_notice_attachment(): void
    {
        Storage::fake('local');
        $this->authorized_user(['read notice']);
        $notice = $this->noticeWithAttachment();

        $this->get(route('notices.attachments.download', $notice))
            ->assertOk()
            ->assertDownload('family-guide.pdf');
    }

    public function test_a_guardian_can_download_an_attachment_sent_to_their_child_when_the_portal_is_open(): void
    {
        Storage::fake('local');
        $this->unauthorized_user();
        features()->enable(Feature::Portal);
        $student = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        $studentRecord = StudentRecord::query()->findOrFail($student->getKey());
        $guardian = User::factory()->create();
        $this->assertInstanceOf(User::class, $guardian);
        $this->assertNotNull($studentRecord->user_id);
        $parentRecord = $guardian->parentRecord()->create(['user_id' => $guardian->id]);
        $this->assertInstanceOf(ParentRecord::class, $parentRecord);
        $parentRecord->students()->attach($studentRecord->user_id);
        $notice = $this->noticeWithAttachment();
        NoticeRecipient::create([
            'notice_id'    => $notice->id,
            'user_id'      => $studentRecord->user_id,
            'state'        => NoticeRecipientState::Delivered,
            'delivered_at' => now(),
        ]);

        $this->actingAs($guardian)
            ->get(route('notices.attachments.download', $notice))
            ->assertOk()
            ->assertDownload('family-guide.pdf');
    }

    public function test_a_stranger_cannot_download_a_managed_notice_attachment(): void
    {
        Storage::fake('local');
        $this->unauthorized_user();
        $notice = $this->noticeWithAttachment();
        $stranger = User::factory()->create();
        $this->assertInstanceOf(User::class, $stranger);

        $this->actingAs($stranger)
            ->get(route('notices.attachments.download', $notice))
            ->assertForbidden();
    }

    public function test_a_notice_cannot_target_a_home_section_from_another_school(): void
    {
        $this->authorized_user(['create notice']);
        $otherSchool = School::query()->findOrFail(School::factory()->create()->getKey());
        $otherAcademicYear = AcademicYear::query()->findOrFail(AcademicYear::factory()->create([
            'school_id' => $otherSchool->id,
        ])->getKey());
        $otherAcademicLevel = AcademicLevel::query()->findOrFail(AcademicLevel::factory()->create([
            'school_id' => $otherSchool->id,
        ])->getKey());
        $otherSection = AcademicCycleSection::query()->findOrFail(AcademicCycleSection::factory()->create([
            'school_id'         => $otherSchool->id,
            'academic_year_id'  => $otherAcademicYear->id,
            'academic_level_id' => $otherAcademicLevel->id,
        ])->getKey());

        $this->post(route('notices.store'), [
            'title'      => 'Family guide',
            'content'    => 'Please read the guide before the first day.',
            'start_date' => now()->toDateString(),
            'stop_date'  => now()->addWeek()->toDateString(),
            'audience'   => ['academic_cycle_section_ids' => [$otherSection->id]],
        ])->assertSessionHasErrors('audience.academic_cycle_section_ids.0');
    }

    /** Create a current notice with a private attachment. */
    private function noticeWithAttachment(): Notice
    {
        $path = 'notice-attachments/'.$this->workingSchool()->id.'/family-guide.pdf';
        Storage::disk('local')->put($path, 'Family guide content.');

        return Notice::create([
            'title'                => 'Family guide',
            'content'              => 'Please read the guide before the first day.',
            'start_date'           => now()->toDateString(),
            'stop_date'            => now()->addWeek()->toDateString(),
            'school_id'            => $this->workingSchool()->id,
            'status'               => NoticeStatus::Published,
            'active'               => true,
            'attachment'           => $path,
            'attachment_disk'      => 'local',
            'attachment_name'      => 'family-guide.pdf',
            'attachment_mime_type' => 'application/pdf',
            'attachment_size'      => Storage::disk('local')->size($path),
        ]);
    }
}
