<?php

namespace Tests\Unit;

use App\Livewire\AssignStudentsToParent;
use App\Livewire\CreateTimetableForm;
use App\Livewire\DashboardDataCards;
use App\Livewire\ListFeeInvoicesTable;
use App\Livewire\ListTimetablesTable;
use App\Livewire\ShowStudentProfile;
use Livewire\Attributes\Locked;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/**
 * A list the server builds to check a choice against must not come back from the browser.
 *
 * A Livewire property without #[Locked] takes any value the browser sends. When
 * that property is the list a choice is checked against, the check passes
 * whatever the person sends.
 */
class LockedAllowlistTest extends TestCase
{
    /**
     * @return array<string, array{0: class-string, 1: string}>
     */
    public static function allowlists(): array
    {
        return [
            'guardian sections' => [AssignStudentsToParent::class, 'cycleSections'],
            'guardian learners' => [AssignStudentsToParent::class, 'students'],
            'guardian children' => [AssignStudentsToParent::class, 'children'],
            'timetable weekdays' => [CreateTimetableForm::class, 'weekdays'],
            'timetable audiences' => [CreateTimetableForm::class, 'roles'],
            'timetable terms' => [CreateTimetableForm::class, 'periods'],
            'enrollment statuses' => [ShowStudentProfile::class, 'statusOptions'],
            'invoice filters' => [ListFeeInvoicesTable::class, 'statuses'],
            'timetable list sections' => [ListTimetablesTable::class, 'cycleSections'],
            'timetable list student flag' => [ListTimetablesTable::class, 'isStudent'],
            'dashboard campus flag' => [DashboardDataCards::class, 'showCampuses'],
        ];
    }

    /**
     * @param  class-string  $component
     */
    #[DataProvider('allowlists')]
    public function test_the_list_is_locked(string $component, string $property): void
    {
        $this->assertNotEmpty((new ReflectionProperty($component, $property))->getAttributes(Locked::class), "{$component}::\${$property} is not locked.");
    }
}
