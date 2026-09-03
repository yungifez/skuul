@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('rankings.index'), 'text' => 'Rankings', 'active'],
]])

@section('title', 'Rankings')
@section('page_heading', 'Rankings')

@php
    $chosen = $academicLevel !== null || $section !== null || $cohort !== null;
    $groupName = $cohort?->name ?? $section?->name ?? $academicLevel?->name;
    $selectionMode = $academicLevel?->is_group ? 'group' : 'class';
@endphp

@section('content')
    <div class="space-y-6">
        @if ($error !== null)
            <april:alert variant="destructive">
                <slot:title>No order was worked out</slot:title>
                <slot:description>{{ $error }}</slot:description>
            </april:alert>
        @endif

        <april:card>
            <slot:title>Choose a class or group</slot:title>
            <slot:description>
                Start with who you want to compare. We only offer a subject when every learner in that selection takes it.
            </slot:description>
            <slot:content>
                <form method="GET" action="{{ route('rankings.index') }}"
                    class="space-y-6" x-data="{ selectionMode: @js($selectionMode) }">
                    <fieldset class="space-y-3">
                        <legend class="text-sm font-medium">What do you want to compare?</legend>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <label class="flex cursor-pointer items-start gap-3 rounded-md border border-input p-3 transition-colors hover:bg-accent/40 has-[:checked]:border-primary/50 has-[:checked]:bg-primary/5">
                                <input type="radio" name="selection_mode" value="class" class="mt-1 size-4 shrink-0 accent-primary"
                                    x-model="selectionMode"
                                    x-on:change="$refs.group && ($refs.group.value = ''); $refs.section.value = ''; $refs.subject.value = '';"
                                    @checked($selectionMode === 'class')>
                                <span class="flex min-w-0 flex-col gap-1">
                                    <span class="text-sm font-semibold">{{ school_term('class_level', 'Class') }}</span>
                                    <span class="text-xs text-muted-foreground">Compare learners in one class and its sections.</span>
                                </span>
                            </label>
                            @if ($academicLevels->where('is_group', true)->isNotEmpty())
                                <label class="flex cursor-pointer items-start gap-3 rounded-md border border-input p-3 transition-colors hover:bg-accent/40 has-[:checked]:border-primary/50 has-[:checked]:bg-primary/5">
                                    <input type="radio" name="selection_mode" value="group" class="mt-1 size-4 shrink-0 accent-primary"
                                        x-model="selectionMode"
                                        x-on:change="$refs.class.value = ''; $refs.section.value = ''; $refs.subject.value = '';"
                                        @checked($selectionMode === 'group')>
                                    <span class="flex min-w-0 flex-col gap-1">
                                        <span class="text-sm font-semibold">Whole group</span>
                                        <span class="text-xs text-muted-foreground">Compare learners across the group’s child classes.</span>
                                    </span>
                                </label>
                            @endif
                        </div>
                    </fieldset>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div x-cloak x-show="selectionMode === 'class'" class="flex flex-col gap-2">
                            <div class="flex min-h-10 items-start">
                                <april:label for="academic_level_id">{{ school_term('class_level', 'Class') }}</april:label>
                            </div>
                            <april:native-select id="academic_level_id" name="academic_level_id" x-ref="class" class="w-full min-w-0"
                                x-bind:disabled="selectionMode !== 'class'"
                                x-on:change="$refs.group && ($refs.group.value = ''); $refs.section.value = ''; $refs.subject.value = ''; $el.form.requestSubmit()">
                                <option value="">Choose a {{ strtolower(school_term('class_level', 'class')) }}</option>
                                <optgroup label="{{ school_terms('class_level', 'Classes') }}">
                                    @foreach ($academicLevels->where('is_group', false) as $option)
                                        <option value="{{ $option->id }}" @selected(!$academicLevel?->is_group && $academicLevel?->id === $option->id)>
                                            {{ $option->parent?->name ? $option->parent->name.' → ' : '' }}{{ $option->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            </april:native-select>
                            <p class="text-xs text-muted-foreground">Sections will appear after you choose a class.</p>
                        </div>

                        @if ($academicLevels->where('is_group', true)->isNotEmpty())
                            <div x-cloak x-show="selectionMode === 'group'" class="flex flex-col gap-2">
                                <div class="flex min-h-10 items-start">
                                    <april:label for="group_academic_level_id">Whole group</april:label>
                                </div>
                                <april:native-select id="group_academic_level_id" name="group_academic_level_id" x-ref="group" class="w-full min-w-0"
                                    x-bind:disabled="selectionMode !== 'group'"
                                    x-on:change="$refs.class.value = ''; $refs.section.value = ''; $refs.subject.value = ''; $el.form.requestSubmit()">
                                    <option value="">Choose a group</option>
                                    @foreach ($academicLevels->where('is_group', true) as $option)
                                        <option value="{{ $option->id }}" @selected($academicLevel?->is_group && $academicLevel->id === $option->id)>
                                            {{ $option->parent?->name ? $option->parent->name.' → ' : '' }}{{ $option->name }}
                                        </option>
                                    @endforeach
                                </april:native-select>
                                <p class="text-xs text-muted-foreground">Includes every learner in the group’s child classes.</p>
                            </div>
                        @endif
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="flex flex-col gap-2">
                            <div class="flex min-h-10 items-start">
                                <april:label for="academic_cycle_section_id">{{ school_term('section', 'Section') }}</april:label>
                            </div>
                            <april:native-select id="academic_cycle_section_id" name="academic_cycle_section_id" x-ref="section" class="w-full min-w-0"
                                :disabled="$academicLevel === null"
                                x-on:change="$refs.subject.value = ''; $el.form.requestSubmit()">
                                <option value="">Every section in this {{ strtolower(school_term('class_level', 'class')) }}</option>
                                @foreach ($sections as $option)
                                    <option value="{{ $option->id }}" @selected($section?->id === $option->id)>
                                        {{ $option->academicLevel?->name }} · {{ $option->label ?? $option->name }}
                                    </option>
                                @endforeach
                            </april:native-select>
                            <p class="text-xs text-muted-foreground">Optional: narrow the comparison to one section.</p>
                        </div>

                        <div class="flex flex-col gap-2">
                            <div class="flex min-h-10 items-start">
                                <april:label for="cohort_id">Learner group</april:label>
                            </div>
                            <april:native-select id="cohort_id" name="cohort_id" class="w-full min-w-0"
                                x-on:change="$refs.subject.value = ''; $el.form.requestSubmit()">
                                <option value="">No learner group</option>
                                @foreach ($cohorts as $option)
                                    <option value="{{ $option->id }}" @selected($cohort?->id === $option->id)>{{ $option->name }}</option>
                                @endforeach
                            </april:native-select>
                            <p class="text-xs text-muted-foreground">Optional: compare a named set of learners instead.</p>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="flex flex-col gap-2">
                            <div class="flex min-h-10 items-start">
                                <april:label for="academic_period_id">{{ school_term('period', 'Period') }}</april:label>
                            </div>
                            <april:native-select id="academic_period_id" name="academic_period_id" class="w-full min-w-0"
                                x-on:change="$refs.subject.value = ''; $el.form.requestSubmit()">
                                <option value="">Every {{ school_term('period', 'period') }}</option>
                                @foreach ($periods as $option)
                                    <option value="{{ $option->id }}" @selected($period?->id === $option->id)>{{ $option->label ?? $option->name }}</option>
                                @endforeach
                            </april:native-select>
                        </div>

                        <div class="flex flex-col gap-2">
                            <div class="flex min-h-10 items-start">
                                <april:label for="subject_id">Subject</april:label>
                            </div>
                            <april:native-select id="subject_id" name="subject_id" x-ref="subject" class="w-full min-w-0"
                                :disabled="$subjects->isEmpty()">
                                <option value="">{{ $subjects->isEmpty() ? 'No subject shared by everyone' : 'Every subject' }}</option>
                                @foreach ($subjects as $option)
                                    <option value="{{ $option->id }}" @selected($subject?->id === $option->id)>{{ $option->name }}</option>
                                @endforeach
                            </april:native-select>
                            <p class="text-xs text-muted-foreground">Only subjects shared by every learner in the selection are listed.</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap justify-end gap-2">
                        <april:button type="submit">
                            <x-lucide-list-ordered class="mr-2 size-4" />
                            Work it out
                        </april:button>
                        @if ($chosen)
                            <april:button-link href="{{ route('rankings.index') }}" variant="outline">Clear</april:button-link>
                        @endif
                    </div>
                </form>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>{{ $chosen ? $groupName : 'The order' }}</slot:title>
            <slot:description>
                Two equal averages share a position. A learner with no published result is not in the order at all.
            </slot:description>
            <slot:content>
                @if (!$chosen)
                    <x-empty-state icon="lucide-list-ordered" title="Choose a class or group first"
                        description="Pick a class, a {{ strtolower(school_term('section', 'section')) }}, or a group above, then work out the order." />
                @elseif ($rows->isEmpty())
                    <x-empty-state icon="lucide-search-x" title="Nothing to put in order"
                        description="Nobody in this group has a published result for what you chose." />
                @else
                    <april:data-table>
                        <slot:header>
                            <april:data-table-row>
                                <april:data-table-head>Position</april:data-table-head>
                                <april:data-table-head>Learner</april:data-table-head>
                                <april:data-table-head>Average</april:data-table-head>
                                <april:data-table-head class="text-right">Subjects counted</april:data-table-head>
                            </april:data-table-row>
                        </slot:header>
                        <slot:body>
                            @foreach ($rows as $row)
                                @php $learner = $learners->get($row['student_record_id']); @endphp
                                <april:data-table-row>
                                    <april:data-table-cell class="font-medium">{{ $row['position'] }}</april:data-table-cell>
                                    <april:data-table-cell>
                                        {{ $learner?->user?->name ?? 'Unnamed' }}
                                        <span class="block text-xs text-muted-foreground">{{ $learner?->admission_number }}</span>
                                    </april:data-table-cell>
                                    <april:data-table-cell>{{ number_format($row['average'], 2) }}%</april:data-table-cell>
                                    <april:data-table-cell class="text-right text-muted-foreground">{{ $row['subjects'] }}</april:data-table-cell>
                                </april:data-table-row>
                            @endforeach
                        </slot:body>
                    </april:data-table>
                @endif
            </slot:content>
        </april:card>
    </div>
@endsection
