<div class="space-y-6">
    <april:card>
        <slot:title class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
            <span class="flex min-w-0 items-center gap-4">
                <span class="flex size-14 shrink-0 items-center justify-center overflow-hidden rounded-xl border bg-muted text-lg font-semibold text-primary">
                    @if ($school->logo_path)
                        <img src="{{ $school->logo_url }}" alt="" class="size-full object-cover" />
                    @else
                        {{ str($school->name)->substr(0, 2)->upper() }}
                    @endif
                </span>
                <span class="min-w-0">
                    <span class="block truncate text-2xl font-semibold tracking-tight">{{ $school->name }}</span>
                    <span class="mt-1 block text-sm font-normal text-muted-foreground">
                        {{ $school->organization?->name ?? 'Independent school' }}
                    </span>
                </span>
            </span>
            @if ($school->is(current_school()))
                <april:badge variant="secondary" class="w-fit gap-2">
                    <span class="size-2 rounded-full bg-emerald-500"></span>
                    Working school
                </april:badge>
            @endif
        </slot:title>
        <slot:description>Core details and the academic workspace this school is currently using.</slot:description>
        <slot:content>
            <april:separator class="mb-6 mt-0" />
            <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-lg border bg-muted/30 p-4">
                    <dt class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-muted-foreground">
                        <x-lucide-building-2 class="size-4" />
                        School code
                    </dt>
                    <dd class="mt-2 font-medium">{{ $school->code ?: 'Not set' }}</dd>
                </div>
                <div class="rounded-lg border bg-muted/30 p-4">
                    <dt class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-muted-foreground">
                        <x-lucide-at-sign class="size-4" />
                        Short name
                    </dt>
                    <dd class="mt-2 font-medium">{{ $school->initials ?: 'Not set' }}</dd>
                </div>
                <div class="rounded-lg border bg-muted/30 p-4 sm:col-span-2 lg:col-span-1">
                    <dt class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-muted-foreground">
                        <x-lucide-map-pin class="size-4" />
                        Address
                    </dt>
                    <dd class="mt-2 break-words font-medium">{{ $school->address ?: 'Not provided' }}</dd>
                </div>
            </dl>
        </slot:content>
        @can('update', $school)
            <slot:footer>
                <april:button-link href="{{ route('schools.edit', $school) }}" variant="outline" size="sm">
                    <x-lucide-settings class="mr-2 size-4" />
                    Edit school details
                </april:button-link>
            </slot:footer>
        @endcan
    </april:card>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.15fr)_minmax(20rem,0.85fr)]">
        <april:card>
            <slot:title class="flex items-center justify-between gap-3">
                <span class="flex items-center gap-3">
                <span class="flex size-10 items-center justify-center rounded-md bg-muted text-primary">
                    <x-lucide-calendar-range class="size-5" />
                </span>
                Academic workspace
                </span>
                @if ($school->academicYear)
                    <x-academic-period-status-badge :status="$school->academicYear->status" />
                @endif
            </slot:title>
            <slot:description>The calendar and reporting period staff are working in.</slot:description>
            <slot:content>
                <april:separator class="mb-5 mt-0" />
                <dl class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm text-muted-foreground">School calendar</dt>
                        <dd class="mt-1 text-lg font-semibold tracking-tight">{{ $school->academicYear?->name ?? 'Not selected' }}</dd>
                        @if ($school->academicYear?->starts_on && $school->academicYear?->ends_on)
                            <p class="mt-1 text-sm text-muted-foreground">
                                {{ $school->academicYear->starts_on->format('M j, Y') }} – {{ $school->academicYear->ends_on->format('M j, Y') }}
                            </p>
                        @endif
                    </div>
                    <div>
                        <dt class="text-sm text-muted-foreground">Current reporting period</dt>
                        <dd class="mt-1 text-lg font-semibold tracking-tight">{{ $school->academicPeriod?->name ?? 'Not selected' }}</dd>
                        @if ($school->academicPeriod?->starts_on && $school->academicPeriod?->ends_on)
                            <p class="mt-1 text-sm text-muted-foreground">
                                {{ $school->academicPeriod->starts_on->format('M j') }} – {{ $school->academicPeriod->ends_on->format('M j, Y') }}
                            </p>
                        @endif
                    </div>
                </dl>
            </slot:content>
            @if ($school->is(current_school()) && auth()->user()->can('read academic year'))
                <slot:footer>
                    <april:button-link href="{{ route('academic-years.index') }}" variant="link" size="none" class="gap-1 p-0">
                        Manage school calendars <span aria-hidden="true">→</span>
                    </april:button-link>
                </slot:footer>
            @endif
        </april:card>

        <april:card>
            <slot:title class="flex items-center gap-3">
                <span class="flex size-10 items-center justify-center rounded-md bg-muted text-primary">
                    <x-lucide-contact class="size-5" />
                </span>
                Contact details
            </slot:title>
            <slot:description>How families, staff, and partners can reach the school.</slot:description>
            <slot:content>
                <april:separator class="mb-5 mt-0" />
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm text-muted-foreground">Email</dt>
                        <dd class="mt-1 break-words font-medium">
                            @if ($school->email)
                                <a href="mailto:{{ $school->email }}" class="text-primary underline-offset-4 hover:underline">{{ $school->email }}</a>
                            @else
                                Not provided
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-muted-foreground">Phone</dt>
                        <dd class="mt-1 font-medium">
                            @if ($school->phone)
                                <a href="tel:{{ $school->phone }}" class="text-primary underline-offset-4 hover:underline">{{ $school->phone }}</a>
                            @else
                                Not provided
                            @endif
                        </dd>
                    </div>
                </dl>
            </slot:content>
        </april:card>
    </div>
</div>
