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
                <slot:title>How learners move through the day</slot:title>
                <slot:description>Pick the closest starting pattern. You can still change each name below.</slot:description>
                <slot:content class="space-y-3">
                    @foreach (['home_sections' => 'Learners stay with one class group for most of the day', 'subject_schedule' => 'Learners move between separate subject classes', 'hybrid' => 'Mostly one class group, with some mixed subjects'] as $value => $label)
                        <label class="flex cursor-pointer items-start gap-3 rounded-lg border p-4 hover:bg-muted/50">
                            <input type="radio" name="preset" value="{{ $value }}" class="mt-1" {{ old('preset', $profile->preset) === $value ? 'checked' : '' }}>
                            <span class="text-sm font-medium">{{ $label }}</span>
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
                <slot:footer><april:button type="submit">Save school language</april:button></slot:footer>
            </april:card>
        </form>
    </div>
@endsection
