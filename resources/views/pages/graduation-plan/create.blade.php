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
            <slot:title>The plan</slot:title>
            <slot:description>
                Add what the learner must finish after you save. A school that does not count credits can leave
                them off and use the requirements alone.
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
                        <textarea id="description" name="description" rows="3"
                            class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                            placeholder="Optional">{{ old('description') }}</textarea>
                        @error('description') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>

                    <label class="flex items-center gap-2 text-sm">
                        <input type="hidden" name="uses_credits" value="0">
                        <input type="checkbox" name="uses_credits" value="1" @checked(old('uses_credits'))
                            class="size-4 rounded border-input text-primary-foreground focus:ring-2 focus:ring-ring">
                        This plan counts credits
                    </label>

                    <div class="flex flex-col gap-2">
                        <april:label for="required_credits">Credits needed</april:label>
                        <april:input id="required_credits" name="required_credits" type="number" min="1"
                            value="{{ old('required_credits') }}" placeholder="Only when credits are counted" />
                        @error('required_credits') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>
                </div>
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
