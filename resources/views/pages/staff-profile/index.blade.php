@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('staff-profiles.index'), 'text' => 'Staff', 'active'],
]])

@section('title', 'Staff')
@section('page_heading', 'Staff')

@section('page_actions')
    @can('create', App\Models\StaffProfile::class)
        <april:button-link href="{{ route('staff-profiles.create') }}">
            <x-lucide-plus class="mr-2 size-4" />
            Add an employment record
        </april:button-link>
    @endcan
@endsection

@section('content')
    <div class="space-y-6">
        <april:card>
            <slot:title>Who works here</slot:title>
            <slot:description>
                An employment record belongs to one school. A person who works in two schools holds two records,
                so leave in one school never hides them in the other.
            </slot:description>
            <slot:content>
                <div class="space-y-6">
                    <dl class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-lg border p-4">
                            <dt class="text-sm text-muted-foreground">Working now</dt>
                            <dd class="text-2xl font-semibold">{{ $employedCount }}</dd>
                        </div>
                        <div class="rounded-lg border p-4">
                            <dt class="text-sm text-muted-foreground">Away today</dt>
                            <dd class="text-2xl font-semibold">{{ $awayCount }}</dd>
                            <p class="mt-1 text-xs text-muted-foreground">Leave the school agreed to</p>
                        </div>
                    </dl>

                    <form method="GET" action="{{ route('staff-profiles.index') }}" class="grid gap-4 border-t pt-6 lg:grid-cols-4 lg:items-end">
                        <div class="flex flex-col gap-2">
                            <april:label for="search">Find a person</april:label>
                            <april:input id="search" name="search" value="{{ $search }}" placeholder="Name, job, or department" />
                        </div>

                        <div class="flex flex-col gap-2">
                            <april:label for="filter-status">State</april:label>
                            <april:native-select id="filter-status" name="status">
                                <option value="">Every state</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->value }}" @selected($selectedStatus === $status)>{{ $status->label() }}</option>
                                @endforeach
                            </april:native-select>
                        </div>

                        <label class="flex items-center gap-2 text-sm">
                            <input type="hidden" name="away" value="0">
                            <input type="checkbox" name="away" value="1" @checked($awayOnly)
                                class="size-4 rounded border-input text-primary focus:ring-2 focus:ring-ring">
                            Only the people away today
                        </label>

                        <div class="flex gap-2">
                            <april:button type="submit">
                                <x-lucide-search class="mr-2 size-4" />
                                Search
                            </april:button>
                            @if ($search !== null || $selectedStatus !== null || $awayOnly)
                                <april:button-link href="{{ route('staff-profiles.index') }}" variant="outline">Clear</april:button-link>
                            @endif
                        </div>
                    </form>
                </div>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>Employment records</slot:title>
            <slot:description>Open a record to read the job, the qualifications, the working hours, and the leave.</slot:description>
            <slot:content>
                @if ($profiles->isEmpty())
                    @if ($search !== null || $selectedStatus !== null || $awayOnly)
                        <x-empty-state icon="lucide-search-x" title="Nothing matches this search"
                            description="No employment record of this school matches.">
                            <april:button-link href="{{ route('staff-profiles.index') }}" variant="outline">Show every record</april:button-link>
                        </x-empty-state>
                    @else
                        <x-empty-state icon="lucide-briefcase" title="No employment records yet"
                            description="Add a record for each person who works here, or import them from a file.">
                            @can('create', App\Models\StaffProfile::class)
                                <april:button-link href="{{ route('staff-profiles.create') }}">Add the first record</april:button-link>
                            @endcan
                        </x-empty-state>
                    @endif
                @else
                    <april:data-table>
                        <slot:header>
                            <april:data-table-row>
                                <april:data-table-head>Person</april:data-table-head>
                                <april:data-table-head>Job</april:data-table-head>
                                <april:data-table-head>Employment</april:data-table-head>
                                <april:data-table-head>State</april:data-table-head>
                                <april:data-table-head>Joined</april:data-table-head>
                                <april:data-table-head class="text-right">Actions</april:data-table-head>
                            </april:data-table-row>
                        </slot:header>
                        <slot:body>
                            @foreach ($profiles as $profile)
                                <april:data-table-row>
                                    <april:data-table-cell class="font-medium">
                                        {{ $profile->user?->name ?? 'Unnamed' }}
                                        <span class="block text-xs text-muted-foreground">
                                            {{ $profile->staff_number ?? 'No staff number' }}
                                            · {{ $profile->credentials_count }} {{ Str::plural('qualification', $profile->credentials_count) }}
                                        </span>
                                    </april:data-table-cell>
                                    <april:data-table-cell>
                                        {{ $profile->job_title ?? '—' }}
                                        <span class="block text-xs text-muted-foreground">{{ $profile->department ?? 'No department' }}</span>
                                    </april:data-table-cell>
                                    <april:data-table-cell class="text-muted-foreground">{{ $profile->employment_type->label() }}</april:data-table-cell>
                                    <april:data-table-cell>
                                        <span class="inline-flex whitespace-nowrap items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold">
                                            {{ $profile->status->label() }}
                                        </span>
                                    </april:data-table-cell>
                                    <april:data-table-cell class="whitespace-nowrap text-muted-foreground">
                                        {{ $profile->joined_on?->format('j M Y') ?? 'No date' }}
                                    </april:data-table-cell>
                                    <april:data-table-cell class="text-right">
                                        <april:button-link href="{{ route('staff-profiles.show', $profile) }}" variant="outline" size="sm">
                                            <x-lucide-eye class="mr-1 size-4" />
                                            Open
                                        </april:button-link>
                                    </april:data-table-cell>
                                </april:data-table-row>
                            @endforeach
                        </slot:body>
                    </april:data-table>

                    <div class="pt-4">
                        {{ $profiles->links('components.pagination-links-view') }}
                    </div>
                @endif
            </slot:content>
        </april:card>
    </div>
@endsection
