<?php

namespace App\Livewire;

use App\Actions\Boarding\StartBoardingRoll;
use App\Enums\BoardingRollType;
use App\Exceptions\InvalidValueException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\BoardingRoll;
use App\Models\Dormitory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Show the rolls of one day for every house, and start the ones still due.
 */
class BoardingRollBoard extends Component
{
    use DispatchesStatusNotifications;

    #[Url(as: 'taken_on', except: '')]
    public string $date = '';

    public function mount(): void
    {
        Gate::authorize('read boarding');
    }

    public function start(int $houseId, string $type, StartBoardingRoll $startBoardingRoll): void
    {
        Gate::authorize('manage boarding');

        $rollType = BoardingRollType::tryFrom($type);
        $day = $this->day();

        if ($rollType === null || $day->isFuture()) {
            $this->notify('A roll can only be taken for today or a day before.', 'danger');

            return;
        }

        $house = Dormitory::query()->inSchool()->active()->find($houseId);

        if ($house === null) {
            $this->notify('This house is no longer open. Reload the page.', 'danger');

            return;
        }

        try {
            $roll = $startBoardingRoll->start($house, $rollType, $day, auth()->user());
        } catch (InvalidValueException $exception) {
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        $this->redirectRoute('boarding-rolls.show', $roll);
    }

    public function render(): View
    {
        $day = $this->day();

        return view('livewire.boarding-roll-board', [
            'day' => $day,
            'isFuture' => $day->isFuture(),
            'houses' => Dormitory::query()->inSchool()->active()->orderBy('name')->get(['id', 'name']),
            'rolls' => BoardingRoll::query()
                ->inSchool()
                ->onDate($day->toDateString())
                ->withCount(['entries', 'entries as unanswered_count' => fn ($query) => $query->where('status', 'not_recorded')])
                ->get()
                ->groupBy('dormitory_id'),
            'types' => BoardingRollType::cases(),
            'canManage' => Gate::allows('manage boarding'),
        ]);
    }

    /**
     * The day the board shows. A date that is no date shows today.
     */
    private function day(): Carbon
    {
        $isDate = $this->date !== '' && Validator::make(['date' => $this->date], ['date' => ['date_format:Y-m-d', 'date']])->passes();

        return $isDate ? Carbon::parse($this->date)->startOfDay() : school_today();
    }
}
