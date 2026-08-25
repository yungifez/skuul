<?php

namespace App\Services\Timetable;

use App\Models\CustomTimetableItem;
use App\Models\Timetable;
use App\Services\Print\PrintService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;

class TimetableService
{
    /**
     * Get timetables for one academic period and one exact home group.
     *
     * @return Collection<int, Timetable>
     */
    public function getAllTimetablesInAcademicPeriodAndCycleSection(int $academicPeriodId, int $academicCycleSectionId): Collection
    {
        return Timetable::query()
            ->where('academic_period_id', $academicPeriodId)
            ->where('academic_cycle_section_id', $academicCycleSectionId)
            ->get();
    }

    /**
     * Create timetable.
     *
     * @param  array{name: string, description?: string|null, academic_cycle_section_id: int, academic_period_id: int}  $data
     */
    public function createTimetable(array $data): Timetable
    {
        return Timetable::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'academic_cycle_section_id' => $data['academic_cycle_section_id'],
            'academic_period_id' => $data['academic_period_id'],
        ]);
    }

    /**
     * Update timetable.
     *
     * @param  mixed  $data
     * @return void
     */
    public function updateTimetable(Timetable $timetable, $data)
    {
        $timetable->name = $data['name'];
        $timetable->description = $data['description'];
        $timetable->save();
    }

    /**
     * Print timetable.
     *
     *
     * @return Response
     */
    public function printTimetable(string $name, string $view, array $data)
    {
        return PrintService::page($view, $data);
    }

    /**
     * Delete timetable.
     *
     *
     * @return void
     */
    public function deleteTimetable(Timetable $timetable)
    {
        $timetable->delete();
    }

    /**
     * Get all custom timetable items in school.
     *
     * @return Collection
     */
    public function getAllCustomTimetableItem()
    {
        return CustomTimetableItem::inSchool()->get();
    }

    /**
     * Create custom timetable item.
     *
     * @param  array<mixed>  $record
     * @return CustomTimetableItem
     */
    public function createCustomTimetableItem($record)
    {
        return CustomTimetableItem::create([
            'name' => $record['name'],
            'school_id' => $record['school_id'],
        ]);
    }

    /**
     * Update a given custom timetable item.
     *
     * @param  array<mixed>  $record
     * @return CustomTimetableItem
     */
    public function updateCustomTimetableItem(CustomTimetableItem $customTimetableItem, $record)
    {
        $customTimetableItem->name = $record['name'];
        $customTimetableItem->save();

        return $customTimetableItem;
    }

    public function deleteCustomTimetableItem(CustomTimetableItem $customTimetableItem)
    {
        $customTimetableItem->timetableRecord()->delete();
        $customTimetableItem->delete();
    }
}
