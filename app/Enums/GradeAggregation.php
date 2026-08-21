<?php

namespace App\Enums;

/**
 * How the items inside a category are put together.
 */
enum GradeAggregation: string
{
    /**
     * Each item counts by its weight.
     */
    case WeightedMean = 'weighted_mean';

    /**
     * Every item counts the same.
     */
    case SimpleMean = 'simple_mean';

    /**
     * The points are added, out of the total maximum.
     */
    case Sum = 'sum';

    /**
     * Only the best item counts.
     */
    case Highest = 'highest';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::WeightedMean => 'Weighted mean',
            self::SimpleMean   => 'Simple mean',
            self::Sum          => 'Sum of points',
            self::Highest      => 'Highest result',
        };
    }

    /**
     * Get the values a form may send.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $aggregation): string => $aggregation->value, self::cases());
    }
}
