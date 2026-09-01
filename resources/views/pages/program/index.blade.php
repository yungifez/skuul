@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('programs.index'), 'text' => 'Programmes', 'active'],
]])

@section('title', 'Programmes')
@section('page_heading', 'Programmes')

@section('page_actions')
    @can('create', App\Models\Program::class)
        <april:button-link href="{{ route('programs.create') }}">
            <x-lucide-plus class="mr-2 size-4" />
            Open a programme
        </april:button-link>
    @endcan
@endsection

@section('content')
    <div class="space-y-6">
        <april:card>
            <slot:title>Activities a learner takes part in</slot:title>
            <slot:description>
                A club, an intervention, or a support service. Taking part never touches enrollment: a learner who
                leaves a club is still a learner.
            </slot:description>
            <slot:content>
                <form method="GET" action="{{ route('programs.index') }}" class="grid gap-4 lg:grid-cols-4 lg:items-end">
                    <div class="flex flex-col gap-2">
                        <april:label for="filter-type">Kind</april:label>
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
                            class="size-4 rounded border-input text-primary-foreground focus:ring-2 focus:ring-ring">
                        Only the programmes still open
                    </label>

                    <div class="flex gap-2">
                        <april:button type="submit">
                            <x-lucide-filter class="mr-2 size-4" />
                            Apply
                        </april:button>
                        @if ($selectedType !== null || $activeOnly)
                            <april:button-link href="{{ route('programs.index') }}" variant="outline">Clear</april:button-link>
                        @endif
                    </div>
                </form>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>Programmes</slot:title>
            <slot:description>Open a programme to read who takes part and to give somebody a place.</slot:description>
            <slot:content>
                @if ($programs->isEmpty())
                    @if ($selectedType !== null || $activeOnly)
                        <x-empty-state icon="lucide-search-x" title="Nothing matches this filter"
                            description="No programme of that kind is open.">
                            <april:button-link href="{{ route('programs.index') }}" variant="outline">Show every programme</april:button-link>
                        </x-empty-state>
                    @else
                        <x-empty-state icon="lucide-sparkles" title="No programmes yet"
                            description="Open one to record who takes part in a club, an intervention, or a support service.">
                            @can('create', App\Models\Program::class)
                                <april:button-link href="{{ route('programs.create') }}">Open the first programme</april:button-link>
                            @endcan
                        </x-empty-state>
                    @endif
                @else
                    <april:data-table>
                        <slot:header>
                            <april:data-table-row>
                                <april:data-table-head>Programme</april:data-table-head>
                                <april:data-table-head>Kind</april:data-table-head>
                                <april:data-table-head>Taking part</april:data-table-head>
                                <april:data-table-head>State</april:data-table-head>
                                <april:data-table-head class="text-right">Actions</april:data-table-head>
                            </april:data-table-row>
                        </slot:header>
                        <slot:body>
                            @foreach ($programs as $program)
                                <april:data-table-row>
                                    <april:data-table-cell class="font-medium">
                                        {{ $program->name }}
                                        @if (filled($program->description))
                                            <span class="block text-xs text-muted-foreground">{{ $program->description }}</span>
                                        @endif
                                    </april:data-table-cell>
                                    <april:data-table-cell>
                                        <span class="inline-flex whitespace-nowrap items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold">
                                            {{ $program->type->label() }}
                                        </span>
                                    </april:data-table-cell>
                                    <april:data-table-cell>{{ $program->running_count }}</april:data-table-cell>
                                    <april:data-table-cell class="text-muted-foreground">
                                        {{ $program->is_active ? 'Open' : 'Closed' }}
                                    </april:data-table-cell>
                                    <april:data-table-cell class="text-right">
                                        <april:button-link href="{{ route('programs.show', $program) }}" variant="outline" size="sm">
                                            <x-lucide-eye class="mr-1 size-4" />
                                            Open
                                        </april:button-link>
                                    </april:data-table-cell>
                                </april:data-table-row>
                            @endforeach
                        </slot:body>
                    </april:data-table>

                    <div class="pt-4">
                        {{ $programs->links('components.pagination-links-view') }}
                    </div>
                @endif
            </slot:content>
        </april:card>
    </div>
@endsection
