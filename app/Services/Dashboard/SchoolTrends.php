<?php

namespace App\Services\Dashboard;

use App\Enums\AttendanceKind;
use App\Enums\AttendanceStatus;
use App\Enums\EnrollmentStatus;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AttendanceRecord;
use App\Models\Expense;
use App\Models\Incident;
use App\Models\StudentPayment;
use App\Models\StudentRecord;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Count how the working school has moved over the last weeks and months.
 *
 * Each method groups rows in the database, so a large school sends back a
 * few dozen totals and not every register mark or payment.
 */
class SchoolTrends
{
    /**
     * Rate the daily registers week by week, oldest week first.
     *
     * A day nobody recorded is left out of the rate, as in AttendanceSummary.
     *
     * @return array{weeks: list<array{week: string, rate: float|null}>, recent: float|null, previous: float|null}
     */
    public function attendanceByWeek(CarbonImmutable $today, int $weeks = 8): array
    {
        $firstWeek = $today->startOfWeek()->subWeeks($weeks - 1);
        $presentStatuses = collect(AttendanceStatus::cases())
            ->filter(fn (AttendanceStatus $status): bool => $status->countsAsPresent())
            ->map(fn (AttendanceStatus $status): string => $status->value)
            ->values()
            ->all();

        $totals = AttendanceRecord::query()
            ->inSchool()
            ->ofKind(AttendanceKind::Daily)
            ->where('status', '!=', AttendanceStatus::NotRecorded)
            ->whereBetween('attended_on', [$firstWeek->toDateString(), $today->toDateString()])
            ->selectRaw('attended_on, count(*) as recorded')
            ->selectRaw('sum(case when status in ('.implode(',', array_fill(0, count($presentStatuses), '?')).') then 1 else 0 end) as present', $presentStatuses)
            ->groupBy('attended_on')
            ->get()
            ->groupBy(fn (AttendanceRecord $day): string => CarbonImmutable::parse($day->getRawOriginal('attended_on'))->startOfWeek()->toDateString());

        $rows = collect(range(0, $weeks - 1))->map(function (int $offset) use ($firstWeek, $totals): array {
            $weekStart = $firstWeek->addWeeks($offset);
            $days = $totals->get($weekStart->toDateString(), collect());

            return [
                'week' => $weekStart->format('M j'),
                'recorded' => (int) $days->sum('recorded'),
                'present' => (int) $days->sum('present'),
            ];
        });

        $half = intdiv($weeks, 2);

        return [
            'weeks' => $rows->map(fn (array $row): array => [
                'week' => $row['week'],
                'rate' => $this->rate($row['present'], $row['recorded']),
            ])->all(),
            'recent' => $this->rate($rows->slice($half)->sum('present'), $rows->slice($half)->sum('recorded')),
            'previous' => $this->rate($rows->take($half)->sum('present'), $rows->take($half)->sum('recorded')),
        ];
    }

