<div class="space-y-10">
    @php
        $snapshotStats = [
            ['label' => school_terms('class_level', school_term('class_level', 'Class')), 'value' => $academicLevels, 'icon' => 'presentation', 'permission' => 'read class', 'href' => route('academic-levels.index')],
            ['label' => school_terms('section', school_term('section', 'Section')).' this year', 'value' => $cycleSections, 'icon' => 'landmark', 'permission' => 'read section', 'href' => route('academic-cycle-sections.index')],
            ['label' => 'Open '.strtolower(school_terms('period', school_term('period', 'Term or reporting period'))), 'value' => $academicPeriods, 'icon' => 'clock', 'permission' => 'read academic period', 'href' => current_academic_year() === null ? route('academic-years.index') : route('academic-years.show', current_academic_year())],
            ['label' => school_terms('course', school_term('course', 'Subject')).' being taught', 'value' => $courseOfferings, 'icon' => 'book-marked', 'permission' => 'read subject', 'href' => route('course-offerings.index')],
            ['label' => 'Active students', 'value' => $students, 'icon' => 'users', 'permission' => 'read student', 'href' => route('students.index')],
            ['label' => 'Teachers', 'value' => $teachers, 'icon' => 'graduation-cap', 'permission' => 'read teacher', 'href' => route('teachers.index')],
            ['label' => 'Parents', 'value' => $parents, 'icon' => 'users', 'permission' => 'read parent', 'href' => route('parents.index')],
        ];
        $visibleSnapshotStats = collect($snapshotStats)->filter(
            fn (array $stat): bool => auth()->user()->can($stat['permission']),
        );
    @endphp

    @if ($setupChecklist !== null)
        @php
            $setupPercent = $setupChecklist['total'] > 0 ? (int) round($setupChecklist['completed'] / $setupChecklist['total'] * 100) : 0;
        @endphp
        <section class="flex flex-col gap-4 border-y py-4 sm:flex-row sm:items-center sm:justify-between" aria-labelledby="school-setup-heading">
            <div class="min-w-0">
                <h2 id="school-setup-heading" class="font-semibold">
                    {{ $setupChecklist['required_remaining'] > 0 ? 'Finish setting up your school' : 'School setup is complete' }}
                </h2>
                <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-muted-foreground">
                    <span class="h-1.5 w-32 overflow-hidden rounded-full bg-muted" role="progressbar" aria-label="Setup progress" aria-valuemin="0" aria-valuemax="{{ $setupChecklist['total'] }}" aria-valuenow="{{ $setupChecklist['completed'] }}">
                        <span class="block h-full rounded-full bg-foreground/60" style="width: {{ $setupPercent }}%"></span>
                    </span>
                    <span class="tabular-nums">{{ $setupChecklist['completed'] }} of {{ $setupChecklist['total'] }}</span>
                    @if ($setupChecklist['required_remaining'] > 0)
                        <span>· {{ $setupChecklist['required_remaining'] }} required left</span>
                    @endif
                    <a href="{{ route('schools.settings') }}" class="font-medium text-foreground underline-offset-4 hover:underline">All setup steps</a>
                </div>
            </div>

            @if ($setupChecklist['required_remaining'] > 0 && $setupChecklist['next'] !== null)
                <div class="flex flex-wrap items-center gap-3">
                    <p class="text-sm"><span class="text-muted-foreground">Next:</span> <span class="font-medium">{{ $setupChecklist['next']['title'] }}</span></p>
                    <april:button-link href="{{ $setupChecklist['next']['url'] }}">
                        {{ $setupChecklist['next']['action'] }}
                        <x-lucide-arrow-right class="ml-1.5 size-4" />
                    </april:button-link>
                </div>
            @else
                <form method="POST" action="{{ route('schools.setup.acknowledge') }}" class="shrink-0">
                    @csrf
                    <april:button type="submit">Continue to dashboard</april:button>
                </form>
            @endif
        </section>
    @endif

    @if ($organization && (auth()->user()->can(\App\Enums\PlatformPermission::AccessAllSchools) || $isOrganizationAdministrator || auth()->user()->hasRole(\App\Enums\Role::Admin)))
        <section class="space-y-2" aria-labelledby="organization-heading">
            <h2 id="organization-heading" class="font-semibold">{{ $organization->name }}</h2>
            <div class="grid divide-y border-y sm:grid-cols-3 sm:divide-x sm:divide-y-0">
                <a href="{{ route('organizations.show', $organization) }}" class="flex min-h-14 items-center gap-3 px-3 hover:bg-muted/40">
                    <x-lucide-school class="size-4 shrink-0 text-muted-foreground" />
                    <span class="text-sm">Campuses</span>
                    <span class="ml-auto font-semibold tabular-nums">{{ number_format($organizationSchools) }}</span>
                </a>
                <a href="{{ route('schools.edit', current_school()) }}" class="flex min-h-14 min-w-0 items-center gap-3 px-3 hover:bg-muted/40">
                    <x-lucide-map-pin class="size-4 shrink-0 text-muted-foreground" />
                    <span class="shrink-0 text-sm">Working school</span>
                    <span class="ml-auto min-w-0 truncate text-sm text-muted-foreground">{{ current_school()->name }}</span>
                </a>
                @can('view', $organization)
                    <a href="{{ route('organizations.calendar-templates.index', $organization) }}" class="flex min-h-14 items-center gap-3 px-3 hover:bg-muted/40">
                        <x-lucide-calendar-range class="size-4 shrink-0 text-muted-foreground" />
                        <span class="text-sm">Calendar templates</span>
                        <span class="ml-auto font-semibold tabular-nums">{{ number_format($calendarTemplates) }}</span>
                    </a>
                @endcan
            </div>
        </section>
    @endif

    @if (auth()->user()->can('read attendance') || auth()->user()->can('viewAny', \App\Models\CalendarEvent::class))
        <section class="space-y-4" aria-labelledby="today-overview">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 id="today-overview" class="font-semibold">Today <span class="font-normal text-muted-foreground">· {{ now()->format('D, M j') }}</span></h2>
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

            <div class="grid gap-8 xl:grid-cols-[minmax(0,1.4fr)_minmax(18rem,0.8fr)]">
                <div class="min-w-0 border-t pt-4">
                    <h3 class="text-sm font-medium">Attendance</h3>
                    <dl class="mt-3 grid grid-cols-3 gap-4">
                        <div>
                            <dt class="text-xs text-muted-foreground">Rate</dt>
                            <dd class="text-2xl font-semibold tabular-nums">{{ $todayAttendance['rate'] === null ? '—' : $todayAttendance['rate'].'%' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-muted-foreground">Registered</dt>
                            <dd class="text-2xl font-semibold tabular-nums">{{ number_format($todayAttendance['registered']) }}<span class="text-sm font-normal text-muted-foreground"> / {{ number_format($students) }}</span></dd>
                        </div>
                        <div>
                            <dt class="text-xs text-muted-foreground">Absent · late</dt>
                            <dd class="text-2xl font-semibold tabular-nums">{{ $todayAttendance['absent'] }}<span class="text-muted-foreground"> · </span>{{ $todayAttendance['late'] }}</dd>
                        </div>
                    </dl>

                    <div class="mt-6 flex h-32 items-end gap-2 border-b px-1" aria-label="Attendance rate over the last seven days" role="img">
                        @foreach ($attendanceTrend as $day)
                            <div class="flex min-w-0 flex-1 flex-col items-center gap-2" title="{{ $day['label'] }}: {{ $day['rate'] === null ? 'no register' : $day['rate'].'%' }}">
                                <div class="flex h-24 w-full items-end">
                                    @if ($day['rate'] === null)
                                        <div class="h-1 w-full rounded-t bg-muted"></div>
                                    @else
                                        <div class="w-full rounded-t bg-foreground/50" style="height: {{ max(8, $day['rate']) }}%"></div>
                                    @endif
                                </div>
                                <span class="text-xs text-muted-foreground">{{ $day['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="min-w-0 border-t pt-4">
                    <h3 class="text-sm font-medium">Agenda</h3>
                    @if ($todayEvents === [])
                        <p class="mt-3 text-sm text-muted-foreground">No events today.</p>
                    @else
                        <ul class="mt-2 divide-y">
                            @foreach ($todayEvents as $event)
                                <li class="flex min-h-11 items-center gap-3 py-2">
                                    <span class="w-16 shrink-0 text-xs tabular-nums text-muted-foreground">{{ $event['time'] }}</span>
                                    <span class="min-w-0">
                                        <span class="block break-words text-sm font-medium">{{ $event['title'] }}</span>
                                        <span class="block text-xs text-muted-foreground">{{ collect([$event['type'], $event['location']])->filter()->join(' · ') }}</span>
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </section>
    @endif

    @if ($visibleSnapshotStats->isNotEmpty() || auth()->user()->can('viewAny', \App\Models\CalendarEvent::class))
        <section class="space-y-4" aria-labelledby="upcoming-overview">
            <h2 id="upcoming-overview" class="font-semibold">Next 7 days</h2>

            <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.35fr)]">
                @can('viewAny', \App\Models\CalendarEvent::class)
                    <div class="min-w-0 border-t pt-4">
                        <h3 class="text-sm font-medium">Upcoming events</h3>
                        @if ($upcomingEvents === [])
                            <p class="mt-3 text-sm text-muted-foreground">Nothing scheduled.</p>
                        @else
                            <ul class="mt-2 divide-y">
                                @foreach ($upcomingEvents as $event)
                                    <li class="flex min-h-11 items-center justify-between gap-3 py-2">
                                        <span class="min-w-0">
                                            <span class="block break-words text-sm font-medium">{{ $event['title'] }}</span>
                                            <span class="block text-xs text-muted-foreground">{{ $event['type'] }}</span>
                                        </span>
                                        <span class="shrink-0 text-right text-xs tabular-nums text-muted-foreground">{{ $event['date'] }}<br>{{ $event['time'] }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endcan

                @if ($visibleSnapshotStats->isNotEmpty())
                    <div class="min-w-0 border-t pt-4">
                        <h3 class="text-sm font-medium">School snapshot</h3>
                        <div class="mt-2 grid sm:grid-cols-2 sm:gap-x-6">
                            @foreach ($visibleSnapshotStats as $stat)
                                <a href="{{ $stat['href'] }}" class="flex min-h-11 items-center gap-3 border-b py-2 hover:bg-muted/40">
                                    <x-icon :name="'lucide-'.$stat['icon']" class="size-4 shrink-0 text-muted-foreground" />
                                    <span class="min-w-0 truncate text-sm">{{ $stat['label'] }}</span>
                                    <span class="ml-auto font-semibold tabular-nums">{{ number_format($stat['value']) }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </section>
    @endif
</div>
