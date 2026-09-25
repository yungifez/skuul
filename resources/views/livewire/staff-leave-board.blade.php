<div class="space-y-6">
    @if ($feedback)
        <april:alert dismissOnTimeout="true">
            <slot:title>Done</slot:title>
            <slot:description>{{ $feedback }}</slot:description>
        </april:alert>
    @endif

    @error('leave')
        <april:alert variant="destructive">
            <slot:title>The leave was not asked for</slot:title>
            <slot:description>{{ $message }}</slot:description>
        </april:alert>
    @enderror

    @error('status')
        <april:alert variant="destructive">
            <slot:title>The leave did not move</slot:title>
            <slot:description>{{ $message }}</slot:description>
        </april:alert>
    @enderror

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
                        <li class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-sm" wire:key="away-{{ $profile->id }}">
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
            <slot:description>The days are held when asked for, so nobody can ask for the same days twice.</slot:description>
            <slot:content>
                <form wire:submit="save" class="grid gap-4 lg:grid-cols-5 lg:items-end">
                    <div class="flex flex-col gap-2">
                        <april:label for="staff_profile_id">Person</april:label>
                        <april:native-select id="staff_profile_id" wire:model="staffProfileId" required>
                            <option value="">Choose a person</option>
                            @foreach ($profiles as $profile)
                                <option value="{{ $profile->id }}">{{ $profile->user?->name ?? 'Unnamed' }}</option>
                            @endforeach
                        </april:native-select>
                        <x-field-error name="staffProfileId" />
                    </div>

                    <div class="flex flex-col gap-2">
                        <april:label for="leave_type">Kind of leave</april:label>
                        <april:native-select id="leave_type" wire:model="leaveType" required>
                            @foreach ($types as $leaveTypeOption)
                                <option value="{{ $leaveTypeOption->value }}">{{ $leaveTypeOption->label() }}</option>
                            @endforeach
                        </april:native-select>
                        <x-field-error name="leaveType" />
                    </div>

                    <div class="flex flex-col gap-2">
                        <april:label for="starts_on">From</april:label>
                        <input type="date" id="starts_on" wire:model="startsOn" required class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                        <x-field-error name="startsOn" />
                    </div>

                    <div class="flex flex-col gap-2">
                        <april:label for="ends_on">To</april:label>
                        <input type="date" id="ends_on" wire:model="endsOn" required class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                        <x-field-error name="endsOn" />
                    </div>

                    <div class="flex flex-col gap-2 lg:col-span-4">
                        <april:label for="leave_reason">Why</april:label>
                        <april:input id="leave_reason" wire:model="reason" placeholder="Optional" />
                        <x-field-error name="reason" />
                    </div>

                    <april:button type="submit" wire:loading.attr="disabled">
                        <x-lucide-plane class="mr-2 size-4" />
                        Ask for these days
                    </april:button>
                </form>
            </slot:content>
        </april:card>
    @endcan

    <april:card>
        <slot:title>Find a request</slot:title>
        <slot:description>Narrow the list to one state or one kind of leave. Your choices stay in the page address.</slot:description>
        <slot:content>
            <div class="grid gap-4 lg:grid-cols-3 lg:items-end">
                <div class="flex flex-col gap-2">
                    <april:label for="filter-status">State</april:label>
                    <april:native-select id="filter-status" wire:model.live="status">
                        <option value="">Every state</option>
                        @foreach ($statuses as $leaveStatus)
                            <option value="{{ $leaveStatus->value }}">{{ $leaveStatus->label() }}</option>
                        @endforeach
                    </april:native-select>
                </div>

                <div class="flex flex-col gap-2">
                    <april:label for="filter-type">Kind of leave</april:label>
                    <april:native-select id="filter-type" wire:model.live="type">
                        <option value="">Every kind</option>
                        @foreach ($types as $leaveTypeOption)
                            <option value="{{ $leaveTypeOption->value }}">{{ $leaveTypeOption->label() }}</option>
                        @endforeach
                    </april:native-select>
                </div>

                @if ($status !== '' || $type !== '')
                    <div>
                        <april:button type="button" variant="outline" wire:click="clearFilters">Clear filters</april:button>
                    </div>
                @endif
            </div>
        </slot:content>
    </april:card>

    <april:card>
        <slot:title>Requests</slot:title>
        <slot:description>A person never answers their own request.</slot:description>
        <slot:content class="space-y-3">
            @forelse ($leaveRequests as $leave)
                <div class="rounded-lg border p-4" wire:key="leave-request-{{ $leave->id }}">
                    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-start">
                        <div class="space-y-1">
                            <p class="font-medium">{{ $leave->staffProfile->user?->name ?? 'Unnamed' }}</p>
                            @if (filled($leave->reason))
                                <p class="text-sm text-muted-foreground">{{ $leave->reason }}</p>
                            @endif
                        </div>
                        <span class="inline-flex w-fit items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold">{{ $leave->status->label() }}</span>
                    </div>

                    <dl class="mt-4 grid gap-3 sm:grid-cols-3">
                        <div>
                            <dt class="text-xs font-medium text-muted-foreground">Days</dt>
                            <dd class="text-sm">{{ $leave->starts_on->format('j M Y') }} to {{ $leave->ends_on->format('j M Y') }} · {{ $leave->days() }} {{ Str::plural('day', $leave->days()) }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-muted-foreground">Kind of leave</dt>
                            <dd class="text-sm">{{ $leave->type->label() }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-muted-foreground">Answer</dt>
                            <dd class="text-sm">
                                @if ($leave->decided_at !== null)
                                    {{ $leave->decidedBy?->name ?? 'Unknown person' }} · {{ $leave->decided_at->format('j M Y') }}
                                @else
                                    Waiting for an answer
                                @endif
                            </dd>
                        </div>
                    </dl>

                    @can('decide', $leave)
                        @if ($leave->status === App\Enums\LeaveStatus::Requested)
                            <div class="mt-4 flex flex-wrap gap-2">
                                <april:button type="button" size="sm" wire:click="changeStatus({{ $leave->id }}, '{{ App\Enums\LeaveStatus::Approved->value }}')" wire:loading.attr="disabled">
                                    <x-lucide-check class="mr-1 size-4" /> Agree
                                </april:button>
                                <april:button type="button" variant="outline" size="sm" wire:click="changeStatus({{ $leave->id }}, '{{ App\Enums\LeaveStatus::Declined->value }}')" wire:loading.attr="disabled">
                                    <x-lucide-x class="mr-1 size-4" /> Say no
                                </april:button>
                            </div>
                        @endif
                    @endcan
                </div>
            @empty
                @if ($status !== '' || $type !== '')
                    <x-empty-state icon="lucide-search-x" title="Nothing matches this filter" description="No request matches those choices.">
                        <april:button type="button" variant="outline" wire:click="clearFilters">Show every request</april:button>
                    </x-empty-state>
                @else
                    <x-empty-state icon="lucide-plane" title="No leave asked for yet" description="Ask for days above. The days are held as soon as they are asked for." />
                @endif
            @endforelse

            {{ $leaveRequests->links('components.datatable-pagination-links-view') }}
        </slot:content>
    </april:card>
</div>
