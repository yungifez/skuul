@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('incidents.index'), 'text' => 'Cases', 'active'],
]])

@section('title', 'Cases')
@section('page_heading', 'Cases')

@section('page_actions')
    @can('create', App\Models\Incident::class)
        <april:button-link href="{{ route('incidents.create') }}">
            <x-lucide-plus class="mr-2 size-4" />
            Record a case
        </april:button-link>
    @endcan
@endsection

@section('content')
    <div class="space-y-6">
        <april:card>
            <slot:title>Behaviour records and safeguarding concerns</slot:title>
            <slot:description>
                {{ $openCount }} {{ Str::plural('case', $openCount) }} still {{ $openCount === 1 ? 'needs' : 'need' }} work.
                A safeguarding case is readable only by the people who handle it, so this list may be shorter than the school's.
            </slot:description>
            <slot:content>
                <form method="GET" action="{{ route('incidents.index') }}" class="grid gap-4 lg:grid-cols-4 lg:items-end">
                    <div class="flex flex-col gap-2">
                        <april:label for="filter-status">State</april:label>
                        <april:native-select id="filter-status" name="status">
                            <option value="">Every state</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}" @selected($selectedStatus === $status)>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </april:native-select>
                    </div>

                    <div class="flex flex-col gap-2">
                        <april:label for="filter-category">Kind</april:label>
                        <april:native-select id="filter-category" name="category">
                            <option value="">Every kind</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->value }}" @selected($selectedCategory === $category)>
                                    {{ $category->label() }}
                                </option>
                            @endforeach
                        </april:native-select>
                    </div>

                    <label class="flex items-center gap-2 text-sm">
                        <input type="hidden" name="open" value="0">
                        <input type="checkbox" name="open" value="1" @checked($openOnly)
                            class="size-4 rounded border-input text-primary-foreground focus:ring-2 focus:ring-ring">
                        Only the cases that need work
                    </label>

                    <div class="flex gap-2">
                        <april:button type="submit">
                            <x-lucide-filter class="mr-2 size-4" />
                            Apply
                        </april:button>
                        @if ($selectedStatus !== null || $selectedCategory !== null || $openOnly)
                            <april:button-link href="{{ route('incidents.index') }}" variant="outline">Clear</april:button-link>
                        @endif
                    </div>
                </form>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>Recorded cases</slot:title>
            <slot:description>The newest case is first. Open a case to read it and to move it on.</slot:description>
            <slot:content>
                @if ($incidents->isEmpty())
                    @if ($selectedStatus !== null || $selectedCategory !== null || $openOnly)
                        <x-empty-state icon="lucide-search-x" title="Nothing matches this filter"
                            description="No case you may read is in that state.">
                            <april:button-link href="{{ route('incidents.index') }}" variant="outline">Show every case</april:button-link>
                        </x-empty-state>
                    @else
                        <x-empty-state icon="lucide-shield-alert" title="No cases yet"
                            description="A case records what happened, who was involved, and what the school did about it.">
                            @can('create', App\Models\Incident::class)
                                <april:button-link href="{{ route('incidents.create') }}">Record the first case</april:button-link>
                            @endcan
                        </x-empty-state>
                    @endif
                @else
                    <april:data-table>
                        <slot:header>
                            <april:data-table-row>
                                <april:data-table-head>Case</april:data-table-head>
                                <april:data-table-head>Kind</april:data-table-head>
                                <april:data-table-head>State</april:data-table-head>
                                <april:data-table-head>Happened</april:data-table-head>
                                <april:data-table-head>Handled by</april:data-table-head>
                                <april:data-table-head class="text-right">Actions</april:data-table-head>
                            </april:data-table-row>
                        </slot:header>
                        <slot:body>
                            @foreach ($incidents as $incident)
                                <april:data-table-row>
                                    <april:data-table-cell class="font-medium">
                                        {{ $incident->summary }}
                                        <span class="block text-xs text-muted-foreground">
                                            {{ $incident->reference }}
                                            · {{ $incident->participants_count }} {{ Str::plural('person', $incident->participants_count) }} named
                                        </span>
                                    </april:data-table-cell>
                                    <april:data-table-cell>
                                        <span class="inline-flex whitespace-nowrap items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold">
                                            {{ $incident->category->label() }}
                                        </span>
                                        @if ($incident->is_restricted)
                                            <span class="mt-1 flex items-center gap-1 text-xs text-muted-foreground">
                                                <x-lucide-lock class="size-3" />
                                                Restricted
                                            </span>
                                        @endif
                                    </april:data-table-cell>
                                    <april:data-table-cell>
                                        <span class="inline-flex whitespace-nowrap items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold">
                                            {{ $incident->status->label() }}
                                        </span>
                                    </april:data-table-cell>
                                    <april:data-table-cell class="whitespace-nowrap text-muted-foreground">
                                        {{ $incident->occurred_at->format('j M Y') }}
                                        <span class="block text-xs">{{ $incident->occurred_at->format('H:i') }}</span>
                                    </april:data-table-cell>
                                    <april:data-table-cell class="text-muted-foreground">
                                        {{ $incident->assignedTo?->name ?? 'Nobody yet' }}
                                    </april:data-table-cell>
                                    <april:data-table-cell class="text-right">
                                        <april:button-link href="{{ route('incidents.show', $incident) }}" variant="outline" size="sm">
                                            <x-lucide-eye class="mr-1 size-4" />
                                            Open
                                        </april:button-link>
                                    </april:data-table-cell>
                                </april:data-table-row>
                            @endforeach
                        </slot:body>
                    </april:data-table>

                    <div class="pt-4">
                        {{ $incidents->links('components.pagination-links-view') }}
                    </div>
                @endif
            </slot:content>
        </april:card>
    </div>
@endsection
