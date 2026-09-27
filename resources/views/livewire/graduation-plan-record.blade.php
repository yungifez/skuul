<div class="flex flex-col gap-10">
    @php
        $controlClasses = 'mt-1 h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
        $ruleLabel = fn ($operator, $count, $credits) => match ($operator) {
            'any' => 'Any one item',
            'at_least' => 'At least '.$count.' items',
            'at_least_credits' => 'At least '.$credits.' credits',
            default => 'All items',
        };
        $stateLabels = [
            'met' => 'Met',
            'exempt' => 'Excused',
            'not_met' => 'Not met',
            'no_result' => 'No published result',
            'not_judged' => 'Judged elsewhere',
        ];
    @endphp

    <section class="flex flex-col gap-3" aria-labelledby="plan-heading">
        <div class="flex items-center justify-between gap-3">
            <h2 id="plan-heading" class="text-base font-semibold">{{ $plan->parent === null ? 'The plan' : 'The stage' }}</h2>
            @if ($canWrite && !$isEditing)
                <april:button type="button" variant="outline" class="h-11 select-none" wire:click="startEditing">
                    <x-lucide-pencil class="mr-2 size-4" aria-hidden="true" />Change
                </april:button>
            @endif
        </div>

        @if ($plan->parent !== null)
            <p class="text-sm text-muted-foreground">
                A stage inside <a href="{{ route('graduation-plans.show', $plan->parent) }}" class="font-medium text-foreground underline underline-offset-4">{{ $plan->parent->name }}</a>.
            </p>
        @endif

        @if ($isEmpty)
            <p class="flex items-center gap-2 text-sm font-medium">
                <x-lucide-circle-alert class="size-4 shrink-0" aria-hidden="true" />Asks for nothing yet, so no learner finishes it. Add a requirement or a stage below.
            </p>
        @elseif ($plan->completion_operator === 'at_least' && $plan->required_count > $countedItems)
            <p class="flex items-center gap-2 text-sm font-medium">
                <x-lucide-circle-alert class="size-4 shrink-0" aria-hidden="true" />Asks for {{ $plan->required_count }} items but holds {{ $countedItems }}, so no learner can finish it.
            </p>
        @endif

        @if ($isEditing)
            <form wire:submit="save" class="flex flex-col gap-3" aria-label="Change the plan">
                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <label for="name" class="text-sm text-muted-foreground">Name</label>
                        <input id="name" wire:model="name" required maxlength="100" class="{{ $controlClasses }}" {{ field_error_bindings('name') }}>
                        <x-field-error name="name" class="mt-1" />
                    </div>
                    <div>
                        <label for="description" class="text-sm text-muted-foreground">What it is (optional)</label>
                        <input id="description" wire:model="description" maxlength="1000" class="{{ $controlClasses }}" {{ field_error_bindings('description') }}>
                        <x-field-error name="description" class="mt-1" />
                    </div>
                </div>
                <label class="flex min-h-11 select-none items-center gap-3 text-sm">
                    <input type="checkbox" wire:model="isActive" class="size-5 rounded border-input">
                    In use. A closed plan leaves the portal, and a closed stage stops counting.
                </label>
                @include('livewire.partials.graduation-rule-fields', [
                    'operatorProperty' => 'completionOperator',
                    'countProperty' => 'requiredCount',
                    'creditsProperty' => 'requiredCredits',
                    'creditsToggleProperty' => 'usesCredits',
                    'operator' => $completionOperator,
                    'isCountingCredits' => $usesCredits,
                ])
                <div class="flex justify-end gap-2">
                    <april:button type="button" variant="ghost" class="h-11 select-none" wire:click="stopEditing">Cancel</april:button>
                    <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save">Save</april:button>
                </div>
            </form>
        @else
            <dl class="grid gap-x-6 gap-y-3 text-sm sm:grid-cols-4">
                <div>
                    <dt class="text-muted-foreground">Rule</dt>
                    <dd>{{ $ruleLabel($plan->completion_operator, $plan->required_count, $plan->required_credits) }}{{ $plan->is_negated ? ' · must not be complete' : '' }}</dd>
                </div>
                <div>
                    <dt class="text-muted-foreground">Credits</dt>
                    <dd>{{ $plan->uses_credits ? $plan->required_credits.' needed' : '—' }}</dd>
                </div>
                <div>
                    <dt class="text-muted-foreground">Who it is for</dt>
                    <dd>{{ $plan->cohort?->name ?? 'Every learner' }}</dd>
                </div>
                <div>
                    <dt class="text-muted-foreground">State</dt>
                    <dd>{{ $plan->is_active ? 'In use' : 'Closed' }}</dd>
                </div>
                <div class="sm:col-span-4">
                    <dt class="text-muted-foreground">What it is</dt>
                    <dd>{{ $plan->description ?? '—' }}</dd>
                </div>
            </dl>
        @endif
    </section>

    <section class="flex flex-col gap-3" aria-labelledby="stages-heading">
        <h2 id="stages-heading" class="text-base font-semibold">Stages</h2>
        <p class="text-sm text-muted-foreground">Use stages for years or pathways. Each has its own rule.</p>

        @if ($plan->children->isEmpty())
            <p class="text-sm text-muted-foreground">No stage below this one.</p>
        @else
            @include('pages.graduation-plan.partials.stage-tree', ['stages' => $plan->children, 'depth' => 0])
        @endif

        @if ($canWrite)
            <form wire:submit="addStage" class="flex flex-col gap-3" aria-label="Add a stage">
                <div class="grid gap-3 sm:grid-cols-[1fr_auto] sm:items-end">
                    <div>
                        <label for="stage_name" class="text-sm text-muted-foreground">Class or pathway stage</label>
                        <input id="stage_name" wire:model="stageName" required maxlength="100" placeholder="Primary 1" class="{{ $controlClasses }}" {{ field_error_bindings('stageName') }}>
                        <x-field-error name="stageName" class="mt-1" />
                    </div>
                    <april:button type="submit" variant="outline" class="h-11 select-none" wire:loading.attr="disabled" wire:target="addStage">
                        <x-lucide-git-branch-plus class="mr-2 size-4" aria-hidden="true" />Add stage
                    </april:button>
                </div>
                @include('livewire.partials.graduation-rule-fields', [
                    'operatorProperty' => 'stageCompletionOperator',
                    'countProperty' => 'stageRequiredCount',
                    'creditsProperty' => 'stageRequiredCredits',
                    'creditsToggleProperty' => null,
                    'operator' => $stageCompletionOperator,
                    'isCountingCredits' => false,
                ])
                <label class="flex min-h-11 select-none items-center gap-3 text-sm">
                    <input type="checkbox" wire:model="stageIsNegated" class="size-5 rounded border-input">
                    The learner must not complete this stage (NOT)
                </label>
            </form>
        @endif
    </section>

    <section class="flex flex-col gap-3" aria-labelledby="requirements-heading">
        <h2 id="requirements-heading" class="text-base font-semibold">What a learner must finish</h2>
        <p class="text-sm text-muted-foreground">A requirement that names a subject is judged from the newest published result.</p>

        @if ($plan->requirements->isEmpty())
            <p class="text-sm text-muted-foreground">Nothing yet.</p>
        @else
            <ul class="divide-y border-y">
                @foreach ($plan->requirements as $requirement)
                    <li wire:key="requirement-{{ $requirement->id }}" class="flex items-center gap-3 py-3">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">{{ $requirement->description }}</p>
                            <p class="truncate text-xs text-muted-foreground">
                                {{ $requirement->is_negated ? 'Must not be met (NOT)' : ($requirement->is_required ? 'Must be met' : 'Optional') }}
                                · {{ $requirement->subject?->name ?? '—' }} · pass at {{ rtrim(rtrim(number_format((float) $requirement->pass_mark, 2), '0'), '.') }}%
                                · {{ $requirement->credits }} {{ Str::plural('credit', $requirement->credits) }}
                            </p>
                        </div>
                        @if ($canWrite)
                            <april:dropdown-menu>
                                <slot:trigger>
                                    <april:button type="button" variant="ghost" size="icon" class="size-11 select-none" aria-label="More for {{ $requirement->description }}">
                                        <x-lucide-ellipsis class="size-4" />
                                    </april:button>
                                </slot:trigger>
                                <slot:content align="end">
                                    <april:dropdown-menu-item wire:click="removeRequirement({{ $requirement->id }})" wire:confirm="Remove {{ $requirement->description }}? Every learner excused from it loses the excusal."><x-lucide-trash-2 class="mr-2 size-4" />Remove</april:dropdown-menu-item>
                                </slot:content>
                            </april:dropdown-menu>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif

        @if ($canWrite)
            <form wire:submit="addRequirement" class="flex flex-col gap-3" aria-label="Add a requirement">
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="sm:col-span-2">
                        <label for="requirement_description" class="text-sm text-muted-foreground">Subject or requirement</label>
                        <input id="requirement_description" wire:model="requirementDescription" required maxlength="255" placeholder="Pass mathematics" class="{{ $controlClasses }}" {{ field_error_bindings('requirementDescription') }}>
                        <x-field-error name="requirementDescription" class="mt-1" />
                    </div>
                    <div>
                        <label for="requirement_subject_id" class="text-sm text-muted-foreground">Judged from (optional)</label>
                        <select id="requirement_subject_id" wire:model="requirementSubjectId" class="{{ $controlClasses }}" {{ field_error_bindings('requirementSubjectId') }}>
                            <option value="">No subject</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                        <x-field-error name="requirementSubjectId" class="mt-1" />
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="requirement_pass_mark" class="text-sm text-muted-foreground">Pass mark %</label>
                            <input id="requirement_pass_mark" type="number" inputmode="decimal" min="0" max="100" step="0.01" wire:model="requirementPassMark" required class="{{ $controlClasses }}" {{ field_error_bindings('requirementPassMark') }}>
                            <x-field-error name="requirementPassMark" class="mt-1" />
                        </div>
                        <div>
                            <label for="requirement_credits" class="text-sm text-muted-foreground">Credits</label>
                            <input id="requirement_credits" type="number" inputmode="numeric" min="0" max="100" wire:model="requirementCredits" required class="{{ $controlClasses }}" {{ field_error_bindings('requirementCredits') }}>
                            <x-field-error name="requirementCredits" class="mt-1" />
                        </div>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-x-6">
                    <label class="flex min-h-11 select-none items-center gap-3 text-sm">
                        <input type="checkbox" wire:model="requirementIsRequired" class="size-5 rounded border-input">
                        Required for graduation
                    </label>
                    <label class="flex min-h-11 select-none items-center gap-3 text-sm">
                        <input type="checkbox" wire:model="requirementIsNegated" class="size-5 rounded border-input">
                        Must not be passed (NOT)
                    </label>
                    <april:button type="submit" variant="outline" class="ml-auto h-11 select-none" wire:loading.attr="disabled" wire:target="addRequirement">
                        <x-lucide-plus class="mr-2 size-4" aria-hidden="true" />Add this requirement
                    </april:button>
                </div>
            </form>
        @endif
    </section>

    <section class="flex flex-col gap-3" aria-labelledby="learner-heading">
        <h2 id="learner-heading" class="text-base font-semibold">How far one learner is</h2>
        <div class="max-w-md">
            <label for="learner_id" class="text-sm text-muted-foreground">Learner</label>
            <select id="learner_id" wire:model.live="learnerId" class="{{ $controlClasses }}">
                <option value="">Choose a learner</option>
                @foreach ($students as $student)
                    <option value="{{ $student->id }}">{{ $student->user?->name ?? '—' }} · {{ $student->admission_number }}</option>
                @endforeach
            </select>
        </div>

        @if ($progress !== null)
            <div wire:key="progress-{{ $learner->id }}" class="flex flex-col gap-3">
                <p class="text-sm">
                    <span class="font-semibold">{{ $progress['is_complete'] ? 'Has finished the plan' : 'Still working through the plan' }}</span>
                    <span class="text-muted-foreground">· {{ $progress['credits_earned'] }}{{ $progress['credits_required'] !== null ? ' of '.$progress['credits_required'] : '' }} credits earned</span>
                </p>

                @if ($progress['stages'] !== [])
                    @include('pages.graduation-plan.partials.progress-tree', ['stages' => $progress['stages'], 'depth' => 0])
                @endif

                @if ($progress['requirements'] !== [])
                    <ul class="divide-y border-y">
                        @foreach ($progress['requirements'] as $line)
                            @php($exemption = $exemptions->get($line['requirement_id']))
                            <li wire:key="line-{{ $line['requirement_id'] }}" class="flex flex-wrap items-center gap-3 py-3">
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium">{{ $line['description'] }}</p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ $stateLabels[$line['state']] ?? $line['state'] }} · {{ $line['percentage'] === null ? '—' : number_format($line['percentage'], 2).'%' }}
                                        @if ($exemption !== null)
                                            · {{ $exemption->reason }}
                                        @endif
                                    </p>
                                </div>
                                @if ($canWrite && $exemption === null)
                                    <form wire:submit="excuse({{ $line['requirement_id'] }})" class="flex w-full items-start gap-2 sm:w-auto" aria-label="Excuse from {{ $line['description'] }}">
                                        <div class="min-w-0 flex-1 sm:w-56">
                                            <label for="reason-{{ $line['requirement_id'] }}" class="sr-only">Why excuse them from {{ $line['description'] }}</label>
                                            <input id="reason-{{ $line['requirement_id'] }}" wire:model="reasons.{{ $line['requirement_id'] }}" maxlength="500" placeholder="Why excuse them" class="{{ $controlClasses }} mt-0" {{ field_error_bindings('reasons.'.$line['requirement_id']) }}>
                                            <x-field-error :name="'reasons.'.$line['requirement_id']" class="mt-1" />
                                        </div>
                                        <april:button type="submit" variant="outline" class="h-11 select-none">Excuse</april:button>
                                    </form>
                                @elseif ($canWrite)
                                    <april:dropdown-menu>
                                        <slot:trigger>
                                            <april:button type="button" variant="ghost" size="icon" class="size-11 select-none" aria-label="More for {{ $line['description'] }}">
                                                <x-lucide-ellipsis class="size-4" />
                                            </april:button>
                                        </slot:trigger>
                                        <slot:content align="end">
                                            <april:dropdown-menu-item wire:click="takeBack({{ $exemption->id }})" wire:confirm="Take back the excusal for {{ $learner->user?->name ?? 'this learner' }}?"><x-lucide-undo-2 class="mr-2 size-4" />Take the excusal back</april:dropdown-menu-item>
                                        </slot:content>
                                    </april:dropdown-menu>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @else
            <p class="text-sm text-muted-foreground">The plan is judged one learner at a time, from their published results.</p>
        @endif
    </section>
</div>
