@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('portal-requests.index'), 'text' => 'Family requests', 'active'],
]])

@section('title', 'Family requests')
@section('page_heading', 'Family requests')

@section('content')
    <div class="space-y-6">
        @if ($errors->has('status'))
            <april:alert variant="destructive">
                <slot:title>The request did not move</slot:title>
                <slot:description>{{ $errors->first('status') }}</slot:description>
            </april:alert>
        @endif

        <april:card>
            <slot:title>What families have asked for</slot:title>
            <slot:description>
                {{ $waitingCount }} {{ Str::plural('request', $waitingCount) }}
                {{ $waitingCount === 1 ? 'is' : 'are' }} still waiting for an answer. A request changes no record by
                itself; somebody has to do the work and then say so here.
            </slot:description>
            <slot:content>
                <form method="GET" action="{{ route('portal-requests.index') }}" class="grid gap-4 lg:grid-cols-4 lg:items-end">
                    <div class="flex flex-col gap-2">
                        <april:label for="filter-status">State</april:label>
                        <april:native-select id="filter-status" name="status">
                            <option value="">Every state</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}" @selected($selectedStatus === $status)>{{ $status->label() }}</option>
                            @endforeach
                        </april:native-select>
                    </div>

                    <div class="flex flex-col gap-2">
                        <april:label for="filter-type">Kind</april:label>
                        <april:native-select id="filter-type" name="type">
                            <option value="">Every kind</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->value }}" @selected($selectedType === $type)>{{ $type->label() }}</option>
                            @endforeach
                        </april:native-select>
                    </div>

                    <div class="flex gap-2">
                        <april:button type="submit">
                            <x-lucide-filter class="mr-2 size-4" />
                            Apply
                        </april:button>
                        @if ($selectedStatus !== null || $selectedType !== null)
                            <april:button-link href="{{ route('portal-requests.index') }}" variant="outline">Clear</april:button-link>
                        @endif
                    </div>
                </form>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>Requests</slot:title>
            <slot:description>A family never answers its own request, whatever else they hold.</slot:description>
            <slot:content>
                @if ($requests->isEmpty())
                    @if ($selectedStatus !== null || $selectedType !== null)
                        <x-empty-state icon="lucide-search-x" title="Nothing matches this filter"
                            description="No request is in that state.">
                            <april:button-link href="{{ route('portal-requests.index') }}" variant="outline">Show every request</april:button-link>
                        </x-empty-state>
                    @else
                        <x-empty-state icon="lucide-message-square" title="No requests yet"
                            description="Families send requests from the portal. They arrive here." />
                    @endif
                @else
                    <div class="space-y-4">
                        @foreach ($requests as $request)
                            <div class="rounded-lg border p-4">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="font-medium">{{ $request->subject }}</p>
                                        <p class="text-sm text-muted-foreground">
                                            About {{ $request->studentRecord->user?->name ?? $request->studentRecord->admission_number }}
                                            · from {{ $request->requestedBy?->name ?? 'an unknown person' }}
                                            · {{ $request->created_at->format('j M Y') }}
                                        </p>
                                        @if (filled($request->message))
                                            <p class="mt-2 whitespace-pre-line text-sm">{{ $request->message }}</p>
                                        @endif
                                    </div>
                                    <div class="flex flex-col items-end gap-1">
                                        <span class="whitespace-nowrap rounded-full border px-2.5 py-0.5 text-xs font-semibold">
                                            {{ $request->status->label() }}
                                        </span>
                                        <span class="text-xs text-muted-foreground">{{ $request->type->label() }}</span>
                                    </div>
                                </div>

                                @if (filled($request->response))
                                    <div class="mt-3 rounded-md bg-muted/40 p-3">
                                        <p class="text-xs text-muted-foreground">
                                            Answered by {{ $request->answeredBy?->name ?? 'an unknown person' }}
                                            on {{ $request->answered_at?->format('j M Y') }}
                                        </p>
                                        <p class="mt-1 whitespace-pre-line text-sm">{{ $request->response }}</p>
                                    </div>
                                @endif

                                @can('answer', $request)
                                    @if ($request->status->isOpen())
                                        <form method="POST" action="{{ route('portal-requests.status.update', $request) }}"
                                            class="mt-4 grid gap-4 border-t pt-4 lg:grid-cols-4 lg:items-end">
                                            @csrf
                                            @method('PUT')

                                            <div class="flex flex-col gap-2">
                                                <april:label for="status-{{ $request->id }}">Move it to</april:label>
                                                <april:native-select id="status-{{ $request->id }}" name="status" required>
                                                    @foreach ($request->status->allowedNext() as $status)
                                                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                                                    @endforeach
                                                </april:native-select>
                                            </div>

                                            <div class="flex flex-col gap-2 lg:col-span-2">
                                                <april:label for="response-{{ $request->id }}">The answer</april:label>
                                                <april:input id="response-{{ $request->id }}" name="response"
                                                    placeholder="Needed when you answer" />
                                            </div>

                                            <april:button type="submit">
                                                <x-lucide-reply class="mr-2 size-4" />
                                                Send it
                                            </april:button>
                                        </form>
                                    @endif
                                @endcan
                            </div>
                        @endforeach
                    </div>

                    <div class="pt-4">
                        {{ $requests->links('components.pagination-links-view') }}
                    </div>
                @endif
            </slot:content>
        </april:card>
    </div>
@endsection
