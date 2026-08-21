<?php

namespace App\Services\Import;

use App\Contracts\Importer;
use App\Enums\ImportRowState;
use App\Enums\ImportStatus;
use App\Exceptions\InvalidValueException;
use App\Models\ImportBatch;
use App\Models\ImportedRecord;
use App\Models\ImportRow;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

/**
 * Check an import, show what it will do, then write it.
 *
 * Nothing is written until the school asks for it, and a row that fails never
 * stops the rows around it. A row that names an outside identifier writes the
 * same record every time, so the same file can be imported twice.
 */
class ImportRunner
{
    public function __construct(private ImportRegistry $registry) {}

    /**
     * Start an import and check every row.
     *
     * @param  array<int, array<string, mixed>>  $rows
     *
     * @throws InvalidValueException when the file is missing a required column
     */
    public function stage(string $type, array $rows, ?string $sourceName = null, ?User $actor = null): ImportBatch
    {
        $importer = $this->registry->get($type);

        $this->failIfColumnsAreMissing($importer, $rows);

        return DB::transaction(function () use ($importer, $type, $rows, $sourceName, $actor): ImportBatch {
            $batch = ImportBatch::create([
                'school_id' => current_school_id(),
                'type' => $type,
                'source_name' => $sourceName,
                'created_by' => $actor === null ? auth()->id() : $actor->id,
            ]);

            $valid = 0;
            $invalid = 0;

            foreach ($rows as $index => $row) {
                $errors = $this->check($importer, $row);
                $isValid = $errors === [];
                $isValid ? $valid++ : $invalid++;

                ImportRow::create([
                    'import_batch_id' => $batch->id,
                    // Line one is the heading, so the first row of data is line two.
                    'line_number' => $index + 2,
                    'source_id' => $this->sourceIdOf($row),
                    'payload' => $row,
                    'state' => $isValid ? ImportRowState::Valid : ImportRowState::Invalid,
                    'errors' => $errors === [] ? null : $errors,
                ]);
            }

            $batch->forceFill([
                'status' => ImportStatus::Checked,
                'row_count' => count($rows),
                'valid_count' => $valid,
                'invalid_count' => $invalid,
            ])->save();

            return $batch;
        });
    }

    /**
     * Write the rows that passed the check.
     *
     * @throws InvalidValueException when the import was already written or dropped
     */
    public function apply(ImportBatch $batch): ImportBatch
    {
        if (!$batch->status->canBeApplied()) {
            throw new InvalidValueException('This import was already finished.');
        }

        $importer = $this->registry->get($batch->type);
        $applied = 0;

        foreach ($batch->rows()->ready()->get() as $row) {
            try {
                $subject = DB::transaction(fn (): Model => $this->write($importer, $batch, $row));
            } catch (Throwable $exception) {
                $row->forceFill([
                    'state' => ImportRowState::Invalid,
                    'errors' => [$exception->getMessage()],
                ])->save();

                continue;
            }

            $row->forceFill([
                'state' => ImportRowState::Applied,
                'subject_type' => $subject->getMorphClass(),
                'subject_id' => $subject->getKey(),
            ])->save();

            $applied++;
        }

        $batch->forceFill([
            'status' => ImportStatus::Applied,
            'applied_count' => $applied,
            'invalid_count' => $batch->rows()->broken()->count(),
            'applied_at' => now(),
        ])->save();

        return $batch;
    }

    /**
     * Drop an import without writing it.
     */
    public function cancel(ImportBatch $batch): ImportBatch
    {
        if (!$batch->status->canBeApplied()) {
            throw new InvalidValueException('This import was already finished.');
        }

        $batch->forceFill(['status' => ImportStatus::Cancelled])->save();

        return $batch;
    }

    /**
     * Write one row and remember what it wrote.
     */
    private function write(Importer $importer, ImportBatch $batch, ImportRow $row): Model
    {
        $sourceId = $row->source_id;
        $link = null;

        if ($sourceId !== null) {
            $link = ImportedRecord::query()
                ->where('school_id', $batch->school_id)
                ->where('type', $batch->type)
                ->where('source_id', $sourceId)
                ->first();
        }

        $subject = $importer->apply($row->payload, $link?->subject);

        if ($sourceId !== null) {
            ImportedRecord::updateOrCreate(
                ['school_id' => $batch->school_id, 'type' => $batch->type, 'source_id' => $sourceId],
                ['subject_type' => $subject->getMorphClass(), 'subject_id' => $subject->getKey()],
            );
        }

        return $subject;
    }

    /**
     * Get what one row got wrong.
     *
     * @param  array<string, mixed>  $row
     * @return array<int, string>
     */
    private function check(Importer $importer, array $row): array
    {
        $validator = Validator::make($row, $importer->rules());

        return $validator->fails() ? array_values($validator->errors()->all()) : [];
    }

    /**
     * Get the outside identifier a row names, when it names one.
     *
     * @param  array<string, mixed>  $row
     */
    private function sourceIdOf(array $row): ?string
    {
        $sourceId = $row['source_id'] ?? null;

        return $sourceId === null || $sourceId === '' ? null : (string) $sourceId;
    }

    /**
     * Refuse a file that is missing a column the import needs.
     *
     * @param  array<int, array<string, mixed>>  $rows
     *
     * @throws InvalidValueException when a required column is missing
     */
    private function failIfColumnsAreMissing(Importer $importer, array $rows): void
    {
        if ($rows === []) {
            throw new InvalidValueException('The file has no rows.');
        }

        $columns = array_keys($rows[0]);
        $missing = array_diff($importer->requiredColumns(), $columns);

        if ($missing !== []) {
            throw new InvalidValueException('The file is missing these columns: '.implode(', ', $missing).'.');
        }
    }
}
