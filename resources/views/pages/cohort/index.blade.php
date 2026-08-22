@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('cohorts.index'), 'text' => 'Groups', 'active'],
]])

@section('title', 'Groups')
@section('page_heading', 'Groups')

@section('page_actions')
    @can('create', App\Models\Cohort::class)
        <april:button-link href="{{ route('cohorts.create') }}">
            <x-lucide-plus class="mr-2 size-4" />
            Make a group
        </april:button-link>
    @endcan
@endsection

@section('content')
    <div class="space-y-6">
        <april:card>
            <slot:title>A group that is not a class</slot:title>
            <slot:description>
                A graduation year, a scholarship group, a club, or a watchlist. A place in a group is kept when
                somebody leaves, so the school can still see who was in it last year.
            </slot:description>
            <slot:content>
                <form method="GET" action="{{ route('cohorts.index') }}" class="grid gap-4 lg:grid-cols-4 lg:items-end">
                    <div class="flex flex-col gap-2">
                        <april:label for="filter-type">Kind of group</april:label>
                        <april:native-select id="filter-type" name="type">
                            <option value="">Every kind</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->value }}" @selected($selectedType === $type)>{{ $type->label() }}</option>
                            @endforeach
                        </april:native-select>
                    </div>

                    <label class="flex items-center gap-2 text-sm">
                        <input type="hidden" name="active" value="0">
                        <input type="checkbox" name="active" value="1" @checked($activeOnly)
                            class="size-4 rounded border-input text-primary focus:ring-2 focus:ring-ring">
                        Only the groups still in use
                    </label>

                    <div class="flex gap-2">
                        <april:button type="submit">
                            <x-lucide-filter class="mr-2 size-4" />
                            Apply
                        </april:button>
                        @if ($selectedType !== null || $activeOnly)
                            <april:button-link href="{{ route('cohorts.index') }}" variant="outline">Clear</april:button-link>
                        @endif
                    </div>
                </form>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>Groups</slot:title>
            <slot:description>Open a group to read who is in it and to add somebody.</slot:description>
            <slot:content>
                @if ($cohorts->isEmpty())
                    @if ($selectedType !== null || $activeOnly)
                        <x-empty-state icon="lucide-search-x" title="Nothing matches this filter"
                            description="No group you may read is of that kind.">
                            <april:button-link href="{{ route('cohorts.index') }}" variant="outline">Show every group</april:button-link>
                        </x-empty-state>
                    @else
                        <x-empty-state icon="lucide-users-round" title="No groups yet"
                            description="Make a group to follow a set of learners across classes and years.">
                            @can('create', App\Models\Cohort::class)
                                <april:button-link href="{{ route('cohorts.create') }}">Make the first group</april:button-link>
                            @endcan
                        </x-empty-state>
                    @endif
                @else
                    <april:data-table>
                        <slot:header>
                            <april:data-table-row>
                                <april:data-table-head>Group</april:data-table-head>
                                <april:data-table-head>Kind</april:data-table-head>
                                <april:data-table-head>In it now</april:data-table-head>
                                <april:data-table-head>State</april:data-table-head>
                                <april:data-table-head class="text-right">Actions</april:data-table-head>
                            </april:data-table-row>
                        </slot:header>
                        <slot:body>
                            @foreach ($cohorts as $cohort)
                                <april:data-table-row>
                                    <april:data-table-cell class="font-medium">
                                        {{ $cohort->name }}
                                        @if (filled($cohort->description))
                                            <span class="block text-xs text-muted-foreground">{{ $cohort->description }}</span>
                                        @endif
                                    </april:data-table-cell>
                                    <april:data-table-cell>
                                        <span class="inline-flex whitespace-nowrap items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold">
                                            {{ $cohort->type->label() }}
                                        </span>
                                        @if ($cohort->is_restricted)
                                            <span class="mt-1 flex items-center gap-1 text-xs text-muted-foreground">
                                                <x-lucide-lock class="size-3" />
                                                Private
                                            </span>
                                        @endif
                                    </april:data-table-cell>
                                    <april:data-table-cell>{{ $cohort->current_members_count }}</april:data-table-cell>
                                    <april:data-table-cell class="text-muted-foreground">
                                        {{ $cohort->is_active ? 'In use' : 'Closed' }}
                                    </april:data-table-cell>
                                    <april:data-table-cell class="text-right">
                                        <april:button-link href="{{ route('cohorts.show', $cohort) }}" variant="outline" size="sm">
                                            <x-lucide-eye class="mr-1 size-4" />
                                            Open
                                        </april:button-link>
                                    </april:data-table-cell>
                                </april:data-table-row>
                            @endforeach
                        </slot:body>
                    </april:data-table>

                    <div class="pt-4">
                        {{ $cohorts->links('components.pagination-links-view') }}
                    </div>
                @endif
            </slot:content>
        </april:card>
    </div>
@endsection
