@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('data-sharing-requests.index'), 'text' => 'Record sharing'],
    ['text' => 'Request', 'active'],
]])

@section('title', 'Record sharing request')
@section('page_heading', 'Record sharing request')

@section('page_actions')
    <april:button-link href="{{ route('data-sharing-requests.index') }}" variant="outline">
        <x-lucide-arrow-left class="mr-2 size-4" />
        Back to record sharing
    </april:button-link>
@endsection

@php
    $categories = collect($sharingRequest->categories())->map(fn ($category) => $category->label());
@endphp

@section('content')
    <div class="space-y-6">
        @foreach (['status' => 'The request did not move', 'fulfil' => 'The records were not handed over', 'receive' => 'The records were not taken in'] as $key => $title)
            @if ($errors->has($key))
                <april:alert variant="destructive">
                    <slot:title>{{ $title }}</slot:title>
                    <slot:description>{{ $errors->first($key) }}</slot:description>
                </april:alert>
            @endif
        @endforeach

        @if ($sharingRequest->hasExpired())
            <april:alert variant="destructive">
                <slot:title>This permission has run out</slot:title>
                <slot:description>
                    It ended on {{ $sharingRequest->expires_on->format('j M Y') }}. The records cannot be handed over
                    under it any more.
                </slot:description>
            </april:alert>
        @endif

        <april:card>
            <slot:title>{{ $sharingRequest->requestingSchool?->name ?? 'A school' }} asked {{ $sharingRequest->holdingSchool?->name ?? 'another school' }}</slot:title>
            <slot:description>{{ $sharingRequest->purpose }}</slot:description>
            <slot:content>
                <div class="space-y-6">
                    <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="rounded-lg border p-4">
                            <dt class="text-sm text-muted-foreground">State</dt>
                            <dd class="text-lg font-semibold">{{ $sharingRequest->status->label() }}</dd>
                            @if ($sharingRequest->decidedBy !== null)
                                <p class="mt-1 text-xs text-muted-foreground">
                                    {{ $sharingRequest->decidedBy->name }}
                                    · {{ $sharingRequest->decided_at?->format('j M Y') }}
                                </p>
                            @endif
                        </div>
                        <div class="rounded-lg border p-4">
                            <dt class="text-sm text-muted-foreground">Learner</dt>
                            <dd class="text-lg font-semibold">{{ $sharingRequest->studentRecord?->admission_number ?? 'Unknown' }}</dd>
                            <p class="mt-1 text-xs text-muted-foreground">At {{ $sharingRequest->holdingSchool?->name }}</p>
                        </div>
                        <div class="rounded-lg border p-4">
                            <dt class="text-sm text-muted-foreground">Asked on</dt>
                            <dd class="text-lg font-semibold">{{ $sharingRequest->created_at->format('j M Y') }}</dd>
                            <p class="mt-1 text-xs text-muted-foreground">
                                By {{ $sharingRequest->requestedBy?->name ?? 'an unknown person' }}
                            </p>
                        </div>
                        <div class="rounded-lg border p-4">
                            <dt class="text-sm text-muted-foreground">Runs out</dt>
                            <dd class="text-lg font-semibold">{{ $sharingRequest->expires_on?->format('j M Y') ?? 'No end date' }}</dd>
                        </div>
                    </dl>

                    <div class="rounded-lg border p-4">
                        <p class="text-sm text-muted-foreground">What was asked for</p>
                        <ul class="mt-2 flex flex-wrap gap-2">
                            @foreach ($categories as $label)
                                <li class="rounded-full border px-2.5 py-0.5 text-xs font-semibold">{{ $label }}</li>
                            @endforeach
                        </ul>
                    </div>

                    @if (filled($sharingRequest->decision_note))
                        <div class="rounded-lg border p-4">
                            <p class="text-sm text-muted-foreground">What the deciding school said</p>
                            <p class="mt-1 text-sm">{{ $sharingRequest->decision_note }}</p>
                        </div>
                    @endif
                </div>
            </slot:content>
        </april:card>

        @if ($isHolder)
            <april:card>
                <slot:title>Your decision</slot:title>
                <slot:description>
                    Only this school decides, because this school holds the records. Agreeing does not hand
                    anything over: that is the separate step below.
                </slot:description>
                <slot:content>
                    <div class="space-y-6">
                        @can('decide', $sharingRequest)
                            @if ($nextStatuses !== [])
                                <form method="POST" action="{{ route('data-sharing-requests.status.update', $sharingRequest) }}"
                                    class="grid gap-4 lg:grid-cols-4 lg:items-end">
                                    @csrf
                                    @method('PUT')

                                    <div class="flex flex-col gap-2">
                                        <april:label for="status">Answer</april:label>
                                        <april:native-select id="status" name="status" required>
                                            @foreach ($nextStatuses as $status)
                                                <option value="{{ $status->value }}">{{ $status->label() }}</option>
                                            @endforeach
                                        </april:native-select>
                                    </div>

                                    <div class="flex flex-col gap-2 lg:col-span-2">
                                        <april:label for="note">Why</april:label>
                                        <april:input id="note" name="note" placeholder="Optional" />
                                    </div>

                                    <april:button type="submit">
                                        <x-lucide-gavel class="mr-2 size-4" />
                                        Answer the request
                                    </april:button>
                                </form>
                            @else
                                <p class="text-sm text-muted-foreground">This request is finished and cannot move again.</p>
                            @endif
                        @endcan

                        @can('fulfil', $sharingRequest)
                            <div class="flex flex-wrap items-center gap-3 border-t pt-6">
                                @if ($sharingRequest->isUsable())
                                    <form method="POST" action="{{ route('data-sharing-requests.fulfil', $sharingRequest) }}">
                                        @csrf
                                        <april:button type="submit">
                                            <x-lucide-package class="mr-2 size-4" />
                                            Hand the records over
                                        </april:button>
                                    </form>
                                    <span class="text-sm text-muted-foreground">
                                        This builds a copy of exactly what was asked for, and nothing else.
                                    </span>
                                @else
                                    <span class="text-sm text-muted-foreground">
                                        Only an approved request that has not run out can be handed over.
                                    </span>
                                @endif
                            </div>
                        @endcan
                    </div>
                </slot:content>
            </april:card>
        @endif

        <april:card>
            <slot:title>The records themselves</slot:title>
            <slot:description>
                A copy is built once and taken in once. Until the asking school takes it in, it has changed nothing
                there.
            </slot:description>
            <slot:content>
                @if ($package === null)
                    <x-empty-state icon="lucide-package" title="Nothing has been handed over"
                        description="The school that holds the records builds the copy after it agrees." />
                @else
                    <div class="space-y-4">
                        <dl class="grid gap-4 sm:grid-cols-3">
                            <div class="rounded-lg border p-4">
                                <dt class="text-sm text-muted-foreground">Built on</dt>
                                <dd class="text-lg font-semibold">{{ $package->created_at->format('j M Y') }}</dd>
                            </div>
                            <div class="rounded-lg border p-4">
                                <dt class="text-sm text-muted-foreground">Kinds of record</dt>
                                <dd class="text-lg font-semibold">{{ count($package->categories) }}</dd>
                            </div>
                            <div class="rounded-lg border p-4">
                                <dt class="text-sm text-muted-foreground">Taken in</dt>
                                <dd class="text-lg font-semibold">
                                    {{ $package->received_at?->format('j M Y') ?? 'Not yet' }}
                                </dd>
                            </div>
                        </dl>

                        @if ($isRequester && !$package->wasReceived())
                            <form method="POST" action="{{ route('data-sharing-requests.packages.receive', [$sharingRequest, $package]) }}"
                                class="border-t pt-4">
                                @csrf
                                <april:button type="submit">
                                    <x-lucide-download class="mr-2 size-4" />
                                    Take the records in
                                </april:button>
                            </form>
                        @endif
                    </div>
                @endif
            </slot:content>
        </april:card>
    </div>
@endsection
