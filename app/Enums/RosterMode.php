<?php

namespace App\Enums;

/**
 * The ways a subject offering names the learners who attend it.
 *
 * One universal model serves every school. A school where learners stay
 * together reads the roster from a home section; a school where learners move
 * between subjects names the learners itself. The instructional model of the
 * campus says which of these an offering may use.
 */
enum RosterMode: string
{
    /**
     * Every learner of one home section attends.
     */
    case HomeSection = 'home_section';

    /**
     * The learners of two or more home sections attend together.
     */
    case CombinedHomeSections = 'combined_home_sections';

    /**
     * Every learner of one academic level attends.
     */
    case AcademicLevel = 'academic_level';

    /**
     * Staff name the learners who attend, one at a time.
     */
    case IndividualRoster = 'individual_roster';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::HomeSection          => 'One home section',
            self::CombinedHomeSections => 'Combined home sections',
            self::AcademicLevel        => 'Whole academic level',
            self::IndividualRoster     => 'Named learners',
        };
    }

    /**
     * Get the sentence that explains this roster to a member of staff.
     */
    public function description(): string
    {
        return match ($this) {
            self::HomeSection          => 'The learners of one home section attend.',
            self::CombinedHomeSections => 'The learners of several home sections attend together.',
            self::AcademicLevel        => 'Every learner of the academic level attends.',
            self::IndividualRoster     => 'Staff choose each learner who attends.',
        };
    }

    /**
     * Check if the roster is read from home sections.
     */
    public function usesHomeSections(): bool
    {
        return $this === self::HomeSection || $this === self::CombinedHomeSections;
    }

    /**
     * Get the values a form may send.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $mode): string => $mode->value, self::cases());
    }
}
