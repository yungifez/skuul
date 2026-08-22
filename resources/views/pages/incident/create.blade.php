@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('incidents.index'), 'text' => 'Cases'],
    ['text' => 'Record a case', 'active'],
]])

@section('title', 'Record a case')
@section('page_heading', 'Record a case')

@section('page_actions')
    <april:button-link href="{{ route('incidents.index') }}" variant="outline">
        <x-lucide-arrow-left class="mr-2 size-4" />
        Back to cases
    </april:button-link>
@endsection

@section('content')
    <form method="POST" action="{{ route('incidents.store') }}" class="space-y-6">
        @csrf

        @if ($errors->has('incident'))
            <april:alert variant="destructive">
                <slot:title>The case was not recorded</slot:title>
                <slot:description>{{ $errors->first('incident') }}</slot:description>
            </april:alert>
        @endif

        <april:card>
            <slot:title>What happened</slot:title>
            <slot:description>Write what you saw. A case is a record of one event, so keep one event to one case.</slot:description>
            <slot:content>
                <div class="grid gap-4 lg:grid-cols-2">
                    <div class="flex flex-col gap-2 lg:col-span-2">
                        <april:label for="summary">Summary</april:label>
                        <april:input id="summary" name="summary" value="{{ old('summary') }}" required
                            placeholder="One line that names the event" />
                        @error('summary') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <april:label for="category">Kind of case</april:label>
                        <april:native-select id="category" name="category" required>
                            @foreach ($categories as $category)
                                <option value="{{ $category->value }}" @selected(old('category') === $category->value)>
                                    {{ $category->label() }}
                                </option>
                            @endforeach
                        </april:native-select>
                        <p class="text-xs text-muted-foreground">
                            A safeguarding case is restricted the moment you save it. Only the people who handle it can read it.
                        </p>
                        @error('category') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <april:label for="occurred_at">When it happened</april:label>
                        <input type="datetime-local" id="occurred_at" name="occurred_at" required
                            value="{{ old('occurred_at', now()->format('Y-m-d\TH:i')) }}"
                            max="{{ now()->format('Y-m-d\TH:i') }}"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                        @error('occurred_at') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <april:label for="location">Where it happened</april:label>
                        <april:input id="location" name="location" value="{{ old('location') }}" placeholder="Optional" />
                        @error('location') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <april:label for="assigned_to">Handled by</april:label>
                        <april:native-select id="assigned_to" name="assigned_to">
                            <option value="">Nobody yet</option>
                            @foreach ($staff as $person)
                                <option value="{{ $person->id }}" @selected(old('assigned_to') == $person->id)>
                                    {{ $person->name }}
                                </option>
                            @endforeach
                        </april:native-select>
                        @error('assigned_to') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col gap-2 lg:col-span-2">
                        <april:label for="description">What happened, in full</april:label>
                        <textarea id="description" name="description" rows="5"
                            class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                            placeholder="Optional. Write what you saw, not what you think it means.">{{ old('description') }}</textarea>
                        @error('description') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>
                </div>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>Who was involved</slot:title>
            <slot:description>Name each learner and say why they appear. Leave a row empty to skip it.</slot:description>
            <slot:content>
                <div x-data="{ rows: {{ max(count(old('participants', [])), 2) }} }" class="space-y-4">
                    @for ($index = 0; $index < 20; $index++)
                        <div x-show="rows > {{ $index }}" @if ($index >= max(count(old('participants', [])), 2)) style="display: none" @endif
                            class="grid gap-3 rounded-lg border p-4 lg:grid-cols-3">
                            <div class="flex flex-col gap-2">
                                <april:label for="participant-{{ $index }}-student">Learner</april:label>
                                <april:native-select id="participant-{{ $index }}-student" name="participants[{{ $index }}][student_record_id]">
                                    <option value="">Choose a learner</option>
                                    @foreach ($students as $student)
                                        <option value="{{ $student->id }}" @selected(old("participants.$index.student_record_id") == $student->id)>
                                            {{ $student->user?->name ?? 'Unnamed' }} · {{ $student->admission_number }}
                                        </option>
                                    @endforeach
                                </april:native-select>
                            </div>

                            <div class="flex flex-col gap-2">
                                <april:label for="participant-{{ $index }}-role">Why they appear</april:label>
                                <april:native-select id="participant-{{ $index }}-role" name="participants[{{ $index }}][role]">
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->value }}" @selected(old("participants.$index.role") === $role->value)>
                                            {{ $role->label() }}
                                        </option>
                                    @endforeach
                                </april:native-select>
                            </div>

                            <div class="flex flex-col gap-2">
                                <april:label for="participant-{{ $index }}-note">Note</april:label>
                                <april:input id="participant-{{ $index }}-note" name="participants[{{ $index }}][note]"
                                    value="{{ old("participants.$index.note") }}" placeholder="Optional" />
                            </div>
                        </div>
                    @endfor

                    <april:button type="button" variant="outline" size="sm" x-on:click="rows = Math.min(rows + 1, 20)"
                        x-show="rows < 20">
                        <x-lucide-plus class="mr-1 size-4" />
                        Add another person
                    </april:button>
                </div>
            </slot:content>
        </april:card>

        <div class="flex flex-wrap gap-3">
            <april:button type="submit">
                <x-lucide-shield-alert class="mr-2 size-4" />
                Record the case
            </april:button>
            <april:button-link href="{{ route('incidents.index') }}" variant="outline">Cancel</april:button-link>
        </div>
    </form>
@endsection
