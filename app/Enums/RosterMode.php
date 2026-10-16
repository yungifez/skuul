<?php

namespace App\Enums;

/**
 * Select a home-section roster or an explicit learner roster.
 * The instructional model controls which modes an offering can use.
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

    public function label(string $sectionLabel = 'section', string $sectionsLabel = 'sections'): string
    {
        return match ($this) {
            self::HomeSection => 'One '.$sectionLabel,
            self::CombinedHomeSections => 'Combined '.$sectionsLabel,
            self::AcademicLevel => 'Whole class',
            self::IndividualRoster => 'Named learners',
        };
    }

    /**
     * Get the sentence that explains this roster to a member of staff.
     */
    public function description(string $sectionLabel = 'section', string $sectionsLabel = 'sections'): string
    {
        return match ($this) {
            self::HomeSection => 'The learners of one '.$sectionLabel.' attend.',
            self::CombinedHomeSections => 'The learners of several '.$sectionsLabel.' attend together.',
            self::AcademicLevel => 'Every learner of the class attends.',
            self::IndividualRoster => 'Staff choose each learner who attends.',
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
