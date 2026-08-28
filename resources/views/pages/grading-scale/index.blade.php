@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('schools.settings'), 'text' => 'School setup'],
    ['href' => route('grading-scales.index'), 'text' => 'Grading scales', 'active'],
]])

@section('title', __('Grading scales'))
@section('page_heading', __('Grading scales'))

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        <april:card>
            <slot:title class="flex items-center gap-1">
                <span>Create a grading scale</span>
                <x-help-tooltip label="Grading scale help">Use familiar grade names such as A–F, Excellent–Needs support, or local terms. Give every option points only when the scale contributes to a numeric result.</x-help-tooltip>
            </slot:title>
            <slot:description>Add the choices teachers use when grading.</slot:description>
            <slot:content>
                <form method="POST" action="{{ route('grading-scales.store') }}" class="space-y-5">
                    @csrf
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2"><april:label for="scale-name">Scale name</april:label><input id="scale-name" name="name" value="{{ old('name') }}" required class="flex h-10 w-full rounded-md border bg-background px-3 py-2 text-sm" placeholder="Primary school grades"></div>
                        <div class="flex items-end pb-1"><label class="flex items-center gap-2 text-sm"><input name="is_active" type="checkbox" value="1" {{ old('is_active', true) ? 'checked' : '' }}> Available for new assessments</label></div>
                    </div>
                    <div class="space-y-2"><april:label for="scale-description">Description</april:label><textarea id="scale-description" name="description" rows="2" class="w-full rounded-md border bg-background px-3 py-2 text-sm" placeholder="When staff should use this scale">{{ old('description') }}</textarea></div>
                    @php
                        $createOptions = old('options', array_fill(0, 5, ['label' => '', 'points' => '']));
                    @endphp
                    <div class="space-y-3">
                        <div class="flex items-center gap-1"><p class="text-sm font-medium">Grade options</p><x-help-tooltip label="Grade options help">The points value is the exact numeric score recorded when a teacher chooses that grade. It is not a lower or upper boundary. Use points on every option, or leave them blank on every option for descriptive grades.</x-help-tooltip></div>
                        <div x-data="{ extraOptions: 0 }" class="space-y-3">
                            @foreach ($createOptions as $index => $option)
                                <div class="grid gap-3 sm:grid-cols-[1fr_12rem_auto]">
                                    <input name="options[{{ $index }}][label]" value="{{ $option['label'] ?? '' }}" class="h-10 rounded-md border bg-background px-3 py-2 text-sm" placeholder="Excellent">
                                    <input name="options[{{ $index }}][points]" value="{{ $option['points'] ?? '' }}" type="number" min="0" step="0.01" class="h-10 rounded-md border bg-background px-3 py-2 text-sm" placeholder="Recorded points">
                                </div>
                            @endforeach
                            <template x-for="index in extraOptions" :key="index">
                                <div class="grid gap-3 sm:grid-cols-[1fr_12rem_auto]">
                                    <input x-bind:name="'options[' + ({{ count($createOptions) }} + index - 1) + '][label]'" class="h-10 rounded-md border bg-background px-3 py-2 text-sm" placeholder="New grade option">
                                    <input x-bind:name="'options[' + ({{ count($createOptions) }} + index - 1) + '][points]'" type="number" min="0" step="0.01" class="h-10 rounded-md border bg-background px-3 py-2 text-sm" placeholder="Recorded points">
                                </div>
                            </template>
                            <button type="button" x-on:click="extraOptions++" class="inline-flex items-center rounded-md border px-3 py-2 text-sm font-medium hover:bg-muted">
                                Add another option
                            </button>
                        </div>
                    </div>
                    <div class="flex justify-end"><april:button type="submit">Create grading scale</april:button></div>
                </form>
            </slot:content>
        </april:card>

        @if ($errors->has('grading_scale') || $errors->has('options'))
            <div class="rounded-lg border border-destructive/40 bg-destructive/10 px-4 py-3 text-sm text-destructive">
                @foreach (array_merge($errors->get('grading_scale'), $errors->get('options')) as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <section class="space-y-3">
            <div><h2 class="text-xl font-semibold tracking-tight">Your school’s scales</h2><p class="text-sm text-muted-foreground">Deactivate a scale to stop new assessments using it. Existing learner records remain intact.</p></div>
            @forelse ($gradingScales as $gradingScale)
                <details class="rounded-xl border bg-card p-5">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-4">
                        <span><span class="block font-medium">{{ $gradingScale->name }}</span><span class="block text-sm text-muted-foreground">{{ $gradingScale->options->pluck('label')->join(' · ') }}</span></span>
                        <span class="flex items-center gap-2"><april:badge variant="{{ $gradingScale->is_active ? 'secondary' : 'outline' }}">{{ $gradingScale->is_active ? 'Active' : 'Inactive' }}</april:badge><span class="text-sm text-muted-foreground">{{ $gradingScale->grade_items_count }} assessment{{ $gradingScale->grade_items_count === 1 ? '' : 's' }}</span></span>
                    </summary>
                    <form method="POST" action="{{ route('grading-scales.update', $gradingScale) }}" class="mt-5 space-y-5 border-t pt-5">
                        @csrf
                        @method('PUT')
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2"><april:label for="scale-name-{{ $gradingScale->id }}">Scale name</april:label><input id="scale-name-{{ $gradingScale->id }}" name="name" value="{{ $gradingScale->name }}" required class="flex h-10 w-full rounded-md border bg-background px-3 py-2 text-sm"></div>
                            <div class="flex items-end pb-1"><label class="flex items-center gap-2 text-sm"><input name="is_active" type="checkbox" value="1" {{ $gradingScale->is_active ? 'checked' : '' }}> Available for new assessments</label></div>
                        </div>
                        <div class="space-y-2"><april:label for="scale-description-{{ $gradingScale->id }}">Description</april:label><textarea id="scale-description-{{ $gradingScale->id }}" name="description" rows="2" class="w-full rounded-md border bg-background px-3 py-2 text-sm">{{ $gradingScale->description }}</textarea></div>
                        <div x-data="{ extraOptions: 0 }" class="space-y-3">
                            <div><p class="text-sm font-medium">Grade options</p><p class="text-xs text-muted-foreground">Recorded points are exact scores, not lower or upper boundaries. Options used in learner records cannot be changed or removed.</p></div>
                            <div class="space-y-3">
                                @foreach ($gradingScale->options as $index => $option)
                                    <div class="grid gap-3 sm:grid-cols-[1fr_12rem_auto]">
                                        <input type="hidden" name="options[{{ $index }}][id]" value="{{ $option->id }}">
                                        <input name="options[{{ $index }}][label]" value="{{ $option->label }}" required class="h-10 rounded-md border bg-background px-3 py-2 text-sm">
                                        <input name="options[{{ $index }}][points]" value="{{ $option->points }}" type="number" min="0" step="0.01" class="h-10 rounded-md border bg-background px-3 py-2 text-sm" placeholder="Recorded points">
                                    </div>
                                @endforeach
                                @for ($index = $gradingScale->options->count(); $index < $gradingScale->options->count() + 5; $index++)
                                    <div class="grid gap-3 sm:grid-cols-[1fr_12rem]">
                                        <input name="options[{{ $index }}][label]" class="h-10 rounded-md border bg-background px-3 py-2 text-sm" placeholder="New grade option">
                                        <input name="options[{{ $index }}][points]" type="number" min="0" step="0.01" class="h-10 rounded-md border bg-background px-3 py-2 text-sm" placeholder="Recorded points">
                                    </div>
                                @endfor
                                <template x-for="index in extraOptions" :key="index">
                                    <div class="grid gap-3 sm:grid-cols-[1fr_12rem]">
                                        <input x-bind:name="'options[' + ({{ $gradingScale->options->count() + 5 }} + index - 1) + '][label]'" class="h-10 rounded-md border bg-background px-3 py-2 text-sm" placeholder="New grade option">
                                        <input x-bind:name="'options[' + ({{ $gradingScale->options->count() + 5 }} + index - 1) + '][points]'" type="number" min="0" step="0.01" class="h-10 rounded-md border bg-background px-3 py-2 text-sm" placeholder="Recorded points">
                                    </div>
                                </template>
                                <button type="button" x-on:click="extraOptions++" class="inline-flex items-center rounded-md border px-3 py-2 text-sm font-medium hover:bg-muted">
                                    Add another option
                                </button>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center justify-between gap-3"><april:button type="submit">Save changes</april:button></div>
                    </form>
                    @if ($gradingScale->grade_items_count === 0)
                        <form method="POST" action="{{ route('grading-scales.destroy', $gradingScale) }}" class="mt-3">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-destructive hover:underline">Delete scale</button>
                        </form>
                    @endif
                </details>
            @empty
                <div class="rounded-xl border border-dashed p-8 text-center text-sm text-muted-foreground">Create the first scale your teachers use when marking work.</div>
            @endforelse
        </section>
    </div>
@endsection
