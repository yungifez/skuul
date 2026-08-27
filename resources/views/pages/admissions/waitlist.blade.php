@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('admissions.waitlist.index'), 'text' => 'Admissions waitlist', 'active'],
]])

@section('title', __('Admissions waitlist'))
@section('page_heading', __('Admissions waitlist'))

@section('content')
    <div class="mx-auto flex w-full max-w-6xl flex-col gap-6">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-foreground md:text-3xl">Places for full {{ strtolower(school_terms('section', 'sections')) }}</h2>
            <p class="mt-1 text-sm text-muted-foreground">Capacity is enforced when a learner is placed. A waitlist keeps priority and decision history until a place is accepted.</p>
        </div>

        <x-display-validation-errors />

        @can('create', \App\Models\AdmissionWaitlistEntry::class)
            <april:card>
                <slot:title>Add a candidate</slot:title>
                <slot:description>Only {{ strtolower(school_terms('section', 'sections')) }} that have reached their configured capacity appear here.</slot:description>
                <slot:content>
                    <form action="{{ route('admissions.waitlist.store') }}" method="POST" class="grid gap-4 md:grid-cols-4 md:items-end">
                        @csrf
                        <div class="flex flex-col gap-2 md:col-span-2">
                            <label for="waitlist-section" class="text-sm font-medium">{{ school_term('section', 'Section') }}</label>
                            <select id="waitlist-section" name="academic_cycle_section_id" required class="h-10 rounded-md border border-input bg-background px-3 text-sm">
                                @foreach ($sections as $section)
                                    <option value="{{ $section->id }}">{{ $section->academicLevel->name }} · {{ $section->label ?? $section->name }} · {{ $section->academicYear->name }} ({{ $section->capacity }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label for="waitlist-candidate" class="text-sm font-medium">Candidate</label>
                            <select id="waitlist-candidate" name="user_id" required class="h-10 rounded-md border border-input bg-background px-3 text-sm">
                                @foreach ($candidates as $candidate)
                                    <option value="{{ $candidate->id }}">{{ $candidate->name }} · {{ $candidate->email }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label for="waitlist-priority" class="text-sm font-medium">Priority</label>
                            <input id="waitlist-priority" name="priority" type="number" min="0" max="9999" value="0" class="h-10 rounded-md border border-input bg-background px-3 text-sm">
                        </div>
                        <div class="md:col-span-4">
                            <april:button type="submit">Add to waitlist</april:button>
                        </div>
                    </form>
                </slot:content>
            </april:card>
        @endcan

        <april:card>
            <slot:title>Queue</slot:title>
            <slot:description>Higher priority is offered first. Within the same priority, the original position is kept.</slot:description>
            <slot:content>
                @if ($entries->isEmpty())
                    <x-empty-state icon="lucide-users" title="No waitlist entries" description="Candidates added to a full section will appear here." />
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b text-left text-xs uppercase tracking-wider text-muted-foreground">
                                    <th class="p-3 font-medium">Candidate</th>
                                    <th class="p-3 font-medium">Section</th>
                                    <th class="p-3 font-medium">Priority</th>
                                    <th class="p-3 font-medium">Position</th>
                                    <th class="p-3 font-medium">Status</th>
                                    <th class="p-3"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($entries as $entry)
                                    <tr class="border-b last:border-0">
                                        <td class="p-3 font-medium">{{ $entry->candidate?->name }}</td>
                                        <td class="p-3">{{ $entry->academicCycleSection?->academicLevel?->name }} · {{ $entry->academicCycleSection?->name }}</td>
                                        <td class="p-3">{{ $entry->priority }}</td>
                                        <td class="p-3">{{ $entry->position }}</td>
                                        <td class="p-3">{{ $entry->status->label() }}</td>
                                        <td class="p-3 text-right">
                                            @can('update', $entry)
                                                @if ($entry->status === \App\Enums\AdmissionWaitlistStatus::Pending)
                                                    <form action="{{ route('admissions.waitlist.offer', $entry) }}" method="POST" class="inline">
                                                        @csrf
                                                        <april:button type="submit" size="sm" variant="outline">Offer place</april:button>
                                                    </form>
                                                @elseif ($entry->status === \App\Enums\AdmissionWaitlistStatus::Offered)
                                                    <form action="{{ route('admissions.waitlist.accept', $entry) }}" method="POST" class="inline">
                                                        @csrf
                                                        <april:button type="submit" size="sm">Accept and enrol</april:button>
                                                    </form>
                                                @endif
                                                @if ($entry->isOpen())
                                                    <form action="{{ route('admissions.waitlist.decline', $entry) }}" method="POST" class="ml-2 inline">
                                                        @csrf
                                                        <april:button type="submit" size="sm" variant="ghost">Decline</april:button>
                                                    </form>
                                                @endif
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </slot:content>
        </april:card>
    </div>
@endsection
