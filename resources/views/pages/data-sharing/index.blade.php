@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('data-sharing-requests.index'), 'text' => 'Record sharing', 'active'],
]])

@section('title', 'Record sharing')
@section('page_heading', 'Record sharing')

@section('page_actions')
    @can('create', App\Models\DataSharingRequest::class)
        <april:button-link href="{{ route('data-sharing-requests.create') }}">
            <x-lucide-plus class="mr-2 size-4" />
            Ask another school
        </april:button-link>
    @endcan
@endsection

@section('content')
    <div class="space-y-6">
        @if ($waitingCount > 0)
            <april:alert>
                <slot:icon><x-lucide-inbox class="size-4" /></slot:icon>
                <slot:title>{{ $waitingCount }} {{ Str::plural('school', $waitingCount) }} {{ $waitingCount === 1 ? 'is' : 'are' }} waiting for your answer</slot:title>
                <slot:description>
                    Another school has asked for a learner's records. Nothing leaves this school until you agree,
                    and agreeing still does not hand anything over.
                </slot:description>
            </april:alert>
        @endif

        <april:card>
            <slot:title>Asked of this school</slot:title>
            <slot:description>
                Requests from other schools for records this school holds. Only this school decides.
            </slot:description>
            <slot:content>
                @if ($received->isEmpty())
                    <x-empty-state icon="lucide-inbox" title="Nobody has asked this school for records"
                        description="A request arrives here when another school asks about a learner who studied here." />
                @else
                    <april:data-table>
                        <slot:header>
                            <april:data-table-row>
                                <april:data-table-head>Learner</april:data-table-head>
                                <april:data-table-head>Asked by</april:data-table-head>
                                <april:data-table-head>What they want</april:data-table-head>
                                <april:data-table-head>State</april:data-table-head>
                                <april:data-table-head class="text-right">Actions</april:data-table-head>
                            </april:data-table-row>
                        </slot:header>
                        <slot:body>
                            @foreach ($received as $request)
                                <april:data-table-row>
                                    <april:data-table-cell class="font-medium">
                                        {{ $request->studentRecord?->user?->name ?? 'Unnamed' }}
                                        <span class="block text-xs text-muted-foreground">{{ $request->studentRecord?->admission_number }}</span>
                                    </april:data-table-cell>
                                    <april:data-table-cell>{{ $request->requestingSchool?->name ?? 'Unknown school' }}</april:data-table-cell>
                                    <april:data-table-cell class="text-muted-foreground">
                                        {{ count($request->categories) }} {{ Str::plural('kind', count($request->categories)) }} of record
                                    </april:data-table-cell>
                                    <april:data-table-cell>
                                        <span class="inline-flex whitespace-nowrap items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold">
                                            {{ $request->status->label() }}
                                        </span>
                                    </april:data-table-cell>
                                    <april:data-table-cell class="text-right">
                                        <april:button-link href="{{ route('data-sharing-requests.show', $request) }}" variant="outline" size="sm">
                                            <x-lucide-eye class="mr-1 size-4" />
                                            Open
                                        </april:button-link>
                                    </april:data-table-cell>
                                </april:data-table-row>
                            @endforeach
                        </slot:body>
                    </april:data-table>
                @endif
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>Asked by this school</slot:title>
            <slot:description>
                Records this school asked another school for. The other school decides, and you take the records
                in when they arrive.
            </slot:description>
            <slot:content>
                @if ($asked->isEmpty())
                    <x-empty-state icon="lucide-send" title="This school has asked for nothing"
                        description="Ask another school when a learner arrives from one and you need their records.">
                        @can('create', App\Models\DataSharingRequest::class)
                            <april:button-link href="{{ route('data-sharing-requests.create') }}">Ask another school</april:button-link>
                        @endcan
                    </x-empty-state>
                @else
                    <april:data-table>
                        <slot:header>
                            <april:data-table-row>
                                <april:data-table-head>Learner</april:data-table-head>
                                <april:data-table-head>Asked of</april:data-table-head>
                                <april:data-table-head>Runs out</april:data-table-head>
                                <april:data-table-head>State</april:data-table-head>
                                <april:data-table-head class="text-right">Actions</april:data-table-head>
                            </april:data-table-row>
                        </slot:header>
                        <slot:body>
                            @foreach ($asked as $request)
                                <april:data-table-row>
                                    <april:data-table-cell class="font-medium">
                                        {{ $request->studentRecord?->admission_number ?? 'Unknown learner' }}
                                    </april:data-table-cell>
                                    <april:data-table-cell>{{ $request->holdingSchool?->name ?? 'Unknown school' }}</april:data-table-cell>
                                    <april:data-table-cell class="whitespace-nowrap text-muted-foreground">
                                        {{ $request->expires_on?->format('j M Y') ?? 'No end date' }}
                                    </april:data-table-cell>
                                    <april:data-table-cell>
                                        <span class="inline-flex whitespace-nowrap items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold">
                                            {{ $request->status->label() }}
                                        </span>
                                    </april:data-table-cell>
                                    <april:data-table-cell class="text-right">
                                        <april:button-link href="{{ route('data-sharing-requests.show', $request) }}" variant="outline" size="sm">
                                            <x-lucide-eye class="mr-1 size-4" />
                                            Open
                                        </april:button-link>
                                    </april:data-table-cell>
                                </april:data-table-row>
                            @endforeach
                        </slot:body>
                    </april:data-table>
                @endif
            </slot:content>
        </april:card>
    </div>
@endsection
