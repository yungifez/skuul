<?php

namespace App\Services\Calendar;

use App\Enums\CalendarEventType;
use App\Models\CalendarEvent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class CalendarEventDirectory
{
    public function __construct(private SchoolCalendar $calendar) {}

    /** @return Collection<int, CalendarEvent> */
    public function events(Carbon $month, ?CalendarEventType $type, bool $draftsOnly, bool $canWrite): Collection
    {
        return CalendarEvent::query()
            ->inSchool()
            ->with([
                'audiences.academicCycleSection:id,name,label,academic_level_id',
                'audiences.academicCycleSection.academicLevel:id,name',
                'audiences.user:id,name',
            ])
            ->between($month->copy()->startOfMonth(), $month->copy()->endOfMonth())
            ->when(!$canWrite, function (Builder $query): void {
                $query->published();
            })
            ->when($type !== null, function (Builder $query) use ($type): void {
                $query->where('type', $type);
            })
            ->when($draftsOnly, function (Builder $query): void {
                $query->where('is_published', false);
            })
            ->orderBy('starts_at')
            ->get();
    }

    /** @return Collection<int, CalendarEvent> */
    public function closures(Carbon $month): Collection
    {
        return $this->calendar->closures($month->copy()->startOfMonth(), $month->copy()->endOfMonth());
    }

    public function draftCount(): int
    {
        return CalendarEvent::query()->inSchool()->where('is_published', false)->count();
    }
}
