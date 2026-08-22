<?php

namespace App\Enums;

/**
 * The way a campus teaches a cycle.
 *
 * The model sets defaults, the screens staff see, the validation an offering
 * passes, and the words a report uses. It does not create a second data
 * model: every campus keeps home sections and instructional groups, and the
 * model only says which rosters an offering may use.
 *
 * Setup asks one question a school manager can answer without help. It never
 * asks for a country or an education system.
 */
enum InstructionalModel: string
{
    /**
     * Learners stay in one home section. Teachers move between sections.
     */
    case FixedHomeSections = 'fixed_home_sections';

    /**
     * Home sections stay the default. Named offerings may differ.
     */
    case Hybrid = 'hybrid';

    /**
     * Each subject offering carries its own roster of learners.
     */
    case SubjectBasedSchedule = 'subject_based_schedule';

    /**
     * The plain-language question setup asks.
     */
    public const SETUP_QUESTION = 'Do learners normally remain with one class group through the day?';

    /**
     * Get the model a campus uses when it has not chosen one.
     *
     * Most schools teach this way, and it is what every cycle recorded before
     * this setting existed did.
     */
    public static function default(): self
    {
        return self::FixedHomeSections;
    }

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::FixedHomeSections => 'One class group all day',
            self::Hybrid => 'One class group, with exceptions',
            self::SubjectBasedSchedule => 'A timetable of separate subjects',
        };
    }

    /**
     * Get the answer to the setup question that picks this model.
     */
    public function setupAnswer(): string
    {
        return match ($this) {
            self::FixedHomeSections => 'Yes. Learners stay together all day.',
            self::Hybrid => 'Mostly. A few subjects mix or split the groups.',
            self::SubjectBasedSchedule => 'No. Learners follow their own subject timetable.',
        };
    }

    /**
     * Get the sentence that explains this model to a school manager.
     */
    public function description(): string
    {
        return match ($this) {
            self::FixedHomeSections => 'Learners stay in one home section. Every subject roster comes from that section, and teachers move between sections.',
            self::Hybrid => 'Home sections stay the default. A chosen subject may join sections together or name its own learners.',
            self::SubjectBasedSchedule => 'Each subject names the learners who attend. Learners can be in different rooms through the day.',
        };
    }

    /**
     * Get an example of a school that teaches this way.
     */
    public function example(): string
    {
        return match ($this) {
            self::FixedHomeSections => 'A primary school where one group of learners shares every lesson.',
            self::Hybrid => 'A secondary school where two sections take music together and a small group takes extra reading.',
            self::SubjectBasedSchedule => 'A senior school where each learner chooses subjects and follows a personal timetable.',
        };
    }

    /**
     * Get the roster an offering starts with under this model.
     */
    public function defaultRosterMode(): RosterMode
    {
        return match ($this) {
            self::FixedHomeSections, self::Hybrid => RosterMode::HomeSection,
            self::SubjectBasedSchedule => RosterMode::IndividualRoster,
        };
    }

    /**
     * Check if an offering may join home sections together.
     */
    public function allowsCombinedSections(): bool
    {
        return $this !== self::FixedHomeSections;
    }

    /**
     * Check if an offering may name the learners who attend.
     */
    public function allowsIndividualRosters(): bool
    {
        return $this !== self::FixedHomeSections;
    }

    /**
     * Check if an offering of this campus may use the given roster.
     *
     * A campus can still allow one offering to differ, such as a combined
     * music class. That exception is recorded on the offering and leaves the
     * campus model alone.
     */
    public function allowsRosterMode(RosterMode $mode): bool
    {
        return match ($mode) {
            RosterMode::HomeSection, RosterMode::AcademicLevel => true,
            RosterMode::CombinedHomeSections => $this->allowsCombinedSections(),
            RosterMode::IndividualRoster => $this->allowsIndividualRosters(),
        };
    }

    /**
     * Get the rosters an offering of this campus may use.
     *
     * @return array<int, RosterMode>
     */
    public function rosterModes(): array
    {
        return array_values(array_filter(
            RosterMode::cases(),
            fn (RosterMode $mode): bool => $this->allowsRosterMode($mode),
        ));
    }

    /**
     * Check if learners keep one group for most of the day.
     *
     * Reports and screens read this to choose between the home-section view
     * and the subject view.
     */
    public function keepsLearnersTogether(): bool
    {
        return $this !== self::SubjectBasedSchedule;
    }

    /**
     * Get the values a form may send.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $model): string => $model->value, self::cases());
    }
}
