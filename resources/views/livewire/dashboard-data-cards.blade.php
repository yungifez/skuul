<div class="space-y-8">
    @if (auth()->user()->can(\App\Enums\PlatformPermission::AccessAllSchools) || $isOrganizationAdministrator || auth()->user()->hasRole(\App\Enums\Role::Admin))
        <april:card>
            <slot:title class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <span>
                    <span class="block text-xs font-semibold uppercase text-muted-foreground">Today at {{ current_school()->name }}</span>
                    <span class="mt-2 block text-2xl font-semibold tracking-tight md:text-3xl">Your school, ready for the day</span>
                </span>
                <april:badge variant="secondary" class="w-fit gap-2">
                    <span class="size-2 rounded-full bg-emerald-500"></span>
                    Live school data
                </april:badge>
            </slot:title>
            <slot:description>See the school year you are working in, then take care of what needs attention next.</slot:description>
            <slot:content>
                <april:separator class="mb-6 mt-0" />
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="flex items-center gap-3 rounded-lg border bg-muted p-4">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-md bg-background text-primary">
                            <x-icon name="lucide-school" class="size-5" />
                        </span>
                        <div>
                            <p class="text-sm font-medium">You are working in</p>
                            <p class="text-sm text-muted-foreground">{{ current_school()->name }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 rounded-lg border bg-muted p-4">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-md bg-background text-primary">
                            <x-icon name="lucide-calendar-range" class="size-5" />
                        </span>
                        <div>
                            <p class="text-sm font-medium">School year and term</p>
                            <p class="text-sm text-muted-foreground">{{ current_academic_year()?->name ?? 'Not selected' }} · {{ current_academic_period()?->name ?? 'No academic period' }}</p>
                        </div>
                    </div>
                </div>
            </slot:content>
        </april:card>

        @if ($setupChecklist !== null)
            <section class="rounded-xl border border-primary/20 bg-primary/5 p-5" aria-labelledby="start-here-heading">
                <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-center">
                    <div class="flex items-start gap-3">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-md bg-primary text-primary-foreground">
                            <x-lucide-list-checks class="size-5" />
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 id="start-here-heading" class="font-semibold">Start here</h2>
                                <x-help-tooltip label="Start here help">This checklist guides school administrators through the setup needed before the academic workspace is ready for daily use.</x-help-tooltip>
                            </div>
                            @if ($setupChecklist['next'] !== null)
                                <p class="mt-1 text-sm text-muted-foreground">
                                    <span class="font-medium text-foreground">Next: {{ $setupChecklist['next']['title'] }}.</span>
                                    {{ $setupChecklist['next']['reason'] }}
                                </p>
                            @else
                                <p class="mt-1 text-sm text-muted-foreground">The required setup is complete. Review the checklist when you are ready for the recommended next steps.</p>
                            @endif
                            <p class="mt-2 text-xs text-muted-foreground">{{ $setupChecklist['completed'] }} of {{ $setupChecklist['total'] }} setup areas complete</p>
                        </div>
                    </div>
                    <div class="flex shrink-0 flex-wrap gap-2">
                        @if ($setupChecklist['next'] !== null)
                            <a href="{{ route('schools.setup', current_school()) }}" class="inline-flex items-center justify-center rounded-md bg-primary px-3 py-2 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-ring">
                                Continue school setup
                                <span aria-hidden="true" class="ml-2">→</span>
                            </a>
                        @endif
                        <a href="{{ route('schools.settings') }}" class="inline-flex items-center justify-center rounded-md border bg-background px-3 py-2 text-sm font-medium transition-colors hover:bg-muted focus:outline-none focus:ring-2 focus:ring-ring">
                            View school setup checklist
                        </a>
                    </div>
                </div>
            </section>
        @endif

        @if ($organization)
            <section class="space-y-4">
                <div>
                    <p class="text-xs font-semibold uppercase text-muted-foreground">Organization context</p>
                    <h3 class="mt-1 text-xl font-semibold tracking-tight">{{ $organization->name }}</h3>
                </div>

                <div class="grid gap-4 md:grid-cols-3">
                    <april:card>
                        <slot:title class="flex items-center justify-between gap-3 text-base">
                            <span class="flex items-center gap-3">
                                <span class="flex size-10 items-center justify-center rounded-md bg-muted text-foreground">
                                    <x-icon name="lucide-school" class="size-5" />
                                </span>
                                Campuses
                            </span>
                            <april:badge variant="outline">{{ number_format($organizationSchools) }}</april:badge>
                        </slot:title>
                        <slot:description>Campuses managed by this organization.</slot:description>
                        <slot:footer>
                            <april:button-link href="{{ route('organizations.show', $organization) }}" variant="link" size="none" class="gap-1 p-0">
                                View organization <span aria-hidden="true">→</span>
                            </april:button-link>
                        </slot:footer>
                    </april:card>

                    <april:card>
                        <slot:title class="flex items-center justify-between gap-3 text-base">
                            <span class="flex items-center gap-3">
                                <span class="flex size-10 items-center justify-center rounded-md bg-muted text-foreground">
                                    <x-icon name="lucide-map-pin" class="size-5" />
                                </span>
                                Working school
                            </span>
                            <x-lucide-arrow-up-right class="size-4 text-muted-foreground" />
                        </slot:title>
                        <slot:description class="truncate">{{ current_school()->name }}</slot:description>
                        <slot:footer>
                            <april:button-link href="{{ route('schools.edit', current_school()) }}" variant="link" size="none" class="gap-1 p-0">
                                Open school settings <span aria-hidden="true">→</span>
                            </april:button-link>
                        </slot:footer>
                    </april:card>

                    @can('view', $organization)
                        <april:card>
                            <slot:title class="flex items-center justify-between gap-3 text-base">
                                <span class="flex items-center gap-3">
                                    <span class="flex size-10 items-center justify-center rounded-md bg-muted text-foreground">
                                        <x-icon name="lucide-calendar-range" class="size-5" />
                                    </span>
                                    Calendar templates
                                </span>
                                <april:badge variant="outline">{{ number_format($calendarTemplates) }}</april:badge>
                            </slot:title>
                            <slot:description>Shared organization patterns for generating campus cycles.</slot:description>
                            <slot:footer>
                                <april:button-link href="{{ route('organizations.calendar-templates.index', $organization) }}" variant="link" size="none" class="gap-1 p-0">
                                    Manage templates <span aria-hidden="true">→</span>
                                </april:button-link>
                            </slot:footer>
                        </april:card>
                    @endcan
                </div>
            </section>
        @endif

        @php
            $snapshotStats = [
                ['label' => school_terms('class_level', school_term('class_level', 'Class')), 'value' => $academicLevels, 'icon' => 'presentation', 'permission' => 'read class', 'href' => route('academic-levels.index'), 'description' => school_terms('class_level', 'Classes')],
                ['label' => school_terms('section', school_term('section', 'Section')).' this year', 'value' => $cycleSections, 'icon' => 'landmark', 'permission' => 'read section', 'href' => route('academic-cycle-sections.index'), 'description' => 'Active '.strtolower(school_terms('section', 'groups'))],
                ['label' => school_terms('period', school_term('period', 'Term or reporting period')), 'value' => $academicPeriods, 'icon' => 'clock', 'permission' => 'read academic period', 'href' => current_academic_year() === null ? route('academic-years.index') : route('academic-years.show', current_academic_year()), 'description' => 'Open periods'],
                ['label' => school_terms('course', school_term('course', 'Subject')).' being taught', 'value' => $courseOfferings, 'icon' => 'book-marked', 'permission' => 'read subject', 'href' => route('course-offerings.index'), 'description' => 'Course offerings'],
                ['label' => 'Active students', 'value' => $students, 'icon' => 'users', 'permission' => 'read student', 'href' => route('students.index'), 'description' => 'Current learners'],
                ['label' => 'Teachers', 'value' => $teachers, 'icon' => 'graduation-cap', 'permission' => 'read teacher', 'href' => route('teachers.index'), 'description' => 'Teaching staff'],
                ['label' => 'Parents', 'value' => $parents, 'icon' => 'users', 'permission' => 'read parent', 'href' => route('parents.index'), 'description' => 'Family accounts'],
            ];
        @endphp

        <section class="space-y-4" aria-labelledby="today-overview">
            <div class="flex flex-col justify-between gap-2 sm:flex-row sm:items-end">
                <div>
                    <p class="text-xs font-semibold uppercase text-muted-foreground">Daily pulse</p>
                    <h3 id="today-overview" class="mt-1 text-xl font-semibold tracking-tight">What needs attention today</h3>
                </div>
                <div class="flex flex-wrap gap-2">
                    @can('read attendance')
                        <april:button-link href="{{ route('attendance.register') }}" variant="outline" size="sm">
                            <x-lucide-clipboard-check class="mr-2 size-4" />
                            Take attendance
                        </april:button-link>
                    @endcan
                    @can('viewAny', \App\Models\CalendarEvent::class)
                        <april:button-link href="{{ route('calendar-events.index') }}" variant="ghost" size="sm">
                            Calendar
                            <x-lucide-arrow-up-right class="ml-2 size-4" />
                        </april:button-link>
                    @endcan
                </div>
            </div>

            <div class="grid gap-4 xl:grid-cols-[minmax(0,1.4fr)_minmax(18rem,0.8fr)]">
                <april:card>
                    <slot:title class="flex items-center justify-between gap-3 text-base">
                        <span>Attendance this week</span>
                        <span class="rounded-full bg-muted px-2.5 py-1 text-xs font-medium text-muted-foreground">{{ now()->format('D, M j') }}</span>
                    </slot:title>
                    <slot:description>Daily register coverage and presence across the last seven days.</slot:description>
                    <slot:content>
                        <div class="grid gap-3 sm:grid-cols-3">
                            <div class="rounded-lg border bg-muted/30 p-3">
                                <p class="text-xs font-medium uppercase text-muted-foreground">Today’s rate</p>
                                <p class="mt-1 text-2xl font-semibold tracking-tight">{{ $todayAttendance['rate'] === null ? '—' : $todayAttendance['rate'].'%' }}</p>
                            </div>
                            <div class="rounded-lg border bg-muted/30 p-3">
                                <p class="text-xs font-medium uppercase text-muted-foreground">Registered</p>
                                <p class="mt-1 text-2xl font-semibold tracking-tight">{{ number_format($todayAttendance['registered']) }} <span class="text-sm font-normal text-muted-foreground">/ {{ number_format($students) }}</span></p>
                            </div>
                            <div class="rounded-lg border bg-muted/30 p-3">
                                <p class="text-xs font-medium uppercase text-muted-foreground">Needs follow-up</p>
                                <p class="mt-1 text-2xl font-semibold tracking-tight">{{ number_format($todayAttendance['absent'] + $todayAttendance['late']) }}</p>
                                <p class="text-xs text-muted-foreground">{{ $todayAttendance['absent'] }} absent · {{ $todayAttendance['late'] }} late</p>
                            </div>
                        </div>

                        <div class="mt-6">
                            <div class="flex h-36 items-end gap-2 border-b px-1">
                                @foreach ($attendanceTrend as $day)
                                    <div class="flex min-w-0 flex-1 flex-col items-center gap-2">
                                        <div class="flex h-28 w-full items-end">
                                            @if ($day['rate'] === null)
                                                <div class="h-1.5 w-full rounded-t bg-muted"></div>
                                            @else
                                                <div class="w-full rounded-t bg-primary/80 transition-all" style="height: {{ max(8, $day['rate']) }}%"></div>
                                            @endif
                                        </div>
                                        <span class="text-xs text-muted-foreground">{{ $day['label'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-2 flex items-center justify-between text-xs text-muted-foreground">
                                <span>Presence rate</span>
                                <span>Bars show registered days</span>
                            </div>
                        </div>
                    </slot:content>
                </april:card>

                <april:card>
                    <slot:title class="text-base">Today’s agenda</slot:title>
                    <slot:description>Published events for {{ now()->format('l, M j') }}.</slot:description>
                    <slot:content>
                        @if ($todayEvents === [])
                            <div class="flex min-h-40 flex-col items-center justify-center rounded-lg border border-dashed px-4 text-center">
                                <x-lucide-coffee class="size-6 text-muted-foreground" />
                                <p class="mt-3 text-sm font-medium">No events on the calendar</p>
                                <p class="mt-1 text-xs text-muted-foreground">The day is clear so far.</p>
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach ($todayEvents as $event)
                                    <div class="flex gap-3 rounded-lg border p-3">
                                        <span class="flex size-8 shrink-0 items-center justify-center rounded-md bg-muted text-muted-foreground">
                                            <x-lucide-calendar-days class="size-4" />
                                        </span>
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-medium">{{ $event['title'] }}</p>
                                            <p class="mt-0.5 text-xs text-muted-foreground">{{ $event['time'] }} · {{ $event['type'] }}</p>
                                            @if ($event['location'])
                                                <p class="mt-0.5 truncate text-xs text-muted-foreground">{{ $event['location'] }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </slot:content>
                </april:card>
            </div>
        </section>

        <section class="space-y-4" aria-labelledby="upcoming-overview">
            <div class="flex flex-col justify-between gap-2 sm:flex-row sm:items-end">
                <div>
                    <p class="text-xs font-semibold uppercase text-muted-foreground">Coming up</p>
                    <h3 id="upcoming-overview" class="mt-1 text-xl font-semibold tracking-tight">The next few days</h3>
                </div>
                @can('viewAny', \App\Models\CalendarEvent::class)
                    <april:button-link href="{{ route('calendar-events.index') }}" variant="link" size="none" class="w-fit gap-1 p-0">
                        Open calendar <span aria-hidden="true">→</span>
                    </april:button-link>
                @endcan
            </div>

            <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.35fr)]">
                <div class="rounded-xl border bg-card p-5">
                    <div class="flex items-center gap-3">
                        <span class="flex size-9 items-center justify-center rounded-md bg-muted text-muted-foreground">
                            <x-lucide-calendar-clock class="size-4" />
                        </span>
                        <div>
                            <p class="font-medium">Upcoming events</p>
                            <p class="text-sm text-muted-foreground">The next seven days</p>
                        </div>
                    </div>
                    @if ($upcomingEvents === [])
                        <p class="mt-6 text-sm text-muted-foreground">Nothing published yet.</p>
                    @else
                        <div class="mt-5 space-y-3">
                            @foreach ($upcomingEvents as $event)
                                <div class="flex items-center justify-between gap-3 border-t pt-3 first:border-0 first:pt-0">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-medium">{{ $event['title'] }}</p>
                                        <p class="text-xs text-muted-foreground">{{ $event['type'] }}</p>
                                    </div>
                                    <div class="shrink-0 text-right text-xs text-muted-foreground">
                                        <p>{{ $event['date'] }}</p>
                                        <p>{{ $event['time'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="rounded-xl border bg-card p-5">
                    <div class="flex items-center gap-3">
                        <span class="flex size-9 items-center justify-center rounded-md bg-muted text-muted-foreground">
                            <x-lucide-layout-dashboard class="size-4" />
                        </span>
                        <div>
                            <p class="font-medium">School snapshot</p>
                            <p class="text-sm text-muted-foreground">The current working context</p>
                        </div>
                    </div>
                    <div class="mt-5 grid gap-px overflow-hidden rounded-lg border bg-border sm:grid-cols-2">
                        @foreach ($snapshotStats as $stat)
                            @if (auth()->user()->can($stat['permission']))
                                <a href="{{ $stat['href'] }}" class="group bg-card p-3 transition-colors hover:bg-muted/40">
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="truncate text-sm text-muted-foreground">{{ $stat['label'] }}</span>
                                        <x-icon :name="'lucide-'.$stat['icon']" class="size-4 shrink-0 text-muted-foreground transition-colors group-hover:text-foreground" />
                                    </div>
                                    <p class="mt-2 text-xl font-semibold tracking-tight">{{ number_format($stat['value']) }}</p>
                                    <p class="text-xs text-muted-foreground">{{ $stat['description'] }}</p>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif
</div>
