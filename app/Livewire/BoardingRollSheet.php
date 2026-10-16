<?php

namespace App\Livewire;

use App\Actions\Boarding\RecordBoardingRoll;
use App\Enums\BoardingRollEntryStatus;
use App\Exceptions\InvalidValueException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\BoardingRoll;
use App\Models\BoardingRollEntry;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Take one boarding roll.
 *
 * Two staff often take one roll together, a floor each. A save sends only the
 * rows this person changed, and a row somebody else answered since this sheet
 * was read is refused once, so nobody's answer is lost without a word.
 */
class BoardingRollSheet extends Component
{
    use DispatchesStatusNotifications;

    #[Locked]
    public BoardingRoll $roll;

    /**
     * @var array<int, array{status: string, location: string, note: string}>
     */
    public array $answers = [];

    /**
     * The answers as this sheet last read them.
     *
     * @var array<int, array{status: string, location: string, note: string}>
     */
    #[Locked]
    public array $original = [];

    /**
     * Rows whose newer answer this person has seen and may now replace.
     *
     * @var list<int>
     */
    #[Locked]
    public array $overwrites = [];

    public function mount(BoardingRoll $roll): void
    {
        Gate::authorize('read boarding');
        abort_unless($roll->school_id === current_school_id(), 404);

        $this->roll = $roll;
        $this->readAnswers();
    }

    public function discard(): void
    {
        $this->resetValidation();
        $this->readAnswers();
    }

    public function save(RecordBoardingRoll $recordBoardingRoll): bool
    {
        Gate::authorize('manage boarding');
        $this->resetValidation();

        if ($this->roll->refresh()->isComplete()) {
            $this->readAnswers();
            $this->addError('roll', 'Someone completed this roll while you worked on it. Nothing was saved.');

            return false;
        }

        try {
            $this->validate([
                'answers' => ['array'],
                'answers.*.status' => ['required', Rule::enum(BoardingRollEntryStatus::class)],
                'answers.*.location' => ['nullable', 'string', 'max:150'],
                'answers.*.note' => ['nullable', 'string', 'max:1000'],
            ], [], [
                'answers.*.status' => 'answer',
                'answers.*.location' => 'where',
                'answers.*.note' => 'note',
            ]);
        } catch (ValidationException $exception) {
            // The row shows one message, so fold each field's onto its row.
            foreach ($exception->errors() as $key => $messages) {
                $this->addError((string) preg_replace('/^(answers\.\d+)\..+$/', '$1', $key), $messages[0]);
            }

            return false;
        }

        $entries = $this->entries()->keyBy('id');
        $toSave = [];

        foreach ($this->changedEntryIds() as $entryId) {
            $entry = $entries->get($entryId);

            if ($entry === null) {
                continue;
            }

            $current = $this->valuesOf($entry);

            if ($current !== $this->original[$entryId] && !in_array($entryId, $this->overwrites, true)) {
                $this->original[$entryId] = $current;
                $this->overwrites[] = $entryId;
                $this->addError("answers.$entryId", 'Someone else recorded '.$this->describe($current).' since you opened the roll. Save again to replace it.');

                continue;
            }

            $answer = $this->answers[$entryId];
            $toSave[] = [
                'id' => $entryId,
                'status' => $answer['status'],
                'location' => trim($answer['location']) === '' ? null : trim($answer['location']),
                'note' => trim($answer['note']) === '' ? null : trim($answer['note']),
            ];
        }

        if ($toSave !== []) {
            try {
                $recordBoardingRoll->record($this->roll, $toSave, actor: auth()->user());
            } catch (InvalidValueException $exception) {
                $this->roll->refresh();
                $this->addError('roll', $exception->getMessage());

                return false;
            }
        }

        $this->takeInOtherAnswers(array_column($toSave, 'id'));

        if ($this->getErrorBag()->isNotEmpty()) {
            $this->notify(count($toSave) === 0 ? 'Nothing was saved. Check the rows in red.' : count($toSave).' saved. Check the rows in red.', 'danger');

            return false;
        }

        $this->notify(match (count($toSave)) {
            0 => 'Nothing changed.',
            1 => 'Answer saved.',
            default => count($toSave).' answers saved.',
        });

        return true;
    }

