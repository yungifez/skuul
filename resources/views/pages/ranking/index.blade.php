@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('rankings.index'), 'text' => 'Rankings', 'active'],
]])

@section('title', 'Rankings')
@section('page_heading', 'Rankings')

@php
    $chosen = $academicLevel !== null || $section !== null || $cohort !== null;
    $groupName = $cohort?->name ?? $section?->name ?? $academicLevel?->name;
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
                A position is worked out from the published results, every time you open this screen. It is never
                stored on a learner, so correcting a result changes the order instead of leaving it wrong.
            </slot:description>
            <slot:content>
                <form method="GET" action="{{ route('rankings.index') }}"
                    class="grid gap-4 md:grid-cols-2 lg:grid-cols-[repeat(5,minmax(0,1fr))_auto] lg:items-end">
                    <div class="flex flex-col gap-2">
                        <april:label for="academic_level_id">{{ school_term('class_level', 'Class') }} or group</april:label>
                        <april:native-select id="academic_level_id" name="academic_level_id" class="w-full min-w-0">
                            <option value="">Choose a {{ strtolower(school_term('class_level', 'class')) }} or group</option>
                            <optgroup label="{{ school_terms('class_level', 'Classes') }}">
                                @foreach ($academicLevels->where('is_group', false) as $option)
                                    <option value="{{ $option->id }}" @selected($academicLevel?->id === $option->id)>
                                        {{ $option->parent?->name ? $option->parent->name.' → ' : '' }}{{ $option->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                            @if ($academicLevels->where('is_group', true)->isNotEmpty())
                                <optgroup label="Groups · whole-group teaching">
                                    @foreach ($academicLevels->where('is_group', true) as $option)
                                        <option value="{{ $option->id }}" @selected($academicLevel?->id === $option->id)>
                                            {{ $option->parent?->name ? $option->parent->name.' → ' : '' }}{{ $option->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        </april:native-select>
                        <p class="text-xs text-muted-foreground">Choose a class, or a group such as Kindergarten to include its child classes.</p>
                    </div>

                    <div class="flex flex-col gap-2">
                        <april:label for="academic_cycle_section_id">{{ school_term('section', 'Section') }}</april:label>
                        <april:native-select id="academic_cycle_section_id" name="academic_cycle_section_id" class="w-full min-w-0"
                            :disabled="$academicLevel === null">
                            <option value="">Every section in this {{ strtolower(school_term('class_level', 'class')) }}</option>
                            @foreach ($sections as $option)
                                <option value="{{ $option->id }}" @selected($section?->id === $option->id)>
                                    {{ $option->academicLevel?->name }} · {{ $option->label ?? $option->name }}
                                </option>
                            @endforeach
                        </april:native-select>
                        <p class="text-xs text-muted-foreground">Narrow the class to one section.</p>
                    </div>

                    <div class="flex flex-col gap-2">
                        <april:label for="cohort_id">Or a learner group</april:label>
                        <april:native-select id="cohort_id" name="cohort_id" class="w-full min-w-0">
                            <option value="">Choose one</option>
                            @foreach ($cohorts as $option)
                                <option value="{{ $option->id }}" @selected($cohort?->id === $option->id)>{{ $option->name }}</option>
                            @endforeach
                        </april:native-select>
                        <p class="text-xs text-muted-foreground">A learner group wins when both are chosen.</p>
                    </div>

                    <div class="flex flex-col gap-2">
                        <april:label for="academic_period_id">{{ school_term('period', 'Period') }}</april:label>
                        <april:native-select id="academic_period_id" name="academic_period_id" class="w-full min-w-0">
                            <option value="">Every {{ school_term('period', 'period') }}</option>
                            @foreach ($periods as $option)
                                <option value="{{ $option->id }}" @selected($period?->id === $option->id)>{{ $option->label ?? $option->name }}</option>
                            @endforeach
                        </april:native-select>
                    </div>

                    <div class="flex flex-col gap-2">
                        <april:label for="course_offering_id">Subject</april:label>
                        <april:native-select id="course_offering_id" name="course_offering_id" class="w-full min-w-0">
                            <option value="">Every subject</option>
                            @foreach ($offerings as $option)
                                <option value="{{ $option->id }}" @selected($offering?->id === $option->id)>
                                    {{ $option->subject?->name ?? 'Unnamed' }} · {{ $option->academicLevel?->name ?? 'Unscoped class' }} · {{ school_roster_label($option->roster_mode) }}
                                </option>
                            @endforeach
                        </april:native-select>
                    </div>

                    <div class="flex flex-wrap gap-2">
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
