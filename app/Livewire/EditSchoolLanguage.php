<?php

namespace App\Livewire;

use App\Models\SchoolOperatingProfile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Choose the words the school uses for years, classes, terms and fees.
 *
 * Picking a pattern fills in its words, and each word can then be changed.
 * The words change what screens say, not what records hold.
 */
class EditSchoolLanguage extends Component
{
    /**
     * The words each screen asks for, keyed by the label they fill.
     *
     * @var array<string, string>
     */
    public const WORDS = [
        'academic_year' => 'The school year',
        'class_level' => 'Grade or class',
        'section' => 'Class group',
        'period' => 'Term or semester',
        'course' => 'Subject or course',
        'fee' => 'What families pay',
        'homeroom_teacher' => 'The class teacher',
    ];

    public string $preset = SchoolOperatingProfile::DEFAULT_PRESET;

    /** @var array<string, string> */
    public array $labels = [];

    public bool $setup = false;

    public function mount(bool $setup = false): void
    {
        $school = current_school();
        Gate::authorize('update', $school);

        $profile = $school->operatingProfile()->firstOrCreate([], [
            'preset' => SchoolOperatingProfile::DEFAULT_PRESET,
            'labels' => SchoolOperatingProfile::labelsFor(SchoolOperatingProfile::DEFAULT_PRESET),
        ]);

        $this->setup = $setup;
        $this->preset = array_key_exists($profile->preset, SchoolOperatingProfile::PRESETS) ? $profile->preset : SchoolOperatingProfile::DEFAULT_PRESET;
        $defaults = SchoolOperatingProfile::labelsFor($this->preset);

        foreach (array_keys(self::WORDS) as $key) {
            $this->labels[$key] = (string) data_get($profile->labels, $key, $defaults[$key]);
        }
    }

    public function updatedPreset(): void
    {
        $this->labels = array_intersect_key(SchoolOperatingProfile::labelsFor($this->preset), self::WORDS);
        $this->resetErrorBag();
    }

    public function save(bool $continue = false): void
    {
        $school = current_school();
        Gate::authorize('update', $school);

        $rules = ['preset' => ['required', Rule::in(array_keys(SchoolOperatingProfile::PRESETS))]];

        foreach (array_keys(self::WORDS) as $key) {
            $rules["labels.$key"] = ['required', 'string', 'max:40'];
        }

        $this->validate($rules, attributes: collect(self::WORDS)->mapWithKeys(fn (string $word, string $key): array => ["labels.$key" => strtolower($word)])->all());

        $profile = $school->operatingProfile()->updateOrCreate([], ['preset' => $this->preset, 'labels' => $this->labels]);
        $profile->forceFill(['setup_completed_at' => now()])->save();

        session()->flash('success', 'School language updated.');

        $this->setup || $continue
            ? $this->redirectRoute('schools.setup', [$school, 'classes'])
            : $this->redirectRoute('schools.settings');
    }

    public function render(): View
    {
        return view('livewire.edit-school-language', [
            'presetOptions' => SchoolOperatingProfile::presetOptions(),
            'words' => self::WORDS,
        ]);
    }
}
