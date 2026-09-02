@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('graduation-plans.index'), 'text' => 'Graduation plans'],
    ['text' => 'Write a plan', 'active'],
]])

@section('title', 'Write a graduation plan')
@section('page_heading', 'Write a graduation plan')

@section('page_actions')
    <april:button-link href="{{ route('graduation-plans.index') }}" variant="outline">
        <x-lucide-arrow-left class="mr-2 size-4" />
        Back to plans
    </april:button-link>
@endsection

@section('content')
    <form method="POST" action="{{ route('graduation-plans.store') }}" class="space-y-6">
        @csrf

        <april:card>
            <slot:title>Start with the basics</slot:title>
            <slot:description>
                For most nursery, primary, and secondary schools, name the plan and add the classes and subjects
                after you save. Every item is required by default.
            </slot:description>
            <slot:content>
                <div class="grid gap-4 lg:grid-cols-2">
                    <div class="flex flex-col gap-2">
                        <april:label for="name">Name</april:label>
                        <april:input id="name" name="name" value="{{ old('name') }}" required placeholder="Senior school diploma" />
                        @error('name') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <april:label for="cohort_id">Who it is for</april:label>
                        <april:native-select id="cohort_id" name="cohort_id">
                            <option value="">Every learner</option>
                            @foreach ($cohorts as $cohort)
                                <option value="{{ $cohort->id }}" @selected(old('cohort_id') == $cohort->id)>{{ $cohort->name }}</option>
                            @endforeach
                        </april:native-select>
                        @error('cohort_id') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col gap-2 lg:col-span-2">
                        <april:label for="description">What it is</april:label>
                        <april:textarea id="description" name="description" rows="3" placeholder="Optional">{{ old('description') }}</april:textarea>
                        @error('description') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>

                </div>

                <details class="mt-6 rounded-md border p-4" {{ old('completion_operator') && old('completion_operator') !== 'all' ? 'open' : '' }}>
                    <summary class="cursor-pointer text-sm font-semibold">Advanced rules (optional)</summary>
                    <div class="mt-4 space-y-4">
                        <p class="text-sm text-muted-foreground">
                            Use these options for degree plans, elective groups, or a pathway where only some choices
                            are needed.
                        </p>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="flex flex-col gap-2">
                                <label for="completion_operator" class="text-sm font-medium">How should the choices count?</label>
                                <april:native-select id="completion_operator" name="completion_operator">
                                    <option value="all" @selected(old('completion_operator', 'all') === 'all')>All of these</option>
                                    <option value="any" @selected(old('completion_operator') === 'any')>Any one of these</option>
                                    <option value="at_least" @selected(old('completion_operator') === 'at_least')>Choose a number of these</option>
                                </april:native-select>
                                @error('completion_operator') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex flex-col gap-2">
                                <april:label for="required_count">How many are needed?</april:label>
                                <april:input id="required_count" name="required_count" type="number" min="1"
                                    value="{{ old('required_count') }}" placeholder="For example, 4 of 5" />
                                @error('required_count') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                            </div>

                            <label class="flex min-h-10 items-center gap-2 text-sm">
                                <input type="hidden" name="uses_credits" value="0">
                                <april:input type="checkbox" name="uses_credits" value="1" :checked="old('uses_credits')" />
                                Count credits for this plan
                            </label>

                            <div class="flex flex-col gap-2">
                                <april:label for="required_credits">Credits needed</april:label>
                                <april:input id="required_credits" name="required_credits" type="number" min="1"
                                    value="{{ old('required_credits') }}" placeholder="For example, 120" />
                                @error('required_credits') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </details>
            </slot:content>
        </april:card>

        <div class="flex flex-wrap gap-3">
            <april:button type="submit">
                <x-lucide-graduation-cap class="mr-2 size-4" />
                Write the plan
            </april:button>
            <april:button-link href="{{ route('graduation-plans.index') }}" variant="outline">Cancel</april:button-link>
        </div>
    </form>
@endsection
