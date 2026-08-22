@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('support-plans.index'), 'text' => 'Support plans'],
    ['text' => 'Open a plan', 'active'],
]])

@section('title', 'Open a support plan')
@section('page_heading', 'Open a support plan')

@section('page_actions')
    <april:button-link href="{{ route('support-plans.index') }}" variant="outline">
        <x-lucide-arrow-left class="mr-2 size-4" />
        Back to plans
    </april:button-link>
@endsection

@section('content')
    <form method="POST" action="{{ route('support-plans.store') }}" class="space-y-6">
        @csrf

        @if ($errors->has('support_plan'))
            <april:alert variant="destructive">
                <slot:title>The plan was not opened</slot:title>
                <slot:description>{{ $errors->first('support_plan') }}</slot:description>
            </april:alert>
        @endif

        <april:card>
            <slot:title>Who the plan is for</slot:title>
            <slot:description>A plan belongs to one child and one enrollment. The child must be enrolled now.</slot:description>
            <slot:content>
                <div class="grid gap-4 lg:grid-cols-2">
                    <div class="flex flex-col gap-2">
                        <april:label for="student_record_id">Learner</april:label>
                        <april:native-select id="student_record_id" name="student_record_id" required>
                            <option value="">Choose a learner</option>
                            @foreach ($students as $student)
                                <option value="{{ $student->id }}" @selected(old('student_record_id') == $student->id)>
                                    {{ $student->user?->name ?? 'Unnamed' }} · {{ $student->admission_number }}
                                </option>
                            @endforeach
                        </april:native-select>
                        @error('student_record_id') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <april:label for="category">Kind of help</april:label>
                        <april:native-select id="category" name="category" required>
                            @foreach ($categories as $category)
                                <option value="{{ $category->value }}" @selected(old('category') === $category->value)>
                                    {{ $category->label() }}
                                </option>
                            @endforeach
                        </april:native-select>
                        <p class="text-xs text-muted-foreground">
                            A health or counselling plan is confidential the moment you save it. Only the people who run it can read it.
                        </p>
                        @error('category') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>
                </div>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>What the plan says</slot:title>
            <slot:description>Name the plan so another member of staff knows what it is for without opening it.</slot:description>
            <slot:content>
                <div class="grid gap-4 lg:grid-cols-2">
                    <div class="flex flex-col gap-2 lg:col-span-2">
                        <april:label for="title">Title</april:label>
                        <april:input id="title" name="title" value="{{ old('title') }}" required
                            placeholder="Extra reading, four mornings a week" />
                        @error('title') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col gap-2 lg:col-span-2">
                        <april:label for="summary">Summary</april:label>
                        <textarea id="summary" name="summary" rows="4"
                            class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                            placeholder="Optional. Say what the child needs and what the school agreed to do.">{{ old('summary') }}</textarea>
                        @error('summary') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <april:label for="starts_on">Starts on</april:label>
                        <input type="date" id="starts_on" name="starts_on" value="{{ old('starts_on', now()->toDateString()) }}"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                        @error('starts_on') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <april:label for="review_on">Look at it again on</april:label>
                        <input type="date" id="review_on" name="review_on" value="{{ old('review_on') }}"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                        <p class="text-xs text-muted-foreground">The list warns you when this day passes.</p>
                        @error('review_on') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <april:label for="assigned_to">Run by</april:label>
                        <april:native-select id="assigned_to" name="assigned_to">
                            <option value="">Nobody yet</option>
                            @foreach ($staff as $person)
                                <option value="{{ $person->id }}" @selected(old('assigned_to') == $person->id)>{{ $person->name }}</option>
                            @endforeach
                        </april:native-select>
                        @error('assigned_to') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>
                </div>
            </slot:content>
        </april:card>

        <div class="flex flex-wrap gap-3">
            <april:button type="submit">
                <x-lucide-heart-handshake class="mr-2 size-4" />
                Open the plan
            </april:button>
            <april:button-link href="{{ route('support-plans.index') }}" variant="outline">Cancel</april:button-link>
        </div>
    </form>
@endsection
