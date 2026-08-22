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
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-foreground md:text-3xl">{{ $academicYear->name }}</h2>
        <p class="mt-1 text-sm text-muted-foreground">
            One question sets the defaults for every subject in this cycle. It changes no record you already keep,
            and you can answer it until the cycle starts.
        </p>
    </div>

    <x-instructional-model-answer :academic-year="$academicYear" :model="$model" :setting="$setting" :is-future-cycle="$isFutureCycle" />

    @if ($isFutureCycle && $canSet)
        <form action="{{ route('academic-years.instructional-model.update', $academicYear) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
                <div class="flex flex-col gap-1.5 border-b p-6">
                    <h3 class="text-lg font-semibold leading-none tracking-tight">{{ App\Enums\InstructionalModel::SETUP_QUESTION }}</h3>
                    <p class="text-sm text-muted-foreground">
                        Answer for most days of the week. A single subject can still be arranged differently later.
                    </p>
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
                        <label for="instructional-model-reason" class="text-sm font-medium leading-none">Note <span class="font-normal text-muted-foreground">(optional)</span></label>
                        <input id="instructional-model-reason" name="reason" maxlength="500" autocomplete="off"
                            placeholder="Why the campus teaches this way"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                        <p class="text-xs text-muted-foreground">Kept with the change, so anybody can read later why the answer moved.</p>
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
                <h3 class="text-lg font-semibold leading-none tracking-tight">{{ App\Enums\InstructionalModel::SETUP_QUESTION }}</h3>
                <p class="text-sm text-muted-foreground">How {{ $academicYear->name }} is answered, and what it gives every subject in the cycle.</p>
            </div>

            <div class="p-6">
                <x-instructional-model-choice :option="$model" :readonly="true" />
            </div>

            @if (!$isFutureCycle)
                <div class="flex flex-col items-center justify-center gap-4 border-t border-dashed p-8 text-center">
                    <div class="flex size-14 items-center justify-center rounded-full bg-muted text-muted-foreground">
                        <x-lucide-lock class="size-6" />
                    </div>
                    <div>
                        <h4 class="text-base font-semibold text-foreground">This cycle has already started</h4>
                        <p class="mx-auto mt-1 max-w-md text-sm text-muted-foreground">
                            Placements, timetables, and results already sit against {{ $academicYear->name }}. Changing the
                            answer now would rewrite what happened, so it needs the audited migration workflow instead of
                            this form.
                        </p>
                    </div>
                    <a href="{{ route('academic-years.index') }}"
                        class="inline-flex h-10 items-center justify-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground">
                        Answer it for a cycle that has not started
                        <x-lucide-arrow-right class="ml-2 size-4" />
                    </a>
                </div>
            @else
                <div class="flex items-start gap-3 border-t p-6">
                    <div class="flex size-9 shrink-0 items-center justify-center rounded-full bg-muted text-muted-foreground">
                        <x-lucide-info class="size-4" />
                    </div>
                    <div class="text-sm">
                        <p class="font-medium text-foreground">You can read this answer, but not change it</p>
                        <p class="mt-1 text-muted-foreground">Ask a campus administrator to answer the question for {{ $academicYear->name }}.</p>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <p class="text-center text-xs text-muted-foreground">
        Every answer keeps the same records: class groups stay class groups, and a subject keeps one roster.
        The answer only sets what a new subject starts with and what staff are asked for.
    </p>
</div>
@endsection
