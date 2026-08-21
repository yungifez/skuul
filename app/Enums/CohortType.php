<?php

namespace App\Enums;

/**
 * What a cohort is for.
 *
 * A cohort is a named group of people that is not a class and not a section.
 * The same person can be in many.
 */
enum CohortType: string
{
    /**
     * The people expected to finish together.
     */
    case GraduationYear = 'graduation_year';

    /**
     * The people who hold a scholarship or bursary.
     */
    case Scholarship = 'scholarship';

    /**
     * A club or society.
     */
    case Club = 'club';

    /**
     * The people the school is watching for a reason.
     */
    case Watchlist = 'watchlist';

    /**
     * A group the school compares results within.
     */
    case RankingGroup = 'ranking_group';

    /**
     * Anything else the school names.
     */
    case Other = 'other';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::GraduationYear => 'Graduation year',
            self::Scholarship    => 'Scholarship group',
            self::Club           => 'Club',
            self::Watchlist      => 'Watchlist',
            self::RankingGroup   => 'Ranking group',
            self::Other          => 'Other',
        };
    }

    /**
     * Check if the group is private to the staff who keep it.
     */
    public function isRestricted(): bool
    {
        return $this === self::Watchlist;
    }

    /**
     * Get the values a form may send.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $type): string => $type->value, self::cases());
    }
}
