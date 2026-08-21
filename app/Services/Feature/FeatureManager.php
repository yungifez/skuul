<?php

namespace App\Services\Feature;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\Feature;
use App\Models\FeatureSetting;
use App\Models\School;
use App\Models\User;

/**
 * Say whether a feature is on, and turn it on or off.
 *
 * A school setting wins over the platform default. Turning a feature off
 * hides its screens and blocks its actions; it never deletes what the school
 * already recorded.
 */
class FeatureManager
{
    /**
     * The answers already worked out during this request.
     *
     * @var array<string, bool>
     */
    private array $answers = [];

    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * Check if the feature is on for the given school.
     */
    public function enabled(Feature $feature, School|int|null $school = null): bool
    {
        $schoolId = $this->schoolId($school);
        $key = "$feature->value:".($schoolId ?? 'platform');

        return $this->answers[$key] ??= $this->resolve($feature, $schoolId);
    }

    /**
     * Check if the feature is off for the given school.
     */
    public function disabled(Feature $feature, School|int|null $school = null): bool
    {
        return !$this->enabled($feature, $school);
    }

    /**
     * Get one piece of the feature's settings.
     */
    public function config(Feature $feature, string $key, mixed $default = null, School|int|null $school = null): mixed
    {
        $setting = $this->settingFor($feature, $this->schoolId($school));

        return data_get($setting === null ? [] : ($setting->config ?? []), $key, $default);
    }

    /**
     * Turn the feature on for the school.
     *
     * @param  array<string, mixed>|null  $config
     */
    public function enable(Feature $feature, School|int|null $school = null, ?User $actor = null, ?array $config = null): FeatureSetting
    {
        return $this->set($feature, true, $school, $actor, $config);
    }

    /**
     * Turn the feature off for the school.
     */
    public function disable(Feature $feature, School|int|null $school = null, ?User $actor = null): FeatureSetting
    {
        return $this->set($feature, false, $school, $actor);
    }

    /**
     * Get every feature with its answer for the school.
     *
     * @return array<string, bool>
     */
    public function all(School|int|null $school = null): array
    {
        $answers = [];

        foreach (Feature::cases() as $feature) {
            $answers[$feature->value] = $this->enabled($feature, $school);
        }

        return $answers;
    }

    /**
     * Forget what was worked out, after a setting changed.
     */
    public function forget(): void
    {
        $this->answers = [];
    }

    /**
     * Write the setting and record who changed it.
     *
     * @param  array<string, mixed>|null  $config
     */
    private function set(Feature $feature, bool $enabled, School|int|null $school, ?User $actor, ?array $config = null): FeatureSetting
    {
        $schoolId = $this->schoolId($school);

        $setting = FeatureSetting::updateOrCreate(
            ['school_id' => $schoolId, 'feature' => $feature],
            array_filter([
                'enabled' => $enabled,
                'config' => $config,
                'updated_by' => $actor === null ? auth()->id() : $actor->id,
            ], fn (mixed $value): bool => $value !== null),
        );

        $this->forget();

        $this->auditor->record(
            $enabled ? AuditAction::FeatureEnabled : AuditAction::FeatureDisabled,
            $setting,
            ['feature' => $feature->value, 'school_id' => $schoolId],
            $actor,
        );

        return $setting;
    }

    /**
     * Work out the answer: school setting, then platform, then the default.
     */
    private function resolve(Feature $feature, ?int $schoolId): bool
    {
        $setting = $this->settingFor($feature, $schoolId);

        if ($setting !== null) {
            return $setting->enabled;
        }

        return $feature->defaultsToOn();
    }

    /**
     * Get the setting that applies, narrowest first.
     */
    private function settingFor(Feature $feature, ?int $schoolId): ?FeatureSetting
    {
        $ofSchool = $schoolId === null
            ? null
            : FeatureSetting::where('school_id', $schoolId)->where('feature', $feature)->first();

        return $ofSchool ?? FeatureSetting::whereNull('school_id')->where('feature', $feature)->first();
    }

    /**
     * Read the school out of what the caller gave.
     */
    private function schoolId(School|int|null $school): ?int
    {
        return $school instanceof School ? $school->id : ($school ?? current_school_id());
    }
}
