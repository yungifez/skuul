<div class="mx-auto flex w-full max-w-3xl flex-col gap-10">
    @php
        use App\Enums\InstructionalModel;

        $yearLabel = strtolower(school_term('academic_year', 'school year'));
        $canAnswer = $isFutureCycle && $canSet;
        $controlClasses = 'h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
        $capabilitiesOf = fn (InstructionalModel $option): array => [
            ['text' => 'Subjects start with '.strtolower(school_roster_label($option->defaultRosterMode())), 'on' => true],
            ['text' => 'Combined '.strtolower(school_terms('section', 'sections')), 'on' => $option->allowsCombinedSections()],
            ['text' => 'Named learners', 'on' => $option->allowsIndividualRosters()],
        ];
    @endphp

    <section aria-labelledby="teaching-question" class="flex flex-col gap-4">
        <div>
            <h2 id="teaching-question" class="text-lg font-semibold text-foreground">{{ InstructionalModel::SETUP_QUESTION }}</h2>
            <p class="mt-1 text-sm text-muted-foreground" id="teaching-status">
                <span class="font-medium text-foreground">{{ $currentModel->label() }}</span>
                @if ($setting === null)
                    · Not answered yet
                @else
                    · Answered by {{ $setting->updatedBy?->name ?? 'a campus administrator' }}, {{ $setting->updated_at?->diffForHumans() }}
                @endif
                @if (!$isFutureCycle)
                    · Fixed for this {{ $yearLabel }}
                @endif
            </p>
        </div>

        @if ($canAnswer)
            <form wire:submit="save" class="flex flex-col gap-4">
                <fieldset>
                    <legend class="sr-only">{{ InstructionalModel::SETUP_QUESTION }}</legend>
                    <div class="divide-y rounded-lg border">
                        @foreach (InstructionalModel::cases() as $option)
                            <label wire:key="model-{{ $option->value }}" for="model-{{ $option->value }}" class="group flex min-h-11 cursor-pointer items-start gap-3 p-4 select-none has-[:checked]:bg-muted/50">
                                <input type="radio" id="model-{{ $option->value }}" wire:model="model" value="{{ $option->value }}" class="mt-0.5 size-4 shrink-0 accent-primary">
                                <span class="flex min-w-0 flex-col gap-1">
                                    <span class="text-sm font-medium text-foreground">
                                        {{ $option->setupAnswer() }}
                                        @if ($option === InstructionalModel::default())
                                            <span class="ml-1 text-xs font-normal text-muted-foreground">Default</span>
                                        @endif
                                    </span>
                                    <span class="text-sm text-muted-foreground">{{ school_instructional_model_description($option) }}</span>
                                    <span class="hidden flex-wrap gap-x-4 gap-y-1 pt-1 text-xs text-muted-foreground group-has-[:checked]:flex">
                                        @foreach ($capabilitiesOf($option) as $capability)
                                            <span @class(['inline-flex items-center gap-1', 'text-foreground' => $capability['on'], 'line-through' => !$capability['on']])>
                                                @if ($capability['on'])
                                                    <x-lucide-check class="size-3.5 shrink-0" />
                                                @else
                                                    <x-lucide-minus class="size-3.5 shrink-0" />
                                                @endif
                                                {{ $capability['text'] }}
                                            </span>
                                        @endforeach
                                    </span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>
                <x-field-error name="model" />

                <div class="flex flex-col gap-3 sm:flex-row sm:items-start">
                    <div class="min-w-0 flex-1">
                        <label for="instructional-model-reason" class="sr-only">Note</label>
                        <input id="instructional-model-reason" wire:model="reason" maxlength="500" autocomplete="off"
                            placeholder="Note (optional)" class="{{ $controlClasses }}" {{ field_error_bindings('reason') }}>
                        <x-field-error name="reason" class="mt-1" />
                    </div>
                    <april:button type="submit" class="h-11 w-full sm:w-auto" wire:loading.attr="disabled">Save teaching setup</april:button>
                </div>
            </form>
        @else
            <div class="rounded-lg border p-4">
                <p class="text-sm font-medium text-foreground">{{ $currentModel->setupAnswer() }}</p>
                <p class="mt-1 text-sm text-muted-foreground">{{ school_instructional_model_description($currentModel) }}</p>
            </div>
            @if ($isFutureCycle)
                <p class="text-sm text-muted-foreground">Only a campus administrator can change this.</p>
            @endif
        @endif
    </section>

    @if ($canMigrate)
        <section aria-labelledby="move-heading" x-data="{ open: @js($errors->hasAny(['moveTo', 'moveReason', 'confirmMove'])) }" class="flex flex-col gap-4">
            <div class="flex items-center justify-between gap-4 border-t pt-6">
                <h2 id="move-heading" class="text-base font-semibold text-foreground">Move this cycle mid-year</h2>
                <button type="button" x-on:click="open = !open" x-bind:aria-expanded="open" aria-controls="move-form"
                    class="inline-flex h-11 shrink-0 items-center gap-1 rounded-md border px-4 text-sm font-medium select-none hover:bg-muted">
                    <span x-text="open ? 'Cancel' : 'Move'">Move</span>
                </button>
            </div>

            <form id="move-form" wire:submit="migrate" x-show="open" x-cloak class="flex flex-col gap-4">
                <fieldset>
                    <legend class="mb-2 text-sm font-medium text-foreground">Move to</legend>
                    <div class="divide-y rounded-lg border">
                        @foreach (InstructionalModel::cases() as $option)
                            @continue($option === $currentModel)
                            <label wire:key="move-{{ $option->value }}" for="move-{{ $option->value }}" class="flex min-h-11 cursor-pointer items-start gap-3 p-4 select-none has-[:checked]:bg-muted/50">
                                <input type="radio" id="move-{{ $option->value }}" wire:model="moveTo" value="{{ $option->value }}" class="mt-0.5 size-4 shrink-0 accent-primary">
                                <span class="flex min-w-0 flex-col gap-1">
                                    <span class="text-sm font-medium text-foreground">{{ $option->label() }}</span>
                                    @if (isset($impacts[$option->value]))
                                        <span class="text-xs text-muted-foreground">
                                            {{ $impacts[$option->value]['offerings'] }} {{ Str::plural('subject', $impacts[$option->value]['offerings']) }} set up
                                            @if ($impacts[$option->value]['exceptions'] > 0)
                                                · {{ $impacts[$option->value]['exceptions'] }} keep a roster the new answer does not offer
                                            @endif
                                        </span>
                                    @endif
                                </span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>
                <x-field-error name="moveTo" />

                <div class="flex flex-col gap-2">
                    <label for="migrate-reason" class="text-sm font-medium">Why the cycle is moving</label>
                    <textarea id="migrate-reason" wire:model="moveReason" rows="3" maxlength="500"
                        class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring" {{ field_error_bindings('moveReason') }}></textarea>
                    <x-field-error name="moveReason" />
                </div>

                <div>
                    <label for="migrate-confirm" class="flex min-h-11 cursor-pointer items-center gap-3 text-sm select-none">
                        <input type="checkbox" id="migrate-confirm" wire:model="confirmMove" class="size-4 shrink-0 accent-destructive" {{ field_error_bindings('confirmMove') }}>
                        New work uses the new answer. Existing arrangements stay.
                    </label>
                    <x-field-error name="confirmMove" />
                </div>

                <div class="flex justify-end">
                    <april:button type="submit" variant="destructive" class="h-11 w-full sm:w-auto" wire:loading.attr="disabled">Move this cycle</april:button>
                </div>
            </form>
        </section>
    @endif

    @if ($migrations->isNotEmpty())
        <section aria-labelledby="moves-heading" class="flex flex-col gap-3">
            <h2 id="moves-heading" class="text-base font-semibold text-foreground">Moves</h2>
            <ol class="divide-y border-y">
                @foreach ($migrations as $migration)
                    <li wire:key="migration-{{ $migration->id }}" class="flex flex-col gap-1 py-4 sm:flex-row sm:items-start sm:justify-between sm:gap-6">
                        <div class="min-w-0">
                            <p class="flex flex-wrap items-center gap-2 text-sm">
                                <span class="text-muted-foreground">{{ $migration->from_model?->label() ?? 'No answer' }}</span>
                                <x-lucide-arrow-right class="size-3.5 text-muted-foreground" aria-hidden="true" />
                                <span class="font-medium text-foreground">{{ $migration->to_model->label() }}</span>
                            </p>
                            <p class="mt-1 text-sm text-muted-foreground">{{ $migration->reason }}</p>
                        </div>
                        <p class="shrink-0 text-xs text-muted-foreground sm:text-right">
                            {{ $migration->migratedBy?->name ?? 'A campus administrator' }} · {{ school_time($migration->created_at)?->format('M j, Y') }}
                        </p>
                    </li>
                @endforeach
            </ol>
        </section>
    @endif

    <section aria-labelledby="exceptions-heading" class="flex flex-col gap-3">
        <h2 id="exceptions-heading" class="text-base font-semibold text-foreground">Subjects taught differently</h2>

        @if ($exceptions->isEmpty())
            <p class="text-sm text-muted-foreground">None. Every subject follows the answer above.</p>
        @else
            <ul class="divide-y border-y">
                @foreach ($exceptions as $exception)
                    <li wire:key="exception-{{ $exception->id }}" class="flex items-start justify-between gap-4 py-4">
                        <div class="min-w-0">
                            <p class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm">
                                <span @class(['font-medium', 'text-foreground' => $exception->isRunning(), 'text-muted-foreground line-through' => !$exception->isRunning()])>{{ $exception->subject?->name }}</span>
                                <span class="text-muted-foreground">{{ $exception->coverage() }} · {{ school_roster_label($exception->roster_mode) }}</span>
                                @if (!$exception->isRunning())
                                    <span class="text-xs text-muted-foreground">Taken back</span>
                                @endif
                            </p>
                            <p class="mt-1 text-sm text-muted-foreground">{{ $exception->reason }}</p>
                            <p class="mt-1 text-xs text-muted-foreground">{{ $exception->grantedBy?->name ?? 'A campus administrator' }} · {{ school_time($exception->created_at)?->format('M j, Y') }}</p>
                        </div>

                        @if ($canSet && $exception->isRunning())
                            <button type="button" wire:click="revokeException({{ $exception->id }})"
                                wire:confirm="Take back the exception for {{ $exception->subject?->name ?? 'this subject' }}?"
                                class="inline-flex h-11 shrink-0 items-center rounded-md px-3 text-sm font-medium text-muted-foreground select-none hover:bg-muted hover:text-foreground">
                                Take back
                            </button>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
        <x-field-error name="exceptions" />

        @if ($canSet && $exceptionModes !== [] && $subjects->isNotEmpty())
            <form wire:submit="grantException" class="flex flex-col gap-3 pt-2" aria-label="Add an exception">
                <div class="grid gap-3 sm:grid-cols-3">
                    <div>
                        <label for="exception-subject" class="sr-only">Subject</label>
                        <select id="exception-subject" wire:model="exceptionSubjectId" class="{{ $controlClasses }}" {{ field_error_bindings('exceptionSubjectId') }}>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="exception-mode" class="sr-only">How it is taught</label>
                        <select id="exception-mode" wire:model="exceptionRosterMode" class="{{ $controlClasses }}" {{ field_error_bindings('exceptionRosterMode') }}>
                            @foreach ($exceptionModes as $mode)
                                <option value="{{ $mode->value }}">{{ school_roster_label($mode) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="exception-level" class="sr-only">Level</label>
                        <select id="exception-level" wire:model="exceptionLevelId" class="{{ $controlClasses }}" {{ field_error_bindings('exceptionLevelId') }}>
                            <option value="">Every level</option>
                            @foreach ($academicLevels as $level)
                                <option value="{{ $level->id }}">{{ $level->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <x-field-error name="exceptionSubjectId" />
                <x-field-error name="exceptionRosterMode" />
                <x-field-error name="exceptionLevelId" />

                <div class="flex flex-col gap-3 sm:flex-row sm:items-start">
                    <div class="min-w-0 flex-1">
                        <label for="exception-reason" class="sr-only">Why</label>
                        <input id="exception-reason" wire:model="exceptionReason" maxlength="500" autocomplete="off"
                            placeholder="Why this subject is taught differently" class="{{ $controlClasses }}" {{ field_error_bindings('exceptionReason') }}>
                        <x-field-error name="exceptionReason" class="mt-1" />
                    </div>
                    <april:button type="submit" variant="outline" class="h-11 w-full sm:w-auto" wire:loading.attr="disabled">Add exception</april:button>
                </div>
            </form>
        @endif
    </section>
</div>
