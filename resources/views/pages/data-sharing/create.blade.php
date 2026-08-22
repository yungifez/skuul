@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('data-sharing-requests.index'), 'text' => 'Record sharing'],
    ['text' => 'Ask another school', 'active'],
]])

@section('title', 'Ask another school')
@section('page_heading', 'Ask another school')

@section('page_actions')
    <april:button-link href="{{ route('data-sharing-requests.index') }}" variant="outline">
        <x-lucide-arrow-left class="mr-2 size-4" />
        Back to record sharing
    </april:button-link>
@endsection

@section('content')
    <form method="POST" action="{{ route('data-sharing-requests.store') }}" class="space-y-6">
        @csrf

        @if ($errors->has('data_sharing'))
            <april:alert variant="destructive">
                <slot:title>The request was not sent</slot:title>
                <slot:description>{{ $errors->first('data_sharing') }}</slot:description>
            </april:alert>
        @endif

        <april:card>
            <slot:title>Which learner</slot:title>
            <slot:description>
                Name the learner by the admission number the other school gave them. You cannot browse another
                school's learners, so you need the number before you ask.
            </slot:description>
            <slot:content>
                <div class="grid gap-4 lg:grid-cols-2">
                    <div class="flex flex-col gap-2">
                        <april:label for="holding_school_id">The school that holds the records</april:label>
                        <april:native-select id="holding_school_id" name="holding_school_id" required>
                            <option value="">Choose a school</option>
                            @foreach ($schools as $school)
                                <option value="{{ $school->id }}" @selected(old('holding_school_id') == $school->id)>{{ $school->name }}</option>
                            @endforeach
                        </april:native-select>
                        @error('holding_school_id') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <april:label for="admission_number">Their admission number there</april:label>
                        <april:input id="admission_number" name="admission_number" value="{{ old('admission_number') }}" required />
                        @error('admission_number') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>
                </div>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>What you are asking for</slot:title>
            <slot:description>
                Ask for the least you need. The other school reads this list and your reason before it decides.
            </slot:description>
            <slot:content>
                <div class="space-y-6">
                    <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($categories as $category)
                            <label class="flex items-center gap-2 rounded-md border p-3 text-sm">
                                <input type="checkbox" name="categories[]" value="{{ $category->value }}"
                                    @checked(in_array($category->value, old('categories', []), true))
                                    class="size-4 rounded border-input text-primary focus:ring-2 focus:ring-ring">
                                {{ $category->label() }}
                            </label>
                        @endforeach
                    </div>
                    @error('categories') <p class="text-sm text-destructive">{{ $message }}</p> @enderror

                    <div class="grid gap-4 border-t pt-6 lg:grid-cols-3">
                        <div class="flex flex-col gap-2 lg:col-span-2">
                            <april:label for="purpose">Why you need them</april:label>
                            <april:input id="purpose" name="purpose" value="{{ old('purpose') }}" required
                                placeholder="The learner transferred to us in September" />
                            @error('purpose') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col gap-2">
                            <april:label for="expires_on">Permission runs out on</april:label>
                            <input type="date" id="expires_on" name="expires_on" value="{{ old('expires_on') }}"
                                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                            <p class="text-xs text-muted-foreground">Optional. After this day the records cannot be handed over.</p>
                            @error('expires_on') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </slot:content>
        </april:card>

        <div class="flex flex-wrap gap-3">
            <april:button type="submit">
                <x-lucide-send class="mr-2 size-4" />
                Send the request
            </april:button>
            <april:button-link href="{{ route('data-sharing-requests.index') }}" variant="outline">Cancel</april:button-link>
        </div>
    </form>
@endsection
