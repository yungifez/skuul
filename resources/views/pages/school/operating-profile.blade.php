@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('schools.settings'), 'text' => 'School setup'],
    ['href' => route('schools.operating-profile.edit'), 'text' => 'School language', 'active'],
]])

@section('title', __('School language'))
@section('page_heading', __('Use the words your school uses'))

@section('content')
    <div class="mx-auto max-w-3xl space-y-6">
        <div class="flex items-center gap-1 text-muted-foreground">
            <span>Choose the words your school uses.</span>
            <x-help-tooltip label="School language help">Choose a starting pattern, then adjust the labels to match what staff, learners, and families understand. This changes interface wording, not historical records.</x-help-tooltip>
        </div>
        <form method="POST" action="{{ route('schools.operating-profile.update') }}" class="space-y-6">
            @csrf
            @method('PUT')
            @if (request()->boolean('setup'))
                <input type="hidden" name="setup" value="1">
            @endif
            <april:card>
                @php
                    $presetOptions = \App\Models\SchoolOperatingProfile::presetOptions();
                    $selectedPreset = old('preset', array_key_exists($profile->preset, \App\Models\SchoolOperatingProfile::PRESETS) ? $profile->preset : \App\Models\SchoolOperatingProfile::DEFAULT_PRESET);
                @endphp
                <slot:title>Starting language pattern</slot:title>
                <slot:description>Choose one complete set of school terms. Every option includes the same seven labels, and you can edit them below.</slot:description>
                <slot:content class="space-y-3">
                    @foreach ($presetOptions as $value => $option)
                        @php($isSelected = $selectedPreset === $value)
                        <label class="block cursor-pointer rounded-lg border p-4 hover:bg-muted/50 has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                            <span class="flex items-start gap-3">
                                <input type="radio" name="preset" value="{{ $value }}" class="mt-1 size-4 accent-primary" {{ $isSelected ? 'checked' : '' }}>
                                <span class="min-w-0 flex-1">
                                    <span class="flex flex-wrap items-center gap-2 text-sm font-medium">
                                        {{ $option['title'] }}
                                        @if ($value === \App\Models\SchoolOperatingProfile::DEFAULT_PRESET)
                                            <span class="rounded-full bg-muted px-2 py-0.5 text-xs font-normal text-muted-foreground">Default</span>
                                        @endif
                                    </span>
                                    <span class="mt-1 block text-sm text-muted-foreground">{{ $option['description'] }}</span>
                                    <span class="mt-2 flex flex-wrap gap-1">
                                        @foreach ($option['labels'] as $label)
                                            <span class="rounded bg-muted px-1.5 py-0.5 text-xs text-muted-foreground">{{ $label }}</span>
                                        @endforeach
                                    </span>
                                </span>
                            </span>
                        </label>
                    @endforeach
                </slot:content>
            </april:card>
            <april:card>
                <slot:title>Words your school uses</slot:title>
                <slot:description>Use short names that staff will recognize immediately.</slot:description>
                <slot:content class="grid gap-4 sm:grid-cols-2">
                    @foreach (['academic_year' => 'The school year', 'class_level' => 'Grade or class', 'section' => 'Class group', 'period' => 'Term or semester', 'course' => 'Subject or course', 'fee' => 'What families pay', 'homeroom_teacher' => 'The class teacher'] as $key => $label)
                        <div class="space-y-2"><april:label for="label-{{ $key }}">{{ __($label) }}</april:label><input id="label-{{ $key }}" name="labels[{{ $key }}]" value="{{ old('labels.'.$key, data_get($profile->labels, $key, \App\Models\SchoolOperatingProfile::labelsFor($profile->preset)[$key])) }}" class="flex h-10 w-full rounded-md border bg-background px-3 py-2 text-sm"></div>
                    @endforeach
                </slot:content>
                <slot:footer>
                    <div class="flex flex-wrap gap-3">
                        <april:button type="submit">{{ request()->boolean('setup') ? 'Save and continue to classes' : 'Save school language' }}</april:button>
                        @if (!request()->boolean('setup'))
                            <april:button type="submit" name="continue" value="1" variant="outline">Save and continue to classes</april:button>
                        @endif
                    </div>
                </slot:footer>
            </april:card>
        </form>
    </div>
@endsection