    /**
     * Compare what the school billed with what it received, month by month.
     *
     * Both figures are major amounts. A payment and its reversal cancel out,
     * so neither is counted.
     *
     * @return list<array{month: string, billed: float, collected: float}>
     */
    public function feesByMonth(CarbonImmutable $today, int $months = 6): array
    {
        $firstMonth = $today->startOfMonth()->subMonths($months - 1);

        $billed = DB::table('fee_invoice_records')
            ->join('fee_invoices', 'fee_invoices.id', '=', 'fee_invoice_records.fee_invoice_id')
            ->where('fee_invoices.school_id', current_school_id())
            ->whereBetween('fee_invoices.issue_date', [$firstMonth->toDateString(), $today->endOfDay()->toDateTimeString()])
            ->selectRaw("date_format(fee_invoices.issue_date, '%Y-%m') as month")
            ->selectRaw('sum(fee_invoice_records.amount + coalesce(fee_invoice_records.fine, 0) - coalesce(fee_invoice_records.waiver, 0)) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        $collected = StudentPayment::query()
            ->inSchool()
            ->stillStanding()
            ->whereBetween('received_on', [$firstMonth->toDateString(), $today->toDateString()])
            ->selectRaw("date_format(received_on, '%Y-%m') as month, sum(amount) as total")
            ->groupBy('month')
            ->toBase()
            ->pluck('total', 'month');

        return collect(range(0, $months - 1))->map(function (int $offset) use ($firstMonth, $billed, $collected): array {
            $month = $firstMonth->addMonths($offset);
            $key = $month->format('Y-m');

            return [
                'month' => $month->format('M'),
                'billed' => (int) ($billed[$key] ?? 0) / 100,
                'collected' => (int) ($collected[$key] ?? 0) / 100,
            ];
        })->all();
    }

    /**
     * Split what families still owe by how late it is, on the due date.
     *
     * A line paid more than it asked for counts as nothing owed, never as a
     * credit that hides another family's debt.
     *
     * @return list<array{label: string, owed: float}>
     */
    public function owedByLateness(CarbonImmutable $today): array
    {
        $paid = '(select coalesce(sum(payment_allocations.amount), 0) from payment_allocations where payment_allocations.fee_invoice_record_id = fee_invoice_records.id)';
        $owed = "greatest(fee_invoice_records.amount + coalesce(fee_invoice_records.fine, 0) - coalesce(fee_invoice_records.waiver, 0) - $paid, 0)";
        $daysLate = 'datediff(?, fee_invoices.due_date)';

        $totals = DB::table('fee_invoice_records')
            ->join('fee_invoices', 'fee_invoices.id', '=', 'fee_invoice_records.fee_invoice_id')
            ->where('fee_invoices.school_id', current_school_id())
            ->whereNull('fee_invoices.deleted_at')
            ->selectRaw("case when $daysLate <= 0 then 'current' when $daysLate <= 30 then 'late30' when $daysLate <= 60 then 'late60' when $daysLate <= 90 then 'late90' else 'late90plus' end as bucket", array_fill(0, 4, $today->toDateString()))
            ->selectRaw("sum($owed) as total")
            ->groupBy('bucket')
            ->pluck('total', 'bucket');

        return collect([
            'current' => 'Not due yet',
            'late30' => '1–30 days late',
            'late60' => '31–60 days late',
            'late90' => '61–90 days late',
            'late90plus' => 'Over 90 days late',
        ])->map(fn (string $label, string $bucket): array => [
            'label' => $label,
            'owed' => (int) ($totals[$bucket] ?? 0) / 100,
        ])->values()->all();
    }

    /**
     * Compare the money received with the money spent, month by month.
     *
     * @return list<array{month: string, received: float, spent: float}>
     */
    public function cashByMonth(CarbonImmutable $today, int $months = 6): array
    {
        $firstMonth = $today->startOfMonth()->subMonths($months - 1);

        $received = StudentPayment::query()
            ->inSchool()
            ->stillStanding()
            ->whereBetween('received_on', [$firstMonth->toDateString(), $today->toDateString()])
            ->selectRaw("date_format(received_on, '%Y-%m') as month, sum(amount) as total")
            ->groupBy('month')
            ->toBase()
            ->pluck('total', 'month');

        $spent = Expense::query()
            ->inSchool()
            ->whereBetween('expense_date', [$firstMonth->toDateString(), $today->toDateString()])
            ->selectRaw("date_format(expense_date, '%Y-%m') as month, sum(amount) as total")
            ->groupBy('month')
            ->toBase()
            ->pluck('total', 'month');

        return collect(range(0, $months - 1))->map(function (int $offset) use ($firstMonth, $received, $spent): array {
            $key = $firstMonth->addMonths($offset)->format('Y-m');

            return [
                'month' => $firstMonth->addMonths($offset)->format('M'),
                'received' => (int) ($received[$key] ?? 0) / 100,
                'spent' => round((float) ($spent[$key] ?? 0), 2),
            ];
        })->all();
    }

    /**
     * Count the incidents this person may read, week by week.
     *
     * @return list<array{week: string, incidents: int}>
     */
    public function incidentsByWeek(User $reader, CarbonImmutable $today, int $weeks = 8): array
    {
        $firstWeek = $today->startOfWeek()->subWeeks($weeks - 1);

        $counts = Incident::query()
            ->inSchool()
            ->readableBy($reader)
            ->whereBetween('occurred_at', [$firstWeek, $today->endOfDay()])
            ->selectRaw('date(occurred_at) as day, count(*) as total')
            ->groupBy('day')
            ->toBase()
            ->get()
            ->groupBy(fn (object $day): string => CarbonImmutable::parse($day->day)->startOfWeek()->toDateString())
            ->map(fn (Collection $days): int => (int) $days->sum('total'));

        return collect(range(0, $weeks - 1))->map(function (int $offset) use ($firstWeek, $counts): array {
            $weekStart = $firstWeek->addWeeks($offset);

            return [
                'week' => $weekStart->format('M j'),
                'incidents' => $counts->get($weekStart->toDateString(), 0),
            ];
        })->all();
    }

    /**
     * Count the students attending each class this year beside the seats its sections hold.
     *
     * @return list<array{name: string, students: int, capacity: int}>
     */
    public function enrolmentByClass(int $academicYearId): array
    {
        $students = StudentRecord::query()
            ->where('student_records.school_id', current_school_id())
            ->where('student_records.status', EnrollmentStatus::Active)
            ->join('academic_cycle_sections', 'academic_cycle_sections.id', '=', 'student_records.academic_cycle_section_id')
            ->where('academic_cycle_sections.academic_year_id', $academicYearId)
            ->selectRaw('academic_cycle_sections.academic_level_id as level_id, count(*) as total')
            ->groupBy('level_id')
            ->toBase()
            ->pluck('total', 'level_id');

        $capacity = AcademicCycleSection::query()
            ->inSchool()
            ->where('academic_year_id', $academicYearId)
            ->selectRaw('academic_level_id, sum(coalesce(capacity, 0)) as seats')
            ->groupBy('academic_level_id')
            ->toBase()
            ->pluck('seats', 'academic_level_id');

        return AcademicLevel::query()
            ->inSchool()
            ->whereIn('id', $capacity->keys()->merge($students->keys())->unique())
            ->orderBy('position')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (AcademicLevel $level): array => [
                'name' => $level->name,
                'students' => (int) ($students[$level->id] ?? 0),
                'capacity' => (int) ($capacity[$level->id] ?? 0),
            ])
            ->values()
            ->all();
    }

    private function rate(int|float $present, int|float $recorded): ?float
    {
        return $recorded > 0 ? round($present / $recorded * 100, 1) : null;
    }
}
