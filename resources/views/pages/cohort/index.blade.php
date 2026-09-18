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
                <div class="flex min-w-0 flex-col items-start gap-2">
                    <april:label for="filter-type">Kind of group</april:label>
                    <april:native-select id="filter-type" name="type" class="w-full min-w-0">
                        <option value="">Every kind</option>
                        @foreach ($types as $type)
                        <option value="{{ $type->value }}" @selected($selectedType===$type)>{{ $type->label() }}
                        </option>
                        @endforeach
                    </april:native-select>
                </div>

                <label class="flex items-center gap-2 text-sm">
                    <input type="hidden" name="active" value="0">
                    <input type="checkbox" name="active" value="1" @checked($activeOnly)
                        class="size-4 rounded border-input text-primary-foreground focus:ring-2 focus:ring-ring">
                    Only the groups still in use
                </label>

                <div class="flex flex-wrap gap-2">
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
                <april:button-link href="{{ route('cohorts.index') }}" variant="outline">Show every
                    group</april:button-link>
            </x-empty-state>
            @else
            <x-empty-state icon="lucide-users-round" title="No groups yet"
                description="Make a group to follow a set of learners across {{ strtolower(school_terms('class_level', 'classes')) }} and {{ strtolower(school_terms('academic_year', 'school years')) }}.">
                @can('create', App\Models\Cohort::class)
                <april:button-link href="{{ route('cohorts.create') }}">Make the first group</april:button-link>
                @endcan
            </x-empty-state>
            @endif
            @else
            <div class="md:hidden">
                <div class="grid gap-3">
                    @foreach ($cohorts as $cohort)
                    <article class="rounded-lg border bg-background p-4 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h3 class="break-words font-medium leading-6">{{ $cohort->name }}</h3>
                                @if (filled($cohort->description))
                                <p class="mt-1 break-words text-sm text-muted-foreground">{{ $cohort->description }}</p>
                                @endif
                            </div>
                            <span class="shrink-0 text-sm text-muted-foreground">
                                {{ $cohort->is_active ? 'In use' : 'Closed' }}
                            </span>
                        </div>

                        <dl class="mt-4 grid grid-cols-2 gap-x-4 gap-y-3 border-t pt-4 text-sm">
                            <div class="min-w-0">
                                <dt class="text-muted-foreground">Kind</dt>
                                <dd class="mt-1 break-words font-medium">{{ $cohort->type->label() }}</dd>
                            </div>
                            <div class="min-w-0">
                                <dt class="text-muted-foreground">In it now</dt>
                                <dd class="mt-1 font-medium">{{ $cohort->current_members_count }}</dd>
                            </div>
                            @if ($cohort->is_restricted)
                            <div class="col-span-2 flex items-center gap-1 text-muted-foreground">
                                <x-lucide-lock class="size-3" />
                                <dt class="sr-only">Access</dt>
                                <dd>Private group</dd>
                            </div>
                            @endif
                        </dl>

                        <april:button-link href="{{ route('cohorts.show', $cohort) }}" variant="outline"
                            class="mt-4 w-full justify-center" aria-label="Open {{ $cohort->name }}">
                            <x-lucide-eye class="mr-1 size-4" />
                            Open group
                        </april:button-link>
                    </article>
                    @endforeach
                </div>
            </div>

            <div class="hidden md:block">
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
                            <span
                                class="inline-flex whitespace-nowrap items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold">
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
                            <april:button-link href="{{ route('cohorts.show', $cohort) }}" variant="outline" size="sm"
                                aria-label="Open {{ $cohort->name }}">
                                <x-lucide-eye class="mr-1 size-4" />
                                Open
                            </april:button-link>
                        </april:data-table-cell>
                    </april:data-table-row>
                    @endforeach
                </slot:body>
            </april:data-table>
            </div>

            <div class="pt-4">
                {{ $cohorts->links('components.pagination-links-view') }}
            </div>
            @endif
        </slot:content>
    </april:card>
</div>
@endsection
