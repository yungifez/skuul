@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('staff-profiles.index'), 'text' => 'Staff'],
    ['text' => 'Add an employment record', 'active'],
]])

@section('title', 'Add an employment record')
@section('page_heading', 'Add an employment record')

@section('page_actions')
    <april:button-link href="{{ route('staff-profiles.index') }}" variant="outline">
        <x-lucide-arrow-left class="mr-2 size-4" />
        Back to staff
    </april:button-link>
@endsection

@section('content')
    <form method="POST" action="{{ route('staff-profiles.store') }}" class="space-y-6">
        @csrf

        <april:card>
            <slot:title>The job</slot:title>
            <slot:description>
                The person needs an account in this school first. One person holds one employment record per school.
            </slot:description>
            <slot:content>
                <div class="grid gap-4 lg:grid-cols-2">
                    <div class="flex flex-col gap-2">
                        <april:label for="user_id">Person</april:label>
                        <april:native-select id="user_id" name="user_id" required>
                            <option value="">Choose a person</option>
                            @foreach ($people as $person)
                                <option value="{{ $person->id }}" @selected(old('user_id') == $person->id)>{{ $person->name }}</option>
                            @endforeach
                        </april:native-select>
                        @error('user_id') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <april:label for="staff_number">Staff number</april:label>
                        <april:input id="staff_number" name="staff_number" value="{{ old('staff_number') }}" placeholder="Optional" />
                        @error('staff_number') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <april:label for="job_title">Job title</april:label>
                        <april:input id="job_title" name="job_title" value="{{ old('job_title') }}" placeholder="Teacher" />
                        @error('job_title') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <april:label for="department">Department</april:label>
                        <april:input id="department" name="department" value="{{ old('department') }}" placeholder="Science" />
                        @error('department') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <april:label for="employment_type">Employment</april:label>
                        <april:native-select id="employment_type" name="employment_type" required>
                            @foreach ($employmentTypes as $type)
                                <option value="{{ $type->value }}" @selected(old('employment_type') === $type->value)>{{ $type->label() }}</option>
                            @endforeach
                        </april:native-select>
                        @error('employment_type') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <april:label for="joined_on">Joined on</april:label>
                        <input type="date" id="joined_on" name="joined_on" value="{{ old('joined_on', now()->toDateString()) }}"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                        @error('joined_on') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>
                </div>
            </slot:content>
        </april:card>

        <div class="flex flex-wrap gap-3">
            <april:button type="submit">
                <x-lucide-briefcase class="mr-2 size-4" />
                Save the record
            </april:button>
            <april:button-link href="{{ route('staff-profiles.index') }}" variant="outline">Cancel</april:button-link>
        </div>
    </form>
@endsection
