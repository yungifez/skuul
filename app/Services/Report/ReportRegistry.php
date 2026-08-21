<?php

namespace App\Services\Report;

use App\Contracts\Report;
use App\Exceptions\InvalidValueException;
use App\Reports\ClassListReport;
use App\Reports\StudentBalancesReport;

/**
 * The reports this application can build.
 *
 * A new report is one class and one line here, so the request, the queue, and
 * the download do not change when the list grows.
 */
class ReportRegistry
{
    /**
     * The reports, by the name people choose them with.
     *
     * @var array<int, class-string<Report>>
     */
    private const REPORTS = [
        StudentBalancesReport::class,
        ClassListReport::class,
    ];

    /**
     * Get the report with the given name.
     *
     * @throws InvalidValueException when no report has that name
     */
    public function get(string $key): Report
    {
        foreach (self::REPORTS as $class) {
            /** @var Report $report */
            $report = app($class);

            if ($report->key() === $key) {
                return $report;
            }
        }

        throw new InvalidValueException("There is no report called $key.");
    }

    /**
     * Get every report, by name.
     *
     * @return array<string, string>
     */
    public function all(): array
    {
        $reports = [];

        foreach (self::REPORTS as $class) {
            /** @var Report $report */
            $report = app($class);
            $reports[$report->key()] = $report->title();
        }

        return $reports;
    }
}