    public function complete(RecordBoardingRoll $recordBoardingRoll): void
    {
        Gate::authorize('manage boarding');

        if ($this->changedEntryIds() !== [] && !$this->save($recordBoardingRoll)) {
            return;
        }

        try {
            $this->roll = $recordBoardingRoll->record($this->roll, [], true, auth()->user());
        } catch (InvalidValueException $exception) {
            $this->roll->refresh();
            $this->readAnswers();
            $this->addError('roll', $exception->getMessage());

            return;
        }

        $this->readAnswers();
        $unaccounted = collect($this->answers)->where('status', BoardingRollEntryStatus::Unaccounted->value)->count();

        $unaccounted === 0
            ? $this->notify('The roll is complete.')
            : $this->notify('The roll is complete with '.$unaccounted.' '.str('boarder')->plural($unaccounted).' unaccounted for.', 'danger');
    }

    public function render(): View
    {
        $statuses = collect($this->answers)->countBy('status');

        return view('livewire.boarding-roll-sheet', [
            'entries' => $this->entries(),
            'statuses' => BoardingRollEntryStatus::cases(),
            'canManage' => Gate::allows('manage boarding') && !$this->roll->isComplete(),
            'changed' => $this->changedEntryIds(),
            'answered' => count($this->answers) - $statuses->get(BoardingRollEntryStatus::NotRecorded->value, 0),
            'unaccounted' => $statuses->get(BoardingRollEntryStatus::Unaccounted->value, 0),
        ]);
    }

    /**
     * Read every answer afresh.
     */
    private function readAnswers(): void
    {
        $this->answers = [];

        foreach ($this->entries() as $entry) {
            $this->answers[$entry->id] = $this->valuesOf($entry);
        }

        $this->original = $this->answers;
        $this->overwrites = [];
    }

    /**
     * Show the answers others saved on rows this person has not touched.
     *
     * @param  list<int>  $savedIds
     */
    private function takeInOtherAnswers(array $savedIds): void
    {
        $changed = $this->changedEntryIds();

        foreach ($this->entries() as $entry) {
            $isOwnSave = in_array($entry->id, $savedIds, true);

            if ($isOwnSave || (!in_array($entry->id, $changed, true) && !$this->getErrorBag()->has("answers.{$entry->id}"))) {
                $this->answers[$entry->id] = $this->original[$entry->id] = $this->valuesOf($entry);
                $this->overwrites = array_values(array_diff($this->overwrites, [$entry->id]));
            }
        }
    }

    /**
     * @return list<int>
     */
    private function changedEntryIds(): array
    {
        return array_values(array_filter(
            array_map('intval', array_keys($this->answers)),
            fn (int $entryId): bool => isset($this->original[$entryId]) && $this->answers[$entryId] !== $this->original[$entryId],
        ));
    }

    /**
     * @return array{status: string, location: string, note: string}
     */
    private function valuesOf(BoardingRollEntry $entry): array
    {
        return [
            'status' => $entry->status->value,
            'location' => (string) $entry->location,
            'note' => (string) $entry->note,
        ];
    }

    /**
     * Say what an answer holds, in the words the sheet shows.
     *
     * @param  array{status: string, location: string, note: string}  $values
     */
    private function describe(array $values): string
    {
        $status = BoardingRollEntryStatus::from($values['status'])->label();

        return $values['location'] === '' ? "\"$status\"" : "\"$status, {$values['location']}\"";
    }

    /**
     * @return Collection<int, BoardingRollEntry>
     */
    private function entries(): Collection
    {
        return $this->roll->entries()
            ->with('studentRecord.user:id,name')
            ->get()
            ->sortBy(fn (BoardingRollEntry $entry): string => mb_strtolower($entry->studentRecord->user->name ?? (string) $entry->studentRecord->admission_number))
            ->values();
    }
}
