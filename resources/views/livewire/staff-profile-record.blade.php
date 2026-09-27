<div class="flex flex-col gap-10">
    @php
        $controlClasses = 'mt-1 h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
        $days = App\Livewire\StaffProfileRecord::DAYS;
    @endphp

    @if ($isAway)
        <p class="flex items-center gap-2 text-sm font-medium">
            <x-lucide-plane class="size-4" aria-hidden="true" />Away today. The timetable gives them no cover work while the leave holds.
        </p>
    @endif

    <section class="flex flex-col gap-3" aria-labelledby="job-heading">
        <div class="flex items-center justify-between gap-3">
            <h2 id="job-heading" class="text-base font-semibold">The job</h2>
            @if ($canWrite && !$isEditingJob)
                <april:button type="button" variant="outline" class="h-11 select-none" wire:click="startEditingJob">
                    <x-lucide-pencil class="mr-2 size-4" aria-hidden="true" />Change
                </april:button>
            @endif
        </div>

        @if ($isEditingJob)
            <form wire:submit="saveJob" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3" aria-label="Change the job">
                <div>
                    <label for="job_title" class="text-sm text-muted-foreground">Job title</label>
                    <input id="job_title" wire:model="jobTitle" maxlength="100" class="{{ $controlClasses }}" {{ field_error_bindings('jobTitle') }}>
                    <x-field-error name="jobTitle" class="mt-1" />
                </div>
                <div>
                    <label for="department" class="text-sm text-muted-foreground">Department</label>
                    <input id="department" wire:model="department" maxlength="100" class="{{ $controlClasses }}" {{ field_error_bindings('department') }}>
                    <x-field-error name="department" class="mt-1" />
                </div>
                <div>
                    <label for="staff_number" class="text-sm text-muted-foreground">Staff number</label>
                    <input id="staff_number" wire:model="staffNumber" maxlength="30" autocomplete="off" class="{{ $controlClasses }}" {{ field_error_bindings('staffNumber') }}>
                    <x-field-error name="staffNumber" class="mt-1" />
                </div>
                <div>
                    <label for="employment_type" class="text-sm text-muted-foreground">Employment</label>
                    <select id="employment_type" wire:model="employmentType" class="{{ $controlClasses }}" {{ field_error_bindings('employmentType') }}>
                        @foreach ($employmentTypes as $type)
                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                        @endforeach
                    </select>
                    <x-field-error name="employmentType" class="mt-1" />
                </div>
                <div>
                    <label for="status" class="text-sm text-muted-foreground">State</label>
                    <select id="status" wire:model.live="status" class="{{ $controlClasses }}" {{ field_error_bindings('status') }}>
                        @foreach ($statuses as $state)
                            <option value="{{ $state->value }}">{{ $state->label() }}</option>
                        @endforeach
                    </select>
                    <x-field-error name="status" class="mt-1" />
                </div>
                @if ($status === App\Enums\StaffStatus::Left->value)
                    <div>
                        <label for="left_on" class="text-sm text-muted-foreground">Left on</label>
                        <input type="date" id="left_on" wire:model="leftOn" class="{{ $controlClasses }}" {{ field_error_bindings('leftOn') }}>
                        <p class="mt-1 text-xs text-muted-foreground">Empty means today. Leave still held after it is withdrawn.</p>
                        <x-field-error name="leftOn" class="mt-1" />
                    </div>
                @endif
                <div class="flex justify-end gap-2 sm:col-span-2 lg:col-span-3">
                    <april:button type="button" variant="ghost" class="h-11 select-none" wire:click="stopEditingJob">Cancel</april:button>
                    <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="saveJob">Save</april:button>
                </div>
            </form>
        @else
            <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm sm:grid-cols-4">
                <div>
                    <dt class="text-muted-foreground">State</dt>
                    <dd class="font-medium">{{ $profile->status->label() }}</dd>
                </div>
                <div>
                    <dt class="text-muted-foreground">Job</dt>
                    <dd class="font-medium">{{ $profile->job_title ?? '—' }}</dd>
                    <dd class="text-xs text-muted-foreground">{{ $profile->department ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-muted-foreground">Employment</dt>
                    <dd class="font-medium">{{ $profile->employment_type->label() }}</dd>
                    <dd class="text-xs text-muted-foreground">{{ $profile->staff_number ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-muted-foreground">Joined</dt>
                    <dd class="font-medium">{{ $profile->joined_on?->format('j M Y') ?? '—' }}</dd>
                    <dd class="text-xs text-muted-foreground">{{ $profile->left_on === null ? 'Still here' : 'Left '.$profile->left_on->format('j M Y') }}</dd>
                </div>
            </dl>
            <p class="text-xs text-muted-foreground">{{ $profile->user?->email ?? '—' }}</p>
        @endif
    </section>

    <section class="flex flex-col gap-3" aria-labelledby="credentials-heading">
        <h2 id="credentials-heading" class="text-base font-semibold">Qualifications</h2>
        @if ($profile->credentials->isEmpty())
            <p class="text-sm text-muted-foreground">None recorded. A teaching licence, first aid certificate or background check goes here.</p>
        @else
            <ul class="divide-y border-y">
                @foreach ($profile->credentials as $credential)
                    <li wire:key="credential-{{ $credential->id }}" class="flex items-center gap-3 py-2">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">{{ $credential->name }}</p>
                            <p class="truncate text-xs text-muted-foreground">
                                {{ $credential->type }} · {{ $credential->issuer ?? '—' }} · issued {{ $credential->issued_on?->format('j M Y') ?? '—' }}
                            </p>
                        </div>
                        <span @class(['whitespace-nowrap text-xs', 'text-destructive font-medium' => $credential->hasExpired(), 'text-muted-foreground' => !$credential->hasExpired()])>
                            @if ($credential->expires_on === null)
                                Never runs out
                            @elseif ($credential->hasExpired())
                                Ran out {{ $credential->expires_on->format('j M Y') }}
                            @else
                                Runs out {{ $credential->expires_on->format('j M Y') }}
                            @endif
                        </span>
                        @if ($canWrite)
                            <april:dropdown-menu>
                                <slot:trigger>
                                    <april:button type="button" variant="ghost" size="icon" class="size-11 select-none" aria-label="More for {{ $credential->name }}">
                                        <x-lucide-ellipsis class="size-4" />
                                    </april:button>
                                </slot:trigger>
                                <slot:content align="end">
                                    <april:dropdown-menu-item wire:click="removeCredential({{ $credential->id }})" wire:confirm="Remove {{ $credential->name }} from this record?"><x-lucide-trash-2 class="mr-2 size-4" />Remove</april:dropdown-menu-item>
                                </slot:content>
                            </april:dropdown-menu>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif

        @if ($canWrite)
            <form wire:submit="addCredential" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5 lg:items-start" aria-label="Add a qualification">
                <div>
                    <label for="credential-type" class="text-sm text-muted-foreground">Kind</label>
                    <input id="credential-type" wire:model="credentialType" required maxlength="50" placeholder="Licence" class="{{ $controlClasses }}" {{ field_error_bindings('credentialType') }}>
                    <x-field-error name="credentialType" class="mt-1" />
                </div>
                <div>
                    <label for="credential-name" class="text-sm text-muted-foreground">Name</label>
                    <input id="credential-name" wire:model="credentialName" required maxlength="150" class="{{ $controlClasses }}" {{ field_error_bindings('credentialName') }}>
                    <x-field-error name="credentialName" class="mt-1" />
                </div>
                <div>
                    <label for="credential-issuer" class="text-sm text-muted-foreground">Issued by (optional)</label>
                    <input id="credential-issuer" wire:model="credentialIssuer" maxlength="150" class="{{ $controlClasses }}" {{ field_error_bindings('credentialIssuer') }}>
                    <x-field-error name="credentialIssuer" class="mt-1" />
                </div>
                <div>
                    <label for="credential-issued" class="text-sm text-muted-foreground">Issued on (optional)</label>
                    <input type="date" id="credential-issued" wire:model="credentialIssuedOn" class="{{ $controlClasses }}" {{ field_error_bindings('credentialIssuedOn') }}>
                    <x-field-error name="credentialIssuedOn" class="mt-1" />
                </div>
                <div>
                    <label for="credential-expires" class="text-sm text-muted-foreground">Runs out on (optional)</label>
                    <input type="date" id="credential-expires" wire:model="credentialExpiresOn" class="{{ $controlClasses }}" {{ field_error_bindings('credentialExpiresOn') }}>
                    <x-field-error name="credentialExpiresOn" class="mt-1" />
                </div>
                <div class="flex justify-end sm:col-span-2 lg:col-span-5">
                    <april:button type="submit" variant="outline" class="h-11 select-none" wire:loading.attr="disabled" wire:target="addCredential">Add the qualification</april:button>
                </div>
            </form>
        @endif
    </section>

    <section class="flex flex-col gap-3" aria-labelledby="hours-heading">
        <div>
            <h2 id="hours-heading" class="text-base font-semibold">Working hours</h2>
            <p class="text-sm text-muted-foreground">Somebody with no hours listed is free all week.</p>
        </div>
        @if ($profile->availabilities->isNotEmpty())
            <ul class="divide-y border-y">
                @foreach ($profile->availabilities as $block)
                    <li wire:key="hours-{{ $block->id }}" class="flex items-center gap-3 py-1">
                        <p class="min-w-0 flex-1 text-sm"><span class="font-medium">{{ $days[$block->day_of_week] ?? '—' }}</span> <span class="text-muted-foreground">{{ substr((string) $block->starts_at, 0, 5) }} to {{ substr((string) $block->ends_at, 0, 5) }}</span></p>
                        @if ($canWrite)
                            <april:dropdown-menu>
                                <slot:trigger>
                                    <april:button type="button" variant="ghost" size="icon" class="size-11 select-none" aria-label="More for {{ $days[$block->day_of_week] ?? 'these hours' }}">
                                        <x-lucide-ellipsis class="size-4" />
                                    </april:button>
                                </slot:trigger>
                                <slot:content align="end">
                                    <april:dropdown-menu-item wire:click="removeHours({{ $block->id }})" wire:confirm="Remove these hours?"><x-lucide-trash-2 class="mr-2 size-4" />Remove</april:dropdown-menu-item>
                                </slot:content>
                            </april:dropdown-menu>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif

        @if ($canWrite)
            <form wire:submit="addHours" class="grid grid-cols-2 gap-3 sm:grid-cols-4 sm:items-start" aria-label="Add working hours">
                <div class="col-span-2 sm:col-span-1">
                    <label for="day_of_week" class="text-sm text-muted-foreground">Day</label>
                    <select id="day_of_week" wire:model="dayOfWeek" class="{{ $controlClasses }}" {{ field_error_bindings('dayOfWeek') }}>
                        @foreach ($days as $number => $name)
                            <option value="{{ $number }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    <x-field-error name="dayOfWeek" class="mt-1" />
                </div>
                <div>
                    <label for="starts_at" class="text-sm text-muted-foreground">From</label>
                    <input type="time" id="starts_at" wire:model="startsAt" required class="{{ $controlClasses }}" {{ field_error_bindings('startsAt') }}>
                    <x-field-error name="startsAt" class="mt-1" />
                </div>
                <div>
                    <label for="ends_at" class="text-sm text-muted-foreground">To</label>
                    <input type="time" id="ends_at" wire:model="endsAt" required class="{{ $controlClasses }}" {{ field_error_bindings('endsAt') }}>
                    <x-field-error name="endsAt" class="mt-1" />
                </div>
                <div class="col-span-2 flex justify-end sm:col-span-1 sm:mt-6">
                    <april:button type="submit" variant="outline" class="h-11 select-none" wire:loading.attr="disabled" wire:target="addHours">Add the hours</april:button>
                </div>
            </form>
        @endif
    </section>

    <section class="flex flex-col gap-3" aria-labelledby="leave-heading">
        <h2 id="leave-heading" class="text-base font-semibold">Leave</h2>
        @if ($profile->leaveRequests->isEmpty())
            <p class="text-sm text-muted-foreground">None asked for. Leave is asked for on the staff leave page.</p>
        @else
            <ul class="divide-y border-y">
                @foreach ($profile->leaveRequests as $leave)
                    <li wire:key="leave-{{ $leave->id }}" class="flex items-center gap-3 py-2">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium">{{ $leave->starts_on->format('j M Y') }} to {{ $leave->ends_on->format('j M Y') }}</p>
                            <p class="truncate text-xs text-muted-foreground">{{ $leave->type->label() }} · {{ $leave->days() }} {{ Str::plural('day', $leave->days()) }} · {{ $leave->reason ?? '—' }}</p>
                        </div>
                        <span class="whitespace-nowrap text-xs text-muted-foreground">{{ $leave->status->label() }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
</div>
