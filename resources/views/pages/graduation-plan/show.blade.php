@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('graduation-plans.index'), 'text' => 'Graduation plans'],
    ['text' => $plan->name, 'active'],
]])

@section('title', $plan->name)
@section('page_heading', $plan->name)

@section('page_actions')
    <april:button-link href="{{ route('graduation-plans.index') }}" variant="outline">
        <x-lucide-arrow-left class="mr-2 size-4" />
        Back to plans
    </april:button-link>
@endsection

@php
    $canWrite = auth()->user()->can('update', $plan);
    $stateLabels = [
        'met' => 'Met',
        'exempt' => 'Excused',
        'not_met' => 'Not met',
        'no_result' => 'No published result',
        'not_judged' => 'Judged elsewhere',
    ];
@endphp

@section('content')
    <div class="space-y-6">
        <april:card>
            <slot:title>The plan</slot:title>
            <slot:description>{{ $plan->description ?? 'This plan has no description.' }}</slot:description>
            <slot:content>
                <div class="space-y-6">
                    <dl class="grid gap-4 sm:grid-cols-3">
                        <div class="rounded-lg border p-4">
                            <dt class="text-sm text-muted-foreground">Credits</dt>
                            <dd class="text-lg font-semibold">
                                {{ $plan->uses_credits ? $plan->required_credits.' needed' : 'Not counted' }}
                            </dd>
                        </div>
                        <div class="rounded-lg border p-4">
                            <dt class="text-sm text-muted-foreground">Who it is for</dt>
                            <dd class="text-lg font-semibold">{{ $plan->cohort?->name ?? 'Every learner' }}</dd>
                        </div>
                        <div class="rounded-lg border p-4">
                            <dt class="text-sm text-muted-foreground">State</dt>
                            <dd class="text-lg font-semibold">{{ $plan->is_active ? 'In use' : 'Closed' }}</dd>
                        </div>
                    </dl>

                    @if ($canWrite)
                        <form method="POST" action="{{ route('graduation-plans.update', $plan) }}"
                            class="grid gap-4 border-t pt-6 lg:grid-cols-4 lg:items-end">
                            @csrf
                            @method('PUT')

                            <div class="flex flex-col gap-2">
                                <april:label for="name">Name</april:label>
                                <april:input id="name" name="name" value="{{ old('name', $plan->name) }}" required />
                                @error('name') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex flex-col gap-2">
                                <april:label for="required_credits">Credits needed</april:label>
                                <april:input id="required_credits" name="required_credits" type="number" min="1"
                                    value="{{ old('required_credits', $plan->required_credits) }}" />
                                @error('required_credits') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                            </div>

                            <label class="flex items-center gap-2 text-sm">
                                <input type="hidden" name="uses_credits" value="0">
                                <input type="checkbox" name="uses_credits" value="1" @checked(old('uses_credits', $plan->uses_credits))
                                    class="size-4 rounded border-input text-primary focus:ring-2 focus:ring-ring">
                                Count credits
                            </label>

                            <label class="flex items-center gap-2 text-sm">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $plan->is_active))
                                    class="size-4 rounded border-input text-primary focus:ring-2 focus:ring-ring">
                                This plan is in use
                            </label>

                            <input type="hidden" name="description" value="{{ $plan->description }}">
                            <input type="hidden" name="cohort_id" value="{{ $plan->cohort_id }}">

                            <april:button type="submit" class="lg:col-span-4 lg:justify-self-start">
                                <x-lucide-save class="mr-2 size-4" />
                                Save the plan
                            </april:button>
                        </form>
                    @endif
                </div>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>What a learner must finish</slot:title>
            <slot:description>
                A requirement that names a subject is judged from the newest published result. One that names no
                subject is judged by the school and recorded elsewhere.
            </slot:description>
            <slot:content>
                <div class="space-y-6">
                    @if ($plan->requirements->isEmpty())
                        <x-empty-state icon="lucide-list-checks" title="This plan asks for nothing yet"
                            description="Add the subjects a learner must pass." />
                    @else
                        <april:data-table>
                            <slot:header>
                                <april:data-table-row>
                                    <april:data-table-head>Requirement</april:data-table-head>
                                    <april:data-table-head>Subject</april:data-table-head>
                                    <april:data-table-head>Pass mark</april:data-table-head>
                                    <april:data-table-head>Credits</april:data-table-head>
                                    <april:data-table-head class="text-right">Actions</april:data-table-head>
                                </april:data-table-row>
                            </slot:header>
                            <slot:body>
                                @foreach ($plan->requirements as $requirement)
                                    <april:data-table-row>
                                        <april:data-table-cell class="font-medium">
                                            {{ $requirement->description }}
                                            <span class="block text-xs text-muted-foreground">
                                                {{ $requirement->is_required ? 'Must be met' : 'Optional' }}
                                            </span>
                                        </april:data-table-cell>
                                        <april:data-table-cell class="text-muted-foreground">
                                            {{ $requirement->subject?->name ?? 'No subject named' }}
                                        </april:data-table-cell>
                                        <april:data-table-cell>{{ number_format((float) $requirement->pass_mark, 2) }}%</april:data-table-cell>
                                        <april:data-table-cell>{{ $requirement->credits }}</april:data-table-cell>
                                        <april:data-table-cell class="text-right">
                                            @if ($canWrite)
                                                <form method="POST" action="{{ route('graduation-plans.requirements.destroy', [$plan, $requirement]) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <april:button type="submit" variant="outline" size="sm">
                                                        <x-lucide-trash-2 class="mr-1 size-4" />
                                                        Remove
                                                    </april:button>
                                                </form>
                                            @endif
                                        </april:data-table-cell>
                                    </april:data-table-row>
                                @endforeach
                            </slot:body>
                        </april:data-table>
                    @endif

                    @if ($canWrite)
                        <form method="POST" action="{{ route('graduation-plans.requirements.store', $plan) }}"
                            class="grid gap-4 border-t pt-6 lg:grid-cols-5 lg:items-end">
                            @csrf

                            <div class="flex flex-col gap-2 lg:col-span-2">
                                <april:label for="description">What must be finished</april:label>
                                <april:input id="description" name="description" value="{{ old('description') }}" required
                                    placeholder="Pass mathematics" />
                                @error('description') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex flex-col gap-2">
                                <april:label for="subject_id">Subject</april:label>
                                <april:native-select id="subject_id" name="subject_id">
                                    <option value="">No subject</option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}" @selected(old('subject_id') == $subject->id)>{{ $subject->name }}</option>
                                    @endforeach
                                </april:native-select>
                            </div>

                            <div class="flex flex-col gap-2">
                                <april:label for="pass_mark">Pass mark</april:label>
                                <april:input id="pass_mark" name="pass_mark" type="number" step="0.01" min="0" max="100"
                                    value="{{ old('pass_mark', 50) }}" required />
                                @error('pass_mark') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex flex-col gap-2">
                                <april:label for="credits">Credits</april:label>
                                <april:input id="credits" name="credits" type="number" min="0" value="{{ old('credits', 1) }}" required />
                                @error('credits') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                            </div>

                            <label class="flex items-center gap-2 text-sm lg:col-span-2">
                                <input type="hidden" name="is_required" value="0">
                                <input type="checkbox" name="is_required" value="1" @checked(old('is_required', true))
                                    class="size-4 rounded border-input text-primary focus:ring-2 focus:ring-ring">
                                A learner cannot graduate without this
                            </label>

                            <april:button type="submit" class="lg:col-span-5 lg:justify-self-start">
                                <x-lucide-plus class="mr-2 size-4" />
                                Add this requirement
                            </april:button>
                        </form>
                    @endif
                </div>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>How far one learner is</slot:title>
            <slot:description>Choose a learner to read their standing against this plan.</slot:description>
            <slot:content>
                <div class="space-y-6">
                    <form method="GET" action="{{ route('graduation-plans.show', $plan) }}" class="flex flex-wrap items-end gap-2">
                        <div class="flex flex-col gap-2">
                            <april:label for="student_record_id">Learner</april:label>
                            <april:native-select id="student_record_id" name="student_record_id">
                                <option value="">Choose a learner</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}" @selected($learner?->id === $student->id)>
                                        {{ $student->user?->name ?? 'Unnamed' }} · {{ $student->admission_number }}
                                    </option>
                                @endforeach
                            </april:native-select>
                        </div>
                        <april:button type="submit">
                            <x-lucide-search class="mr-2 size-4" />
                            Check
                        </april:button>
                        @if ($learner !== null)
                            <april:button-link href="{{ route('graduation-plans.show', $plan) }}" variant="outline">Clear</april:button-link>
                        @endif
                    </form>

                    @if ($progress === null)
                        <x-empty-state icon="lucide-user-search" title="Choose a learner first"
                            description="The plan is judged one learner at a time, from their published results." />
                    @else
                        <div class="rounded-lg border p-4">
                            <p class="text-sm text-muted-foreground">{{ $learner->user?->name ?? $learner->admission_number }}</p>
                            <p class="text-2xl font-semibold">
                                {{ $progress['is_complete'] ? 'Has finished the plan' : 'Still working through the plan' }}
                            </p>
                            @if ($progress['credits_required'] !== null)
                                <p class="mt-1 text-sm text-muted-foreground">
                                    {{ $progress['credits_earned'] }} of {{ $progress['credits_required'] }} credits earned
                                </p>
                            @else
                                <p class="mt-1 text-sm text-muted-foreground">{{ $progress['credits_earned'] }} credits earned</p>
                            @endif
                        </div>

                        @if ($progress['requirements'] !== [])
                            <april:data-table>
                                <slot:header>
                                    <april:data-table-row>
                                        <april:data-table-head>Requirement</april:data-table-head>
                                        <april:data-table-head>Result</april:data-table-head>
                                        <april:data-table-head>Standing</april:data-table-head>
                                        <april:data-table-head class="text-right">Actions</april:data-table-head>
                                    </april:data-table-row>
                                </slot:header>
                                <slot:body>
                                    @foreach ($progress['requirements'] as $line)
                                        @php $exemption = $exemptions->get($line['requirement_id']); @endphp
                                        <april:data-table-row>
                                            <april:data-table-cell class="font-medium">{{ $line['description'] }}</april:data-table-cell>
                                            <april:data-table-cell class="text-muted-foreground">
                                                {{ $line['percentage'] === null ? '—' : number_format($line['percentage'], 2).'%' }}
                                            </april:data-table-cell>
                                            <april:data-table-cell>
                                                <span class="inline-flex whitespace-nowrap items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold">
                                                    {{ $stateLabels[$line['state']] ?? $line['state'] }}
                                                </span>
                                                @if ($exemption !== null)
                                                    <span class="mt-1 block text-xs text-muted-foreground">{{ $exemption->reason }}</span>
                                                @endif
                                            </april:data-table-cell>
                                            <april:data-table-cell class="text-right">
                                                @if ($canWrite)
                                                    @if ($exemption === null)
                                                        <form method="POST" action="{{ route('graduation-plans.exemptions.store', $plan) }}"
                                                            class="flex items-end justify-end gap-2">
                                                            @csrf
                                                            <input type="hidden" name="graduation_requirement_id" value="{{ $line['requirement_id'] }}">
                                                            <input type="hidden" name="student_record_id" value="{{ $learner->id }}">
                                                            <april:input name="reason" placeholder="Why excuse them" required class="w-48" />
                                                            <april:button type="submit" variant="outline" size="sm">Excuse</april:button>
                                                        </form>
                                                    @else
                                                        <form method="POST" action="{{ route('graduation-plans.exemptions.destroy', [$plan, $exemption]) }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <april:button type="submit" variant="outline" size="sm">Take it back</april:button>
                                                        </form>
                                                    @endif
                                                @endif
                                            </april:data-table-cell>
                                        </april:data-table-row>
                                    @endforeach
                                </slot:body>
                            </april:data-table>
                        @endif
                    @endif
                </div>
            </slot:content>
        </april:card>
    </div>
@endsection
