@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('academic-years.index'), 'text' => 'Academic years'],
    ['href' => route('academic-years.edit', $academicYear->id), 'text' => $academicYear->name],
    ['href' => route('academic-years.instructional-model.edit', $academicYear->id), 'text' => 'Teaching setup', 'active'],
]])

@section('title', __("Teaching setup for {$academicYear->name}"))

@section('page_heading', __('Teaching setup'))

@section('page_actions')
    <x-academic-period-status-control :period="$academicYear" route-prefix="academic-years" />
@endsection

@section('content')
<div class="mx-auto flex w-full max-w-4xl flex-col gap-6">
    <div class="flex items-center gap-2">
        <h2 class="text-2xl font-bold tracking-tight text-foreground md:text-3xl">{{ $academicYear->name }}</h2>
        <x-help-tooltip label="Teaching setup help">One answer sets the defaults for every subject in this cycle. It changes no existing records, and it can be answered until the cycle starts.</x-help-tooltip>
    </div>

    <x-instructional-model-answer :academic-year="$academicYear" :model="$model" :setting="$setting" :is-future-cycle="$isFutureCycle" />

    @if ($isFutureCycle && $canSet)
        <form action="{{ route('academic-years.instructional-model.update', $academicYear) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
                <div class="flex flex-col gap-1.5 border-b p-6">
                    <div class="flex items-center gap-1">
                        <h3 class="text-lg font-semibold leading-none tracking-tight">{{ App\Enums\InstructionalModel::SETUP_QUESTION }}</h3>
                        <x-help-tooltip label="Teaching setup question help">Answer for most days of the week. A single subject can still be arranged differently later.</x-help-tooltip>
                    </div>
                </div>

                <div class="flex flex-col gap-4 p-6">
                    <x-display-validation-errors />

                    <fieldset class="flex flex-col gap-3">
                        <legend class="sr-only">{{ App\Enums\InstructionalModel::SETUP_QUESTION }}</legend>

                        @foreach (App\Enums\InstructionalModel::cases() as $option)
                            <x-instructional-model-choice :option="$option" :selected="$option === $model" />
                        @endforeach
                    </fieldset>

                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-1">
                            <label for="instructional-model-reason" class="text-sm font-medium leading-none">Note <span class="font-normal text-muted-foreground">(optional)</span></label>
                            <x-help-tooltip label="Teaching setup note help">This note is saved with the answer so future administrators can understand why the setup was chosen.</x-help-tooltip>
                        </div>
                        <input id="instructional-model-reason" name="reason" maxlength="500" autocomplete="off"
                            placeholder="Why the campus teaches this way"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t p-6 sm:flex-row sm:items-center sm:justify-between">
                    <a href="{{ route('academic-years.edit', $academicYear->id) }}"
                        class="inline-flex h-10 items-center justify-center rounded-md px-4 py-2 text-sm font-medium text-muted-foreground transition-colors hover:bg-accent hover:text-accent-foreground">
                        Back to the cycle
                    </a>
                    <april:button type="submit" class="w-full sm:w-auto">
                        <x-lucide-check class="mr-2 size-4" />
                        Save teaching setup
                    </april:button>
                </div>
            </div>
        </form>
    @else
        <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col gap-1.5 border-b p-6">
                <div class="flex items-center gap-1">
                    <h3 class="text-lg font-semibold leading-none tracking-tight">{{ App\Enums\InstructionalModel::SETUP_QUESTION }}</h3>
                    <x-help-tooltip label="Current setup question help">This is the answer currently used for {{ $academicYear->name }} and the starting point for every subject in the cycle.</x-help-tooltip>
                </div>
            </div>

            <div class="p-6">
                <x-instructional-model-choice :option="$model" :readonly="true" />
            </div>

            @if (!$isFutureCycle && !$canMigrate)
                <div class="flex flex-col items-center justify-center gap-4 border-t border-dashed p-8 text-center">
                    <div class="flex size-14 items-center justify-center rounded-full bg-muted text-muted-foreground">
                        <x-lucide-lock class="size-6" />
                    </div>
                    <div>
                        <h4 class="text-base font-semibold text-foreground">This cycle has already started</h4>
                        <div class="flex items-center justify-center gap-1">
                            <p class="text-sm text-muted-foreground">This form is closed because the cycle has started.</p>
                            <x-help-tooltip label="Started cycle help">Placements, timetables, and results already sit against {{ $academicYear->name }}. A campus administrator with permission to move the cycle can use the mid-year move below.</x-help-tooltip>
                        </div>
                    </div>
                    <a href="{{ route('academic-years.index') }}"
                        class="inline-flex h-10 items-center justify-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground">
                        Answer it for a cycle that has not started
                        <x-lucide-arrow-right class="ml-2 size-4" />
                    </a>
                </div>
            @elseif (!$isFutureCycle)
                <div class="flex items-center gap-1 border-t p-6 text-sm text-muted-foreground">
                    This cycle is running; use the recorded mid-year move below if the answer must change.
                    <x-help-tooltip label="Mid-year change help">The original answer is kept as history. A mid-year move changes what staff are asked for from that point forward and does not rewrite existing records.</x-help-tooltip>
                </div>
            @else
                <div class="flex items-start gap-3 border-t p-6">
                    <div class="flex size-9 shrink-0 items-center justify-center rounded-full bg-muted text-muted-foreground">
                        <x-lucide-info class="size-4" />
                    </div>
                    <div class="text-sm">
                        <p class="font-medium text-foreground">You can read this answer, but not change it</p>
                        <p class="mt-1 text-muted-foreground">Ask a campus administrator to answer this question.</p>
                        <x-help-tooltip label="Permission to answer help">Only a campus administrator can set the teaching setup for a future cycle.</x-help-tooltip>
                    </div>
                </div>
            @endif
        </div>
    @endif

    @if ($canMigrate)
        <form action="{{ route('academic-years.instructional-model.migrate', $academicYear) }}" method="POST">
            @csrf

            <div class="rounded-xl border border-destructive/40 bg-card text-card-foreground shadow-sm">
                <div class="flex flex-col gap-1.5 border-b border-destructive/30 p-6">
                    <div class="flex items-center gap-1">
                        <span class="flex size-7 items-center justify-center rounded-full bg-destructive/10 text-destructive">
                            <x-lucide-triangle-alert class="size-4" />
                        </span>
                        <h3 class="text-lg font-semibold leading-none tracking-tight">Move this cycle mid-year</h3>
                        <x-help-tooltip label="Mid-year move help">{{ $academicYear->name }} is running. Moving it changes what staff are asked for from today. Existing records are not rewritten, and the move is saved with its reason.</x-help-tooltip>
                    </div>
                </div>

                <div class="flex flex-col gap-4 p-6">
                    <fieldset class="flex flex-col gap-3">
                        <legend class="flex items-center gap-1 text-xs font-bold text-muted-foreground uppercase">
                            Move to
                            <x-help-tooltip label="Move destination help">Choose the teaching setup the cycle should use from now on. Each option shows how many existing subjects may be affected.</x-help-tooltip>
                        </legend>

                        @foreach (App\Enums\InstructionalModel::cases() as $option)
                            @continue($option === $model)
                            <x-instructional-model-choice :option="$option" :impact="$impacts[$option->value] ?? null" id-prefix="migrate-model" />
                        @endforeach
                    </fieldset>

                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-1">
                            <label for="migrate-reason" class="text-sm font-medium leading-none">Why the cycle is moving</label>
                            <x-help-tooltip label="Move reason help">This reason is kept in the cycle history with your name and the date. Write it so another administrator can understand the decision later.</x-help-tooltip>
                        </div>
                        <textarea id="migrate-reason" name="reason" rows="3" maxlength="500" required
                            placeholder="The campus agreed on 3 March to teach Year 5 music as one combined group."
                            class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">{{ old('reason') }}</textarea>
                    </div>

                    <label for="migrate-confirm" class="flex cursor-pointer items-start gap-3 rounded-lg border border-destructive/30 bg-destructive/5 p-4 text-sm">
                        <input type="checkbox" id="migrate-confirm" name="confirm" value="1" class="mt-0.5 size-4 shrink-0 accent-destructive">
                        <span class="text-muted-foreground">
                            I understand that this changes new work from now on; existing arrangements stay as they are.
                            <x-help-tooltip label="Mid-year move confirmation help">Subject setup, screens, and report wording use the new answer from now on. Subjects already arranged the old way stay as they are.</x-help-tooltip>
                        </span>
                    </label>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-destructive/30 p-6 sm:flex-row sm:items-center sm:justify-between">
                    <span class="flex items-center gap-1 text-xs text-muted-foreground">
                        Recorded with your name, the date, and the reason.
                        <x-help-tooltip label="Move audit history help">A move cannot be edited later. If the decision changes again, record another move.</x-help-tooltip>
                    </span>
                    <april:button type="submit" variant="destructive" class="w-full sm:w-auto">
                        <x-lucide-arrow-right-left class="mr-2 size-4" />
                        Move this cycle
                    </april:button>
                </div>
            </div>
        </form>
    @endif

    @if ($migrations->isNotEmpty())
        <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col gap-1.5 border-b p-6">
                <div class="flex items-center gap-1">
                    <h3 class="text-lg font-semibold leading-none tracking-tight">Moves recorded for this cycle</h3>
                    <x-help-tooltip label="Recorded moves help">Moves are kept as written. A correction is another move, never an edit.</x-help-tooltip>
                </div>
            </div>

            <ol class="divide-y">
                @foreach ($migrations as $migration)
                    <li class="flex flex-col gap-2 p-6 sm:flex-row sm:items-start sm:justify-between sm:gap-6">
                        <div class="min-w-0">
                            <p class="flex flex-wrap items-center gap-2 text-sm">
                                <span class="text-muted-foreground">{{ $migration->from_model?->label() ?? 'No answer' }}</span>
                                <x-lucide-arrow-right class="size-3.5 text-muted-foreground" />
                                <span class="font-semibold text-foreground">{{ $migration->to_model->label() }}</span>
                            </p>
                            <p class="mt-2 text-sm text-muted-foreground">{{ $migration->reason }}</p>
                            @if (($migration->impact['exceptions'] ?? 0) > 0)
                                <p class="mt-2 text-xs text-muted-foreground">
                                    {{ $migration->impact['exceptions'] }} of {{ $migration->impact['offerings'] }} subjects
                                    kept a roster the new answer does not offer.
                                </p>
                            @endif
                        </div>
                        <p class="shrink-0 text-xs text-muted-foreground sm:text-right">
                            {{ $migration->migratedBy?->name ?? 'A campus administrator' }}<br>
                            {{ $migration->created_at?->format('M j, Y') }}
                        </p>
                    </li>
                @endforeach
            </ol>
        </div>
    @endif

    <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col gap-1.5 border-b p-6">
                <div class="flex items-center gap-1">
                    <h3 class="text-lg font-semibold leading-none tracking-tight">Subjects taught differently</h3>
                    <x-help-tooltip label="Teaching exceptions help">A campus answer sets the default, but one subject can be taught differently for this cycle. An exception does not change the campus answer.</x-help-tooltip>
                </div>
        </div>

        @if ($exceptions->isEmpty())
            <div class="flex flex-col items-center gap-3 p-10 text-center">
                <span class="flex size-12 items-center justify-center rounded-full bg-muted text-muted-foreground">
                    <x-lucide-shuffle class="size-6" />
                </span>
                <p class="text-sm font-medium">Every subject follows the campus answer.</p>
            </div>
        @else
            <ul class="divide-y">
                @foreach ($exceptions as $exception)
                    <li class="flex flex-col gap-2 p-6 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0">
                            <p class="flex flex-wrap items-center gap-2 text-sm">
                                <span class="font-semibold text-foreground">{{ $exception->subject?->name }}</span>
                                <span class="text-muted-foreground">{{ $exception->coverage() }}</span>
                                <april:badge variant="outline">{{ $exception->roster_mode->label() }}</april:badge>
                                @if (!$exception->isRunning())
                                    <april:badge variant="secondary">Taken back</april:badge>
                                @endif
                            </p>
                            <p class="mt-2 text-sm text-muted-foreground">{{ $exception->reason }}</p>
                            <p class="mt-1 text-xs text-muted-foreground">
                                {{ $exception->grantedBy?->name ?? 'A campus administrator' }} &middot;
                                {{ $exception->created_at?->format('M j, Y') }}
                            </p>
                        </div>

                        @if ($canExcept && $exception->isRunning())
                            <form action="{{ route('academic-years.instructional-model.exceptions.destroy', [$academicYear, $exception]) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <april:button type="submit" variant="ghost" size="sm">Take it back</april:button>
                            </form>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif

        @if ($canExcept && $exceptionModes !== [])
            <form action="{{ route('academic-years.instructional-model.exceptions.store', $academicYear) }}" method="POST" class="border-t">
                @csrf

                <div class="grid gap-4 p-6 sm:grid-cols-2">
                    <div class="flex flex-col gap-2">
                        <label for="exception-subject" class="text-sm font-medium leading-none">Subject</label>
                        <select id="exception-subject" name="subject_id" required
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="exception-mode" class="text-sm font-medium leading-none">How it is taught</label>
                        <select id="exception-mode" name="roster_mode" required
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                            @foreach ($exceptionModes as $mode)
                                <option value="{{ $mode->value }}">{{ $mode->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="exception-level" class="text-sm font-medium leading-none">Level <span class="font-normal text-muted-foreground">(optional)</span></label>
                        <select id="exception-level" name="academic_level_id"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                            <option value="">Every level</option>
                            @foreach ($academicLevels as $level)
                                <option value="{{ $level->id }}">{{ $level->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="exception-reason" class="text-sm font-medium leading-none">Why</label>
                        <input id="exception-reason" name="reason" required maxlength="500" value="{{ old('reason') }}"
                            placeholder="One combined music class for the whole level"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                    </div>
                </div>

                <div class="flex justify-end border-t p-6">
                    <april:button type="submit" variant="outline">
                        <x-lucide-shuffle class="mr-2 size-4" />
                        Record the exception
                    </april:button>
                </div>
            </form>
        @endif
    </div>

</div>
@endsection
