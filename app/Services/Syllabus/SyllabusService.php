<?php

namespace App\Services\Syllabus;

use App\Enums\CourseOfferingStatus;
use App\Enums\SyllabusStatus;
use App\Exceptions\InvalidValueException;
use App\Models\CourseOffering;
use App\Models\Syllabus;
use App\Models\SyllabusTopic;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SyllabusService
{
    /**
     * Start a draft syllabus for one exact offering.
     *
     * @param  array{name: string, description?: string|null, file?: UploadedFile|null, course_offering_id: int}  $data
     */
    public function createSyllabus(array $data): Syllabus
    {
        /** @var CourseOffering $courseOffering */
        $courseOffering = CourseOffering::inSchool()
            ->with('academicPeriod')
            ->findOrFail($data['course_offering_id']);

        if ($courseOffering->academicPeriod->isClosed()) {
            throw new InvalidValueException('Reopen the academic period before adding a syllabus.');
        }

        if ($courseOffering->status === CourseOfferingStatus::Archived) {
            throw new InvalidValueException('This course offering is archived. Choose an offering that is still in use.');
        }

        $file = isset($data['file']) ? $data['file']->store('syllabus', 'public') : null;

        return DB::transaction(fn (): Syllabus => Syllabus::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'file' => $file ?: null,
            'course_offering_id' => $courseOffering->id,
        ]));
    }

    /**
     * Change the details of a draft syllabus.
     *
     * @param  array{name: string, description?: string|null, file?: UploadedFile|null, remove_file?: bool}  $data
     */
    public function updateDraft(Syllabus $syllabus, array $data): Syllabus
    {
        $this->ensureDraft($syllabus);

        $previousFile = $syllabus->file;
        $file = $previousFile;

        if (isset($data['file'])) {
            $file = $data['file']->store('syllabus', 'public') ?: null;
        } elseif ($data['remove_file'] ?? false) {
            $file = null;
        }

        $syllabus->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'file' => $file,
        ]);

        if ($previousFile !== null && $previousFile !== $file) {
            $this->deleteFileIfUnused($previousFile);
        }

        return $syllabus;
    }

    /**
     * Add a topic to a draft syllabus, or change one it already holds.
     *
     * @param  array{title: string, week?: int|null, objectives?: string|null, content?: string|null, resources?: string|null}  $data
     */
    public function saveTopic(Syllabus $syllabus, array $data, ?SyllabusTopic $topic = null): SyllabusTopic
    {
        $this->ensureDraft($syllabus);

        $attributes = [
            'title' => $data['title'],
            'week' => $data['week'] ?? null,
            'objectives' => $data['objectives'] ?? null,
            'content' => $data['content'] ?? null,
            'resources' => $data['resources'] ?? null,
        ];

        if ($topic !== null) {
            if ($topic->syllabus_id !== $syllabus->id) {
                throw new InvalidValueException('That topic belongs to another syllabus.');
            }

            $topic->update($attributes);

            return $topic;
        }

        return $syllabus->topics()->create($attributes + [
            'position' => (int) SyllabusTopic::query()->where('syllabus_id', $syllabus->id)->max('position') + 1,
        ]);
    }

    /**
     * Remove a topic from a draft syllabus.
     */
    public function deleteTopic(Syllabus $syllabus, SyllabusTopic $topic): void
    {
        $this->ensureDraft($syllabus);

        if ($topic->syllabus_id !== $syllabus->id) {
            throw new InvalidValueException('That topic belongs to another syllabus.');
        }

        $topic->delete();
    }

    public function deleteSyllabus(Syllabus $syllabus): void
    {
        if ($syllabus->status !== SyllabusStatus::Draft) {
            throw new InvalidValueException('Published syllabus revisions are immutable. Create a revised draft instead.');
        }

        $file = $syllabus->file;
        $syllabus->delete();

        if ($file !== null) {
            $this->deleteFileIfUnused($file);
        }
    }

    /**
     * Refuse changes to a syllabus that is no longer a draft.
     */
    private function ensureDraft(Syllabus $syllabus): void
    {
        if ($syllabus->fresh()?->status !== SyllabusStatus::Draft) {
            throw new InvalidValueException('Only a draft syllabus can be changed. Create a revised draft instead.');
        }
    }

    /**
     * Delete a stored file once no syllabus revision points at it.
     *
     * A revision starts with the file of the syllabus it replaces, so one file
     * can back several rows. Deleting it while another row still names it
     * would break that row's download.
     */
    private function deleteFileIfUnused(string $file): void
    {
        if (Syllabus::query()->where('file', $file)->exists()) {
            return;
        }

        Storage::disk('public')->delete($file);
    }
}
