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
            <slot:description>
                Keep the basics simple. Add classes and subjects below; every item is required unless you choose an
                advanced rule.
            </slot:description>
            <slot:content>
                <div class="space-y-6">
                    <dl class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
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
                        <div class="rounded-lg border p-4">
                            <dt class="text-sm text-muted-foreground">Stage rule</dt>
                            <dd class="text-lg font-semibold">
                                @if ($plan->completion_operator === 'any')
                                    Any item
                                @elseif ($plan->completion_operator === 'at_least')
                                    At least {{ $plan->required_count }} items
                                @else
                                    All items
                                @endif
                            </dd>
                        </div>
                    </dl>

                    @if ($canWrite)
                        <form method="POST" action="{{ route('graduation-plans.update', $plan) }}"
                            class="space-y-5 border-t pt-6">
                            @csrf
                            @method('PUT')

                            <div class="grid gap-4 lg:grid-cols-12 lg:items-start">
                                <div class="flex flex-col gap-2 lg:col-span-5">
                                    <april:label for="name">Name</april:label>
                                    <april:input id="name" name="name" value="{{ old('name', $plan->name) }}" required />
                                    @error('name') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                                </div>

                                <label class="flex items-center gap-2 text-sm lg:col-span-3 lg:items-start lg:pt-7">
                                    <input type="hidden" name="is_active" value="0">
                                    <april:input type="checkbox" name="is_active" value="1" :checked="old('is_active', $plan->is_active)" />
                                    This plan is in use
                                </label>

                                <input type="hidden" name="cohort_id" value="{{ $plan->cohort_id }}">
                                <div class="flex flex-col gap-2 lg:col-span-12">
                                    <april:label for="description">Description (optional)</april:label>
                                    <april:textarea id="description" name="description" rows="2">{{ old('description', $plan->description) }}</april:textarea>
                                    @error('description') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <details class="rounded-md border p-4" {{ $plan->completion_operator !== 'all' || $plan->uses_credits || (old('completion_operator') && old('completion_operator') !== 'all') ? 'open' : '' }}>
                                <summary class="cursor-pointer text-sm font-semibold">Advanced rules (optional)</summary>
                                <div class="mt-4 space-y-4">
                                    <p class="text-sm text-muted-foreground">
                                        Use these options for degree plans, elective groups, or a pathway where only
                                        some choices are needed.
                                    </p>
                                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4 lg:items-start">
                                        <div class="flex flex-col gap-2 lg:col-span-2">
                                            <label for="completion_operator" class="text-sm font-medium">How should the choices count?</label>
                                            <april:native-select id="completion_operator" name="completion_operator">
                                                <option value="all" @selected(old('completion_operator', $plan->completion_operator) === 'all')>All of these</option>
                                                <option value="any" @selected(old('completion_operator', $plan->completion_operator) === 'any')>Any one of these</option>
                                                <option value="at_least" @selected(old('completion_operator', $plan->completion_operator) === 'at_least')>Choose a number of these</option>
                                            </april:native-select>
                                            @error('completion_operator') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                                        </div>

                                        <div class="flex flex-col gap-2">
                                            <label for="required_count" class="text-sm font-medium">How many are needed?</label>
                                            <april:input id="required_count" name="required_count" type="number" min="1"
                                                value="{{ old('required_count', $plan->required_count) }}" placeholder="For example, 4 of 5" />
                                            @error('required_count') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                                        </div>

                                        <label class="flex min-h-[4.25rem] items-center gap-2 text-sm lg:items-start lg:pt-7">
                                            <input type="hidden" name="uses_credits" value="0">
                                            <april:input type="checkbox" name="uses_credits" value="1" :checked="old('uses_credits', $plan->uses_credits)" />
                                            Count credits
                                        </label>

                                        <div class="flex flex-col gap-2">
                                            <april:label for="required_credits">Credits needed</april:label>
                                            <april:input id="required_credits" name="required_credits" type="number" min="1"
                                                value="{{ old('required_credits', $plan->required_credits) }}" placeholder="For example, 120" />
                                            @error('required_credits') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                </div>
                            </details>

                            <april:button type="submit">
                                <x-lucide-save class="mr-2 size-4" />
                                Save the plan
                            </april:button>
                        </form>
                    @endif
                </div>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>Build the progression</slot:title>
            <slot:description>
                Use stages for years or pathways. Each stage can require all items, or any item. Nest another stage
                when a branch needs its own rule. A stage marked NOT must not be complete.
            </slot:description>
            <slot:content>
                <div class="space-y-6">
                    @if ($plan->parent !== null)
                        <p class="rounded-md border border-primary/20 bg-primary/5 px-3 py-2 text-sm text-muted-foreground">
                            This is a stage inside
                            <a class="font-medium underline" href="{{ route('graduation-plans.show', $plan->parent) }}">{{ $plan->parent->name }}</a>.
                        </p>
                    @endif

                    @if ($plan->children->isEmpty())
                        <x-empty-state icon="lucide-git-branch" title="No stages below this plan yet"
                            description="Add KG1, KG2, KG3, or a nested choice group to model a multi-year pathway." />
                    @else
                        <div class="rounded-lg border p-4">
                            <p class="mb-3 text-sm font-medium">Stages below this plan</p>
                            @include('pages.graduation-plan.partials.stage-tree', ['stages' => $plan->children, 'depth' => 0])
                        </div>
                    @endif

                    @if ($canWrite)
                        <form method="POST" action="{{ route('graduation-plans.children.store', $plan) }}"
                            class="space-y-4 border-t pt-6">
                            @csrf

                            <div class="grid gap-4 lg:grid-cols-6 lg:items-start">
                                <div class="flex flex-col gap-2 lg:col-span-3">
                                    <label for="child_name" class="text-sm font-medium">Class or pathway stage</label>
                                    <april:input id="child_name" name="name" value="{{ old('name') }}" required placeholder="Primary 1" />
                                    @error('name') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                                </div>

                                <p class="text-sm text-muted-foreground lg:col-span-3 lg:pt-7">
                                    All items in this stage are required by default.
                                </p>
                            </div>

                            <details class="rounded-md border p-4" {{ old('completion_operator') && old('completion_operator') !== 'all' ? 'open' : '' }}>
                                <summary class="cursor-pointer text-sm font-semibold">Advanced stage rules (optional)</summary>
                                <div class="mt-4 grid gap-4 md:grid-cols-2 lg:grid-cols-3 lg:items-start">
                                    <div class="flex flex-col gap-2">
                                        <label for="child_operator" class="text-sm font-medium">How should this stage count?</label>
                                        <april:native-select id="child_operator" name="completion_operator">
                                            <option value="all" @selected(old('completion_operator', 'all') === 'all')>All of these</option>
                                            <option value="any" @selected(old('completion_operator') === 'any')>Any one of these</option>
                                            <option value="at_least" @selected(old('completion_operator') === 'at_least')>Choose a number of these</option>
                                        </april:native-select>
                                    </div>

                                    <div class="flex flex-col gap-2">
                                        <label for="child_required_count" class="text-sm font-medium">How many are needed?</label>
                                        <april:input id="child_required_count" name="required_count" type="number" min="1"
                                            value="{{ old('required_count') }}" placeholder="For example, 4 of 5" />
                                        @error('required_count') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                                    </div>

                                    <label class="flex min-h-[4.25rem] items-center gap-2 text-sm lg:items-start lg:pt-7">
                                        <input type="hidden" name="is_negated" value="0">
                                        <april:input id="child_negated" type="checkbox" name="is_negated" value="1" :checked="old('is_negated')" />
                                        Exclude this stage (NOT)
                                    </label>
                                </div>
                            </details>

                            <april:button type="submit">
                                <x-lucide-git-branch-plus class="mr-2 size-4" />
                                Add stage
                            </april:button>
                        </form>
                    @endif
                </div>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>What a learner must finish</slot:title>
            <slot:description>
                A requirement that names a subject is judged from the newest published result. Use NOT for an item
                that must not be met. Requirements in this stage follow the stage rule above.
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
                                                @if ($requirement->is_negated)
                                                    Must not be met (NOT)
                                                @else
                                                    {{ $requirement->is_required ? 'Must be met' : 'Optional' }}
                                                @endif
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
                            class="space-y-4 border-t pt-6">
                            @csrf

                            <div class="grid gap-4 lg:grid-cols-12 lg:items-start">
                                <div class="flex flex-col gap-2 lg:col-span-4">
                                    <april:label for="description">Subject or requirement</april:label>
                                    <april:input id="description" name="description" value="{{ old('description') }}" required
                                        placeholder="Mathematics" />
                                    @error('description') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                                </div>

                                <div class="flex flex-col gap-2 lg:col-span-3">
                                    <april:label for="subject_id">Match a subject (optional)</april:label>
                                    <april:native-select id="subject_id" name="subject_id">
                                        <option value="">No subject named</option>
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->id }}" @selected(old('subject_id') == $subject->id)>{{ $subject->name }}</option>
                                        @endforeach
                                    </april:native-select>
                                </div>

                                <div class="flex flex-col gap-2 lg:col-span-2">
                                    <april:label for="pass_mark">Pass mark</april:label>
                                    <april:input id="pass_mark" name="pass_mark" type="number" step="0.01" min="0" max="100"
                                        value="{{ old('pass_mark', 50) }}" required />
                                    @error('pass_mark') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                                </div>

                                <label class="flex items-center gap-2 text-sm lg:col-span-3 lg:items-start lg:pt-7">
                                    <input type="hidden" name="is_required" value="0">
                                    <april:input type="checkbox" name="is_required" value="1" :checked="old('is_required', true)" />
                                    Required for graduation
                                </label>
                            </div>

                            <details class="rounded-md border p-4" {{ old('is_negated') === '1' || (old('credits') !== null && old('credits') != 1) ? 'open' : '' }}>
                                <summary class="cursor-pointer text-sm font-semibold">Advanced requirement options (optional)</summary>
                                <div class="mt-4 grid gap-4 md:grid-cols-2 lg:grid-cols-3 lg:items-start">
                                    <div class="flex flex-col gap-2">
                                        <input type="hidden" name="is_negated" value="0">
                                        <april:label for="credits">Credits</april:label>
                                        <april:input id="credits" name="credits" type="number" min="0" value="{{ old('credits', 1) }}" required />
                                        @error('credits') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                                    </div>

                                    <label class="flex min-h-[4.25rem] items-center gap-2 text-sm lg:items-start lg:pt-7">
                                        <april:input id="is_negated" type="checkbox" name="is_negated" value="1" :checked="old('is_negated')" />
                                        This must not be passed (NOT)
                                    </label>
                                </div>
                            </details>

                            <april:button type="submit">
                                <x-lucide-plus class="mr-2 size-4" />
                                Add subject or requirement
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

                        @if ($progress['stages'] !== [])
                            <div class="rounded-lg border p-4">
                                <p class="mb-3 text-sm font-medium">Stage progress</p>
                                @include('pages.graduation-plan.partials.progress-tree', ['stages' => $progress['stages'], 'depth' => 0])
                            </div>
                        @endif

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
