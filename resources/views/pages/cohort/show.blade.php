@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('cohorts.index'), 'text' => 'Groups'],
    ['text' => $cohort->name, 'active'],
]])

@section('title', $cohort->name)
@section('page_heading', $cohort->name)

@section('page_actions')
    <april:button-link href="{{ route('cohorts.index') }}" variant="outline">
        <x-lucide-arrow-left class="mr-2 size-4" />
        Back to groups
    </april:button-link>
@endsection

@php
    $current = $cohort->members->filter(fn ($member) => $member->left_on === null);
    $past = $cohort->members->filter(fn ($member) => $member->left_on !== null);
    $canWrite = auth()->user()->can('update', $cohort);
@endphp

@section('content')
    <div class="space-y-6">
        @if ($cohort->is_restricted)
            <april:alert>
                <slot:icon><x-lucide-lock class="size-4" /></slot:icon>
                <slot:title>This group is private</slot:title>
                <slot:description>Only people who may read a private group see it in the list.</slot:description>
            </april:alert>
        @endif

        @if ($errors->has('member'))
            <april:alert variant="destructive">
                <slot:title>The learner did not join</slot:title>
                <slot:description>{{ $errors->first('member') }}</slot:description>
            </april:alert>
        @endif

        <april:card>
            <slot:title>{{ $cohort->type->label() }}</slot:title>
            <slot:description>{{ $cohort->description ?? 'This group has no description.' }}</slot:description>
            <slot:content>
                <div class="space-y-6">
                    <dl class="grid gap-4 sm:grid-cols-3">
                        <div class="rounded-lg border p-4">
                            <dt class="text-sm text-muted-foreground">In it now</dt>
                            <dd class="text-2xl font-semibold">{{ $current->count() }}</dd>
                        </div>
                        <div class="rounded-lg border p-4">
                            <dt class="text-sm text-muted-foreground">Held a place</dt>
                            <dd class="text-2xl font-semibold">{{ $past->count() }}</dd>
                            <p class="mt-1 text-xs text-muted-foreground">People who have left the group</p>
                        </div>
                        <div class="rounded-lg border p-4">
                            <dt class="text-sm text-muted-foreground">State</dt>
                            <dd class="text-lg font-semibold">{{ $cohort->is_active ? 'In use' : 'Closed' }}</dd>
                        </div>
                    </dl>

                    @if ($canWrite)
                        <form method="POST" action="{{ route('cohorts.update', $cohort) }}"
                            class="grid gap-4 border-t pt-6 lg:grid-cols-4 lg:items-end">
                            @csrf
                            @method('PUT')

                            <div class="flex flex-col gap-2">
                                <april:label for="name">Name</april:label>
                                <april:input id="name" name="name" value="{{ old('name', $cohort->name) }}" required />
                                @error('name') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex flex-col gap-2 lg:col-span-2">
                                <april:label for="description">What it is for</april:label>
                                <april:input id="description" name="description" value="{{ old('description', $cohort->description) }}" />
                            </div>

                            <label class="flex items-center gap-2 text-sm">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $cohort->is_active))
                                    class="size-4 rounded border-input text-primary focus:ring-2 focus:ring-ring">
                                This group is still in use
                            </label>

                            <april:button type="submit" class="lg:col-span-4 lg:justify-self-start">
                                <x-lucide-save class="mr-2 size-4" />
                                Save the group
                            </april:button>
                        </form>
                    @endif
                </div>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>Who is in the group</slot:title>
            <slot:description>Taking somebody out keeps their place, so the group still shows who held it.</slot:description>
            <slot:content>
                <div class="space-y-6">
                    @if ($current->isEmpty())
                        <x-empty-state icon="lucide-user-plus" title="Nobody is in this group"
                            description="Add a learner below." />
                    @else
                        <april:data-table>
                            <slot:header>
                                <april:data-table-row>
                                    <april:data-table-head>Person</april:data-table-head>
                                    <april:data-table-head>Joined</april:data-table-head>
                                    <april:data-table-head class="text-right">Actions</april:data-table-head>
                                </april:data-table-row>
                            </slot:header>
                            <slot:body>
                                @foreach ($current as $member)
                                    <april:data-table-row>
                                        <april:data-table-cell class="font-medium">
                                            {{ $member->studentRecord?->user?->name ?? $member->user?->name ?? 'Unnamed' }}
                                            @if ($member->studentRecord !== null)
                                                <span class="block text-xs text-muted-foreground">{{ $member->studentRecord->admission_number }}</span>
                                            @endif
                                        </april:data-table-cell>
                                        <april:data-table-cell class="whitespace-nowrap text-muted-foreground">
                                            {{ $member->joined_on?->format('j M Y') ?? 'No date' }}
                                        </april:data-table-cell>
                                        <april:data-table-cell class="text-right">
                                            @if ($canWrite)
                                                <form method="POST" action="{{ route('cohorts.members.destroy', [$cohort, $member]) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <april:button type="submit" variant="outline" size="sm">
                                                        <x-lucide-user-minus class="mr-1 size-4" />
                                                        Take out
                                                    </april:button>
                                                </form>
                                            @endif
                                        </april:data-table-cell>
                                    </april:data-table-row>
                                @endforeach
                            </slot:body>
                        </april:data-table>
                    @endif

                    @if ($canWrite)
                        <form method="POST" action="{{ route('cohorts.members.store', $cohort) }}"
                            class="grid gap-4 border-t pt-6 lg:grid-cols-4 lg:items-end">
                            @csrf

                            <div class="flex flex-col gap-2 lg:col-span-2">
                                <april:label for="student_record_id">Learner</april:label>
                                <april:native-select id="student_record_id" name="student_record_id" required>
                                    <option value="">Choose a learner</option>
                                    @foreach ($students as $student)
                                        <option value="{{ $student->id }}">
                                            {{ $student->user?->name ?? 'Unnamed' }} · {{ $student->admission_number }}
                                        </option>
                                    @endforeach
                                </april:native-select>
                                @error('student_record_id') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex flex-col gap-2">
                                <april:label for="joined_on">Joined on</april:label>
                                <input type="date" id="joined_on" name="joined_on" value="{{ old('joined_on', now()->toDateString()) }}"
                                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                            </div>

                            <april:button type="submit">
                                <x-lucide-user-plus class="mr-2 size-4" />
                                Add to the group
                            </april:button>
                        </form>
                    @endif
                </div>
            </slot:content>
        </april:card>

        @if ($past->isNotEmpty())
            <april:card>
                <slot:title>Who has left</slot:title>
                <slot:description>These places are kept so last year's group can still be read.</slot:description>
                <slot:content>
                    <april:data-table>
                        <slot:header>
                            <april:data-table-row>
                                <april:data-table-head>Person</april:data-table-head>
                                <april:data-table-head>Joined</april:data-table-head>
                                <april:data-table-head class="text-right">Left</april:data-table-head>
                            </april:data-table-row>
                        </slot:header>
                        <slot:body>
                            @foreach ($past as $member)
                                <april:data-table-row>
                                    <april:data-table-cell class="font-medium">
                                        {{ $member->studentRecord?->user?->name ?? $member->user?->name ?? 'Unnamed' }}
                                    </april:data-table-cell>
                                    <april:data-table-cell class="whitespace-nowrap text-muted-foreground">
                                        {{ $member->joined_on?->format('j M Y') ?? 'No date' }}
                                    </april:data-table-cell>
                                    <april:data-table-cell class="whitespace-nowrap text-right text-muted-foreground">
                                        {{ $member->left_on->format('j M Y') }}
                                    </april:data-table-cell>
                                </april:data-table-row>
                            @endforeach
                        </slot:body>
                    </april:data-table>
                </slot:content>
            </april:card>
        @endif
    </div>
@endsection
