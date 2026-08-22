<?php

namespace App\Services\Timetable;

use App\Models\CustomTimetableItem;
use App\Models\Timetable;
use App\Services\Print\PrintService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;

class TimetableService
{
    // get all syllabus in academic period and class
    public function getAllTimetablesInAcademicPeriodAndClass($academic_period_id, $class_id)
    {
        return Timetable::where('academic_period_id', $academic_period_id)->get()->filter(function ($timetable) use ($class_id) {
            return $timetable->my_class_id == $class_id;
        });
    }

    /**
     * Create timetable.
     *
     * @param  mixed  $data
     */
    public function createTimetable($data): Timetable
    {
        return Timetable::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'my_class_id' => $data['my_class_id'],
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
        return PrintService::createPdfFromView($view, $data)->download($name.'.pdf');
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
