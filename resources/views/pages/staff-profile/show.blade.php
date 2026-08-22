@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('staff-profiles.index'), 'text' => 'Staff'],
    ['text' => $profile->user?->name ?? 'Employment record', 'active'],
]])

@section('title', 'Employment record')
@section('page_heading', $profile->user?->name ?? 'Employment record')

@section('page_actions')
    <april:button-link href="{{ route('staff-profiles.index') }}" variant="outline">
        <x-lucide-arrow-left class="mr-2 size-4" />
        Back to staff
    </april:button-link>
@endsection

@php
    $days = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday'];
    $canWrite = auth()->user()->can('update', $profile);
@endphp

@section('content')
    <div class="space-y-6">
        @if ($isAway)
            <april:alert>
                <slot:icon><x-lucide-plane class="size-4" /></slot:icon>
                <slot:title>This person is away today</slot:title>
                <slot:description>The timetable will not give them cover work while the leave holds these days.</slot:description>
            </april:alert>
        @endif

        <april:card>
            <slot:title>The job</slot:title>
            <slot:description>{{ $profile->user?->email ?? 'No email address' }}</slot:description>
            <slot:content>
                <div class="space-y-6">
                    <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="rounded-lg border p-4">
                            <dt class="text-sm text-muted-foreground">State</dt>
                            <dd class="text-lg font-semibold">{{ $profile->status->label() }}</dd>
                        </div>
                        <div class="rounded-lg border p-4">
                            <dt class="text-sm text-muted-foreground">Job</dt>
                            <dd class="text-lg font-semibold">{{ $profile->job_title ?? '—' }}</dd>
                            <p class="mt-1 text-xs text-muted-foreground">{{ $profile->department ?? 'No department' }}</p>
                        </div>
                        <div class="rounded-lg border p-4">
                            <dt class="text-sm text-muted-foreground">Employment</dt>
                            <dd class="text-lg font-semibold">{{ $profile->employment_type->label() }}</dd>
                            <p class="mt-1 text-xs text-muted-foreground">{{ $profile->staff_number ?? 'No staff number' }}</p>
                        </div>
                        <div class="rounded-lg border p-4">
                            <dt class="text-sm text-muted-foreground">Joined</dt>
                            <dd class="text-lg font-semibold">{{ $profile->joined_on?->format('j M Y') ?? 'No date' }}</dd>
                            <p class="mt-1 text-xs text-muted-foreground">
                                {{ $profile->left_on === null ? 'Still here' : 'Left '.$profile->left_on->format('j M Y') }}
                            </p>
                        </div>
                    </dl>

                    @if ($canWrite)
                        <form method="POST" action="{{ route('staff-profiles.update', $profile) }}"
                            class="grid gap-4 border-t pt-6 lg:grid-cols-3">
                            @csrf
                            @method('PUT')

                            <div class="flex flex-col gap-2">
                                <april:label for="job_title">Job title</april:label>
                                <april:input id="job_title" name="job_title" value="{{ old('job_title', $profile->job_title) }}" />
                                @error('job_title') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex flex-col gap-2">
                                <april:label for="department">Department</april:label>
                                <april:input id="department" name="department" value="{{ old('department', $profile->department) }}" />
                            </div>

                            <div class="flex flex-col gap-2">
                                <april:label for="staff_number">Staff number</april:label>
                                <april:input id="staff_number" name="staff_number" value="{{ old('staff_number', $profile->staff_number) }}" />
                            </div>

                            <div class="flex flex-col gap-2">
                                <april:label for="employment_type">Employment</april:label>
                                <april:native-select id="employment_type" name="employment_type" required>
                                    @foreach ($employmentTypes as $type)
                                        <option value="{{ $type->value }}" @selected(old('employment_type', $profile->employment_type->value) === $type->value)>
                                            {{ $type->label() }}
                                        </option>
                                    @endforeach
                                </april:native-select>
                            </div>

                            <div class="flex flex-col gap-2">
                                <april:label for="status">State</april:label>
                                <april:native-select id="status" name="status" required>
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status->value }}" @selected(old('status', $profile->status->value) === $status->value)>
                                            {{ $status->label() }}
                                        </option>
                                    @endforeach
                                </april:native-select>
                            </div>

                            <div class="flex flex-col gap-2">
                                <april:label for="left_on">Left on</april:label>
                                <input type="date" id="left_on" name="left_on" value="{{ old('left_on', $profile->left_on?->toDateString()) }}"
                                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                                @error('left_on') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                            </div>

                            <input type="hidden" name="joined_on" value="{{ $profile->joined_on?->toDateString() }}">

                            <april:button type="submit" class="lg:col-span-3 lg:justify-self-start">
                                <x-lucide-save class="mr-2 size-4" />
                                Save the job
                            </april:button>
                        </form>
                    @endif
                </div>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>Qualifications</slot:title>
            <slot:description>What the person is qualified to do, and when the paper runs out.</slot:description>
            <slot:content>
                <div class="space-y-6">
                    @if ($profile->credentials->isEmpty())
                        <x-empty-state icon="lucide-award" title="No qualifications recorded"
                            description="Record a teaching licence, a first aid certificate, or a background check." />
                    @else
                        <april:data-table>
                            <slot:header>
                                <april:data-table-row>
                                    <april:data-table-head>Qualification</april:data-table-head>
                                    <april:data-table-head>Kind</april:data-table-head>
                                    <april:data-table-head>Issued</april:data-table-head>
                                    <april:data-table-head class="text-right">Runs out</april:data-table-head>
                                </april:data-table-row>
                            </slot:header>
                            <slot:body>
                                @foreach ($profile->credentials as $credential)
                                    <april:data-table-row>
                                        <april:data-table-cell class="font-medium">
                                            {{ $credential->name }}
                                            <span class="block text-xs text-muted-foreground">{{ $credential->issuer ?? 'No issuer named' }}</span>
                                        </april:data-table-cell>
                                        <april:data-table-cell class="text-muted-foreground">{{ $credential->type }}</april:data-table-cell>
                                        <april:data-table-cell class="whitespace-nowrap text-muted-foreground">
                                            {{ $credential->issued_on?->format('j M Y') ?? 'No date' }}
                                        </april:data-table-cell>
                                        <april:data-table-cell class="whitespace-nowrap text-right">
                                            @if ($credential->expires_on === null)
                                                <span class="text-sm text-muted-foreground">Never</span>
                                            @elseif ($credential->hasExpired())
                                                <span class="text-sm text-destructive">Ran out {{ $credential->expires_on->format('j M Y') }}</span>
                                            @else
                                                <span class="text-sm text-muted-foreground">{{ $credential->expires_on->format('j M Y') }}</span>
                                            @endif
                                        </april:data-table-cell>
                                    </april:data-table-row>
                                @endforeach
                            </slot:body>
                        </april:data-table>
                    @endif

                    @if ($canWrite)
                        <form method="POST" action="{{ route('staff-profiles.credentials.store', $profile) }}"
                            class="grid gap-4 border-t pt-6 lg:grid-cols-5 lg:items-end">
                            @csrf

                            <div class="flex flex-col gap-2">
                                <april:label for="credential-type">Kind</april:label>
                                <april:input id="credential-type" name="type" value="{{ old('type') }}" required placeholder="Licence" />
                                @error('type') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex flex-col gap-2">
                                <april:label for="credential-name">Name</april:label>
                                <april:input id="credential-name" name="name" value="{{ old('name') }}" required />
                                @error('name') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex flex-col gap-2">
                                <april:label for="credential-issuer">Who issued it</april:label>
                                <april:input id="credential-issuer" name="issuer" value="{{ old('issuer') }}" />
                            </div>

                            <div class="flex flex-col gap-2">
                                <april:label for="credential-issued">Issued on</april:label>
                                <input type="date" id="credential-issued" name="issued_on" value="{{ old('issued_on') }}"
                                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                            </div>

                            <div class="flex flex-col gap-2">
                                <april:label for="credential-expires">Runs out on</april:label>
                                <input type="date" id="credential-expires" name="expires_on" value="{{ old('expires_on') }}"
                                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                                @error('expires_on') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                            </div>

                            <april:button type="submit" class="lg:col-span-5 lg:justify-self-start">
                                <x-lucide-plus class="mr-2 size-4" />
                                Add this qualification
                            </april:button>
                        </form>
                    @endif
                </div>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>Working hours</slot:title>
            <slot:description>
                The hours the person can take work. A person who lists no hours is treated as free all week, so a
                school does not have to fill this in before it can plan.
            </slot:description>
            <slot:content>
                <div class="space-y-6">
                    @if ($profile->availabilities->isEmpty())
                        <x-empty-state icon="lucide-clock" title="No hours recorded"
                            description="This person is treated as free at any time the timetable needs them." />
                    @else
                        <ul class="divide-y rounded-md border">
                            @foreach ($profile->availabilities as $block)
                                <li class="flex items-center justify-between px-4 py-3 text-sm">
                                    <span class="font-medium">{{ $days[$block->day_of_week] ?? 'Unknown day' }}</span>
                                    <span class="text-muted-foreground">{{ $block->starts_at }} to {{ $block->ends_at }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    @if ($canWrite)
                        <form method="POST" action="{{ route('staff-profiles.availabilities.store', $profile) }}"
                            class="grid gap-4 border-t pt-6 lg:grid-cols-4 lg:items-end">
                            @csrf

                            <div class="flex flex-col gap-2">
                                <april:label for="day_of_week">Day</april:label>
                                <april:native-select id="day_of_week" name="day_of_week" required>
                                    @foreach ($days as $number => $name)
                                        <option value="{{ $number }}" @selected(old('day_of_week') == $number)>{{ $name }}</option>
                                    @endforeach
                                </april:native-select>
                                @error('day_of_week') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex flex-col gap-2">
                                <april:label for="starts_at">From</april:label>
                                <input type="time" id="starts_at" name="starts_at" value="{{ old('starts_at') }}" required
                                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                                @error('starts_at') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex flex-col gap-2">
                                <april:label for="ends_at">To</april:label>
                                <input type="time" id="ends_at" name="ends_at" value="{{ old('ends_at') }}" required
                                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                                @error('ends_at') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                            </div>

                            <april:button type="submit">
                                <x-lucide-plus class="mr-2 size-4" />
                                Add these hours
                            </april:button>
                        </form>
                    @endif
                </div>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>Leave</slot:title>
            <slot:description>Every time this person asked to be away, and what the school answered.</slot:description>
            <slot:content>
                @if ($profile->leaveRequests->isEmpty())
                    <x-empty-state icon="lucide-plane" title="No leave asked for"
                        description="Leave is asked for on the staff leave screen." />
                @else
                    <april:data-table>
                        <slot:header>
                            <april:data-table-row>
                                <april:data-table-head>Days</april:data-table-head>
                                <april:data-table-head>Kind</april:data-table-head>
                                <april:data-table-head>State</april:data-table-head>
                                <april:data-table-head class="text-right">Why</april:data-table-head>
                            </april:data-table-row>
                        </slot:header>
                        <slot:body>
                            @foreach ($profile->leaveRequests as $leave)
                                <april:data-table-row>
                                    <april:data-table-cell class="whitespace-nowrap font-medium">
                                        {{ $leave->starts_on->format('j M Y') }} to {{ $leave->ends_on->format('j M Y') }}
                                        <span class="block text-xs text-muted-foreground">{{ $leave->days() }} {{ Str::plural('day', $leave->days()) }}</span>
                                    </april:data-table-cell>
                                    <april:data-table-cell class="text-muted-foreground">{{ $leave->type->label() }}</april:data-table-cell>
                                    <april:data-table-cell>
                                        <span class="inline-flex whitespace-nowrap items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold">
                                            {{ $leave->status->label() }}
                                        </span>
                                    </april:data-table-cell>
                                    <april:data-table-cell class="text-right text-muted-foreground">{{ $leave->reason ?? '—' }}</april:data-table-cell>
                                </april:data-table-row>
                            @endforeach
                        </slot:body>
                    </april:data-table>
                @endif
            </slot:content>
        </april:card>
    </div>
@endsection
