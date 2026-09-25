<?php

namespace App\Livewire;

use App\Enums\CalendarEventType;
use App\Models\CalendarEvent;
use App\Services\Calendar\CalendarEventDirectory as CalendarEventDirectoryService;
use App\Traits\ReadsCalendarMonths;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View as ViewFactory;
use Livewire\Attributes\Url;
use Livewire\Component;

class CalendarEventDirectory extends Component
{
    use ReadsCalendarMonths;

    #[Url(except: '')]
    public string $month = '';

    #[Url(except: '')]
    public string $type = '';

    #[Url(as: 'drafts', except: false)]
    public bool $draftsOnly = false;

    protected CalendarEventDirectoryService $directory;

    public function boot(CalendarEventDirectoryService $directory): void
    {
        Gate::authorize('viewAny', CalendarEvent::class);

        $this->directory = $directory;
    }

    public function mount(): void
    {
        $this->normalizeFilters();
    }

    public function updatedMonth(): void
    {
        $this->month = $this->normalizeMonth($this->month);
    }

    public function updatedType(): void
    {
        $this->type = $this->normalizedType($this->type);
    }

    public function previousMonth(): void
    {
        $this->month = Carbon::createFromFormat('Y-m', $this->month)->subMonthNoOverflow()->format('Y-m');
    }

    public function nextMonth(): void
    {
        $this->month = Carbon::createFromFormat('Y-m', $this->month)->addMonthNoOverflow()->format('Y-m');
    }

    public function showCurrentMonth(): void
    {
        $this->month = now()->format('Y-m');
    }

    public function showDrafts(): void
    {
        $this->draftsOnly = true;
    }

    public function clearFilters(): void
    {
        $this->type = '';
        $this->draftsOnly = false;
    }

    public function render(): View
    {
        $calendarMonth = Carbon::createFromFormat('Y-m', $this->month)->startOfMonth();
        $reader = auth()->user();
        abort_unless($reader !== null, 403);
        $canWrite = $reader->can('update calendar event');

        return ViewFactory::make('livewire.calendar-event-directory', [
            'calendarMonth' => $calendarMonth,
            'days' => $this->daysOf($calendarMonth),
            'events' => $this->directory->events(
                $calendarMonth,
                CalendarEventType::tryFrom($this->type),
                $this->draftsOnly,
                $canWrite,
            ),
            'closures' => $this->directory->closures($calendarMonth),
            'types' => CalendarEventType::cases(),
            'selectedType' => CalendarEventType::tryFrom($this->type),
            'canWrite' => $canWrite,
            'draftCount' => $canWrite ? $this->directory->draftCount() : 0,
        ]);
    }

    private function normalizeFilters(): void
    {
        $this->month = $this->normalizeMonth($this->month);
        $this->type = $this->normalizedType($this->type);
    }

    private function normalizedType(string $type): string
    {
        $selectedType = CalendarEventType::tryFrom($type);

        return $selectedType === null ? '' : $selectedType->value;
    }

    private function normalizeMonth(string $month): string
    {
        return rescue(
            fn (): string => Carbon::createFromFormat('Y-m', $month)->startOfMonth()->format('Y-m'),
            fn (): string => now()->format('Y-m'),
            report: false,
        );
    }
}
