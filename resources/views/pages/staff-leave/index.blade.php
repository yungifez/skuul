@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('staff-leave.index'), 'text' => 'Staff leave', 'active'],
]])

@section('title', 'Staff leave')
@section('page_heading', 'Staff leave')

@section('content')
    <div class="space-y-6">
        @if ($errors->has('leave'))
            <april:alert variant="destructive">
                <slot:title>The leave was not asked for</slot:title>
                <slot:description>{{ $errors->first('leave') }}</slot:description>
            </april:alert>
        @endif

        @if ($errors->has('status'))
            <april:alert variant="destructive">
                <slot:title>The leave did not move</slot:title>
                <slot:description>{{ $errors->first('status') }}</slot:description>
            </april:alert>
        @endif

        <april:card>
            <slot:title>Who is away today</slot:title>
            <slot:description>
                {{ $waitingCount }} {{ Str::plural('request', $waitingCount) }}
                {{ $waitingCount === 1 ? 'is' : 'are' }} still waiting for an answer.
            </slot:description>
            <slot:content>
                @if ($awayToday->isEmpty())
                    <p class="text-sm text-muted-foreground">Everybody is in today.</p>
                @else
                    <ul class="flex flex-wrap gap-2">
                        @foreach ($awayToday as $profile)
                            <li class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-sm">
                                <x-lucide-plane class="size-3 text-muted-foreground" />
                                {{ $profile->user?->name ?? 'Unnamed' }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            </slot:content>
        </april:card>

        @can('create', App\Models\StaffLeaveRequest::class)
            <april:card>
                <slot:title>Ask for days away</slot:title>
                <slot:description>
                    The days are held the moment they are asked for, so nobody can ask for the same days twice.
                </slot:description>
                <slot:content>
                    <form method="POST" action="{{ route('staff-leave.store') }}" class="grid gap-4 lg:grid-cols-5 lg:items-end">
                        @csrf

                        <div class="flex flex-col gap-2">
                            <april:label for="staff_profile_id">Person</april:label>
                            <april:native-select id="staff_profile_id" name="staff_profile_id" required>
                                <option value="">Choose a person</option>
                                @foreach ($profiles as $profile)
                                    <option value="{{ $profile->id }}" @selected(old('staff_profile_id') == $profile->id)>
                                        {{ $profile->user?->name ?? 'Unnamed' }}
                                    </option>
                                @endforeach
                            </april:native-select>
                            @error('staff_profile_id') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col gap-2">
                            <april:label for="type">Kind of leave</april:label>
                            <april:native-select id="type" name="type" required>
                                @foreach ($types as $type)
                                    <option value="{{ $type->value }}" @selected(old('type') === $type->value)>{{ $type->label() }}</option>
                                @endforeach
                            </april:native-select>
                        </div>

                        <div class="flex flex-col gap-2">
                            <april:label for="starts_on">From</april:label>
                            <input type="date" id="starts_on" name="starts_on" value="{{ old('starts_on', now()->toDateString()) }}" required
                                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                            @error('starts_on') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col gap-2">
                            <april:label for="ends_on">To</april:label>
                            <input type="date" id="ends_on" name="ends_on" value="{{ old('ends_on', now()->toDateString()) }}" required
                                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                            @error('ends_on') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col gap-2 lg:col-span-4">
                            <april:label for="reason">Why</april:label>
                            <april:input id="reason" name="reason" value="{{ old('reason') }}" placeholder="Optional" />
                        </div>

                        <april:button type="submit">
                            <x-lucide-plane class="mr-2 size-4" />
                            Ask for these days
                        </april:button>
                    </form>
                </slot:content>
            </april:card>
        @endcan

        <april:card>
            <slot:title>Find a request</slot:title>
            <slot:description>Narrow the list to one state or one kind of leave.</slot:description>
            <slot:content>
                <form method="GET" action="{{ route('staff-leave.index') }}" class="grid gap-4 lg:grid-cols-4 lg:items-end">
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
                        <april:label for="filter-type">Kind of leave</april:label>
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
                            <april:button-link href="{{ route('staff-leave.index') }}" variant="outline">Clear</april:button-link>
                        @endif
                    </div>
                </form>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>Requests</slot:title>
            <slot:description>A person never answers their own request.</slot:description>
            <slot:content>
                @if ($leaveRequests->isEmpty())
                    @if ($selectedStatus !== null || $selectedType !== null)
                        <x-empty-state icon="lucide-search-x" title="Nothing matches this filter"
                            description="No request is in that state.">
                            <april:button-link href="{{ route('staff-leave.index') }}" variant="outline">Show every request</april:button-link>
                        </x-empty-state>
                    @else
                        <x-empty-state icon="lucide-plane" title="No leave asked for yet"
                            description="Ask for days above. The days are held as soon as they are asked for." />
                    @endif
                @else
                    <april:data-table>
                        <slot:header>
                            <april:data-table-row>
                                <april:data-table-head>Person</april:data-table-head>
                                <april:data-table-head>Days</april:data-table-head>
                                <april:data-table-head>Kind</april:data-table-head>
                                <april:data-table-head>State</april:data-table-head>
                                <april:data-table-head class="text-right">Answer</april:data-table-head>
                            </april:data-table-row>
                        </slot:header>
                        <slot:body>
                            @foreach ($leaveRequests as $leave)
                                <april:data-table-row>
                                    <april:data-table-cell class="font-medium">
                                        {{ $leave->staffProfile->user?->name ?? 'Unnamed' }}
                                        @if (filled($leave->reason))
                                            <span class="block text-xs text-muted-foreground">{{ $leave->reason }}</span>
                                        @endif
                                    </april:data-table-cell>
                                    <april:data-table-cell class="whitespace-nowrap">
                                        {{ $leave->starts_on->format('j M Y') }} to {{ $leave->ends_on->format('j M Y') }}
                                        <span class="block text-xs text-muted-foreground">{{ $leave->days() }} {{ Str::plural('day', $leave->days()) }}</span>
                                    </april:data-table-cell>
                                    <april:data-table-cell class="text-muted-foreground">{{ $leave->type->label() }}</april:data-table-cell>
                                    <april:data-table-cell>
                                        <span class="inline-flex whitespace-nowrap items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold">
                                            {{ $leave->status->label() }}
                                        </span>
                                        @if ($leave->decided_at !== null)
                                            <span class="mt-1 block text-xs text-muted-foreground">
                                                {{ $leave->decidedBy?->name ?? 'Unknown person' }}
                                                · {{ $leave->decided_at->format('j M Y') }}
                                            </span>
                                        @endif
                                    </april:data-table-cell>
                                    <april:data-table-cell class="text-right">
                                        @can('decide', $leave)
                                            @if ($leave->status === App\Enums\LeaveStatus::Requested)
                                                <div class="flex justify-end gap-2">
                                                    <form method="POST" action="{{ route('staff-leave.status.update', $leave) }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="{{ App\Enums\LeaveStatus::Approved->value }}">
                                                        <april:button type="submit" size="sm">
                                                            <x-lucide-check class="mr-1 size-4" />
                                                            Agree
                                                        </april:button>
                                                    </form>
                                                    <form method="POST" action="{{ route('staff-leave.status.update', $leave) }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="{{ App\Enums\LeaveStatus::Declined->value }}">
                                                        <april:button type="submit" variant="outline" size="sm">
                                                            <x-lucide-x class="mr-1 size-4" />
                                                            Say no
                                                        </april:button>
                                                    </form>
                                                </div>
                                            @else
                                                <span class="text-sm text-muted-foreground">Answered</span>
                                            @endif
                                        @else
                                            <span class="text-sm text-muted-foreground">
                                                {{ $leave->status === App\Enums\LeaveStatus::Requested ? 'Waiting for somebody else' : 'Answered' }}
                                            </span>
                                        @endcan
                                    </april:data-table-cell>
                                </april:data-table-row>
                            @endforeach
                        </slot:body>
                    </april:data-table>

                    <div class="pt-4">
                        {{ $leaveRequests->links('components.pagination-links-view') }}
                    </div>
                @endif
            </slot:content>
        </april:card>
    </div>
@endsection
