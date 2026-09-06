<?php

use App\Enums\Feature;
use App\Enums\InstructionalModel;
use App\Enums\RosterMode;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\School;
use App\Services\Academic\AcademicPeriodContext;
use App\Services\Curriculum\InstructionalModelResolver;
use App\Services\Feature\FeatureManager;
use App\Services\School\SchoolContext;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\View\ComponentAttributeBag;

if (!function_exists('school_context')) {
    /**
     * Get the school context for the current request.
     */
    function school_context(): SchoolContext
    {
        return app(SchoolContext::class);
    }
}

if (!function_exists('current_school')) {
    /**
     * Get the school the current request works in.
     */
    function current_school(): ?School
    {
        return school_context()->school();
    }
}

if (!function_exists('current_school_id')) {
    /**
     * Get the id of the school the current request works in.
     */
    function current_school_id(): ?int
    {
        return school_context()->id();
    }
}

if (!function_exists('academic_period_context')) {
    /**
     * Get the academic period context for the current request.
     */
    function academic_period_context(): AcademicPeriodContext
    {
        return app(AcademicPeriodContext::class);
    }
}

if (!function_exists('current_academic_year')) {
    /**
     * Get the academic year the current request works in.
     */
    function current_academic_year(): ?AcademicYear
    {
        return academic_period_context()->academicYear();
    }
}

if (!function_exists('current_academic_year_id')) {
    /**
     * Get the id of the academic year the current request works in.
     */
    function current_academic_year_id(): ?int
    {
        return academic_period_context()->academicYearId();
    }
}

if (!function_exists('current_academic_period')) {
    /**
     * Get the academic period the current request works in.
     */
    function current_academic_period(): ?AcademicPeriod
    {
        return academic_period_context()->academicPeriod();
    }
}

if (!function_exists('current_academic_period_id')) {
    /**
     * Get the id of the academic period the current request works in.
     */
    function current_academic_period_id(): ?int
    {
        return academic_period_context()->academicPeriodId();
    }
}

if (!function_exists('features')) {
    /**
     * Get the service that answers which features are on.
     */
    function features(): FeatureManager
    {
        return app(FeatureManager::class);
    }
}

if (!function_exists('instructional_model')) {
    /**
     * Get the way the campus teaches the given academic cycle.
     *
     * With no argument this reads the cycle of the current request. A campus
     * that never chose reads as the default model.
     */
    function instructional_model(AcademicYear|int|null $academicYear = null, School|int|null $school = null): InstructionalModel
    {
        return app(InstructionalModelResolver::class)->for($academicYear, $school);
    }
}

if (!function_exists('feature_enabled')) {
    /**
     * Check if a feature is on in the school being worked in.
     */
    function feature_enabled(Feature $feature): bool
    {
        return features()->enabled($feature);
    }
}

if (!function_exists('sidebar_open')) {
    /**
     * Check if the sidebar should render open.
     *
     * April UI stores the choice in a plain cookie, which bootstrap/app.php
     * keeps out of cookie encryption. Pass the result to the sidebar as
     * defaultOpen so the first paint matches the last choice. The sidebar
     * opens when nothing is stored yet.
     */
    function sidebar_open(): bool
    {
        return request()->cookie('sidebar_state') !== 'false';
    }
}

if (!function_exists('school_term')) {
    /**
     * Get a school-facing word from the operating profile.
     */
    function school_term(string $key, string $fallback): string
    {
        return __(current_school()?->operatingProfile?->labels[$key] ?? $fallback);
    }
}

if (!function_exists('school_terms')) {
    /**
     * Get the plural form of a school-facing word from the operating profile.
     */
    function school_terms(string $key, string $fallback): string
    {
        return Str::plural(school_term($key, $fallback));
    }
}

if (!function_exists('school_roster_label')) {
    /**
     * Get a learner-list label that uses the school's chosen section word.
     */
    function school_roster_label(RosterMode $mode): string
    {
        return $mode->label(
            strtolower(school_term('section', 'section')),
            strtolower(school_terms('section', 'sections')),
        );
    }
}

if (!function_exists('school_roster_description')) {
    /**
     * Get a learner-list description that uses the school's chosen section word.
     */
    function school_roster_description(RosterMode $mode): string
    {
        return $mode->description(
            strtolower(school_term('section', 'section')),
            strtolower(school_terms('section', 'sections')),
        );
    }
}

if (!function_exists('school_instructional_model_description')) {
    /**
     * Get a teaching setup description that uses the school's chosen section word.
     */
    function school_instructional_model_description(InstructionalModel $model): string
    {
        return $model->description(
            strtolower(school_term('section', 'section')),
            strtolower(school_terms('section', 'sections')),
        );
    }
}

if (!function_exists('field_error_id')) {
    /**
     * Get the id of the element that carries a field's validation message.
     *
     * A control points at its message with aria-describedby, so both sides
     * have to work the id out the same way.
     */
    function field_error_id(string $field): string
    {
        $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower($field)) ?? '';

        return trim($slug, '-').'-error';
    }
}

if (!function_exists('field_error_key')) {
    /**
     * Read the validation key a form control is bound to.
     *
     * A control names its field with name="..." or with wire:model. HTML array
     * syntax and Livewire dot syntax name the same field two ways, so reduce
     * both to the key the validator uses.
     */
    function field_error_key(?string $name): ?string
    {
        if ($name === null || $name === '') {
            return null;
        }

        return str_replace(['[]', '[', ']'], ['', '.', ''], $name);
    }
}

if (!function_exists('field_error_bindings')) {
    /**
     * Tie a form control to the message that says why it was refused.
     *
     * Without this a screen reader reads the control as if nothing were wrong,
     * and the message sits on the page with nothing to connect it to.
     */
    function field_error_bindings(string $field): HtmlString
    {
        $key = field_error_key($field);

        if ($key === null || !view()->shared('errors')?->has($key)) {
            return new HtmlString('');
        }

        return new HtmlString(sprintf(
            'aria-invalid="true" aria-describedby="%s"',
            e(field_error_id($key)),
        ));
    }
}

if (!function_exists('april_field_error_attributes')) {
    /**
     * Work out a component's error wiring from the field it is bound to.
     *
     * A blade directive inside an <april:*> tag breaks the tag precompiler, so
     * an april component cannot take these attributes from the view that uses
     * it. It reads its own name or wire:model binding instead.
     *
     * @return array<string, string>
     */
    function april_field_error_attributes(ComponentAttributeBag $attributes): array
    {
        $name = $attributes->get('name');

        if (!is_string($name) || $name === '') {
            $name = null;

            foreach ($attributes->getAttributes() as $attribute => $value) {
                if (str_starts_with((string) $attribute, 'wire:model') && is_string($value) && $value !== '') {
                    $name = $value;

                    break;
                }
            }
        }

        $key = field_error_key($name);

        if ($key === null || !view()->shared('errors')?->has($key)) {
            return [];
        }

        return ['aria-invalid' => 'true', 'aria-describedby' => field_error_id($key)];
    }
}
