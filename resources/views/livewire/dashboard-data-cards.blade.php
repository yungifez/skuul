<div class="space-y-10">
    @php
        $snapshotStats = [
            ['label' => school_terms('class_level', 'Class'), 'value' => $academicLevels, 'permission' => 'read class', 'href' => route('academic-levels.index')],
            ['label' => school_terms('section', 'Section'), 'value' => $cycleSections, 'permission' => 'read section', 'href' => route('academic-cycle-sections.index')],
            ['label' => 'Open '.strtolower(school_terms('period', 'Term')), 'value' => $academicPeriods, 'permission' => 'read academic period', 'href' => current_academic_year() === null ? route('academic-years.index') : route('academic-years.show', current_academic_year())],
            ['label' => school_terms('course', 'Subject'), 'value' => $courseOfferings, 'permission' => 'read subject', 'href' => route('course-offerings.index')],
            ['label' => 'Active students', 'value' => $students, 'permission' => 'read student', 'href' => route('students.index')],
            ['label' => 'Teachers', 'value' => $teachers, 'permission' => 'read teacher', 'href' => route('teachers.index')],
            ['label' => 'Parents', 'value' => $parents, 'permission' => 'read parent', 'href' => route('parents.index')],
        ];
        $visibleSnapshotStats = collect($snapshotStats)->filter(
            fn (array $stat): bool => auth()->user()->can($stat['permission']),
        );
    @endphp

    @if ($setupChecklist !== null)
        @php
            $setupPercent = $setupChecklist['total'] > 0 ? (int) round($setupChecklist['completed'] / $setupChecklist['total'] * 100) : 0;
        @endphp
        <section class="flex flex-col gap-4 border-b pb-6 sm:flex-row sm:items-center sm:justify-between" aria-labelledby="school-setup-heading">
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

    @if ($visibleSnapshotStats->isNotEmpty() || $showCampuses)
        <section aria-label="School snapshot">
            <dl class="grid grid-cols-2 gap-x-6 gap-y-5 sm:grid-cols-4 xl:grid-cols-7">
                @if ($showCampuses)
                    <a id="organization-campuses" href="{{ route('organizations.show', $organization) }}" class="group flex min-h-11 select-none flex-col-reverse justify-end">
                        <dt class="truncate text-sm text-muted-foreground group-hover:text-foreground">Campuses</dt>
                        <dd class="text-2xl font-semibold tabular-nums">{{ number_format($organizationSchools) }}</dd>
                    </a>
                @endif
                @foreach ($visibleSnapshotStats as $stat)
                    <a href="{{ $stat['href'] }}" class="group flex min-h-11 select-none flex-col-reverse justify-end">
                        <dt class="truncate text-sm text-muted-foreground group-hover:text-foreground">{{ $stat['label'] }}</dt>
                        <dd class="text-2xl font-semibold tabular-nums">{{ number_format($stat['value']) }}</dd>
                    </a>
                @endforeach
            </dl>
        </section>
    @endif

    @php
        $canReadAttendance = auth()->user()->can('read attendance');
        $canReadCalendar = auth()->user()->can('viewAny', \App\Models\CalendarEvent::class);
    @endphp
    @if ($canReadAttendance || $canReadCalendar)
        <div @class(['grid gap-10', 'lg:grid-cols-[minmax(0,1.5fr)_minmax(0,1fr)]' => $canReadAttendance && $canReadCalendar])>
            @if ($canReadAttendance)
                <section class="min-w-0" aria-labelledby="today-overview">
                    <div class="flex min-h-10 flex-wrap items-center justify-between gap-2 border-b pb-2">
                        <h2 id="today-overview" class="font-semibold">Attendance today</h2>
                        <april:button-link href="{{ route('attendance.register') }}" variant="outline" size="sm">
                            <x-lucide-clipboard-check class="mr-2 size-4" />
                            Take attendance
                        </april:button-link>
                    </div>

                    @if ($todayAttendance['registered'] === 0)
                        <p class="py-4 text-sm text-muted-foreground">No register taken yet.</p>
                    @else
                        <dl class="grid grid-cols-3 gap-4 py-4">
                            <div class="flex flex-col-reverse">
                                <dt class="text-sm text-muted-foreground">Present</dt>
                                <dd class="text-2xl font-semibold tabular-nums">{{ $todayAttendance['rate'] }}%</dd>
                            </div>
                            <div class="flex flex-col-reverse">
                                <dt class="text-sm text-muted-foreground">Registered</dt>
                                <dd class="text-2xl font-semibold tabular-nums">{{ number_format($todayAttendance['registered']) }}<span class="text-sm font-normal text-muted-foreground"> / {{ number_format($students) }}</span></dd>
                            </div>
                            <div class="flex flex-col-reverse">
                                <dt class="text-sm text-muted-foreground">Absent · late</dt>
                                <dd class="text-2xl font-semibold tabular-nums">{{ $todayAttendance['absent'] }}<span class="text-muted-foreground"> · </span>{{ $todayAttendance['late'] }}</dd>
                            </div>
                        </dl>
                    @endif

                    @if (collect($attendanceTrend)->contains(fn (array $day): bool => $day['rate'] !== null))
                        <div class="flex h-28 items-end gap-2" aria-label="Attendance rate over the last seven days" role="img">
                            @foreach ($attendanceTrend as $day)
                                <div class="flex min-w-0 flex-1 flex-col items-center gap-1.5" title="{{ $day['label'] }}: {{ $day['rate'] === null ? 'no register' : $day['rate'].'%' }}">
                                    <div class="flex h-20 w-full items-end">
                                        <div @class(['w-full rounded-t', 'h-1 bg-muted' => $day['rate'] === null, 'bg-foreground/50' => $day['rate'] !== null]) @if ($day['rate'] !== null) style="height: {{ max(8, $day['rate']) }}%" @endif></div>
                                    </div>
                                    <span class="text-xs text-muted-foreground">{{ $day['label'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>
            @endif

            @if ($canReadCalendar)
                @php
                    $calendarRows = collect($todayEvents)
                        ->map(fn (array $event): array => ['title' => $event['title'], 'detail' => collect([$event['type'], $event['location']])->filter()->join(' · '), 'date' => 'Today', 'time' => $event['time']])
                        ->concat(collect($upcomingEvents)->map(fn (array $event): array => ['title' => $event['title'], 'detail' => $event['type'], 'date' => $event['date'], 'time' => $event['time']]));
                @endphp
                <section class="min-w-0" aria-labelledby="upcoming-overview">
                    <div class="flex min-h-10 flex-wrap items-center justify-between gap-2 border-b pb-2">
                        <h2 id="upcoming-overview" class="font-semibold">Next 7 days</h2>
                        <a href="{{ route('calendar-events.index') }}" class="inline-flex min-h-10 select-none items-center gap-1 text-sm text-muted-foreground hover:text-foreground">
                            Calendar <x-lucide-arrow-up-right class="size-4" />
                        </a>
                    </div>
                    @if ($calendarRows->isEmpty())
                        <p class="py-4 text-sm text-muted-foreground">Nothing scheduled.</p>
                    @else
                        <ul class="divide-y">
                            @foreach ($calendarRows as $event)
                                <li class="grid min-h-11 grid-cols-[5.5rem_minmax(0,1fr)] items-center gap-3 py-2">
                                    <span class="text-xs tabular-nums text-muted-foreground">{{ $event['date'] }}<br>{{ $event['time'] }}</span>
                                    <span class="min-w-0">
                                        <span class="block break-words text-sm font-medium">{{ $event['title'] }}</span>
                                        <span class="block text-xs text-muted-foreground">{{ $event['detail'] }}</span>
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>
            @endif
        </div>
    @endif

    @if ($notices !== null)
        <section class="min-w-0" aria-labelledby="notices-overview">
            <div class="flex min-h-10 flex-wrap items-center justify-between gap-2 border-b pb-2">
                <h2 id="notices-overview" class="font-semibold">Notices</h2>
                <a href="{{ route('notices.index') }}" class="inline-flex min-h-10 select-none items-center gap-1 text-sm text-muted-foreground hover:text-foreground">
                    All notices <x-lucide-arrow-up-right class="size-4" />
                </a>
            </div>
            @if ($notices === [])
                <p class="py-4 text-sm text-muted-foreground">No current notices.</p>
            @else
                <ul class="divide-y">
                    @foreach ($notices as $notice)
                        <li>
                            <a href="{{ $notice['url'] }}" class="flex min-h-11 items-center justify-between gap-4 py-2 hover:bg-muted/40">
                                <span class="min-w-0 break-words text-sm font-medium">{{ $notice['title'] }}</span>
                                <span class="shrink-0 text-xs tabular-nums text-muted-foreground">Until {{ $notice['until'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    @endif
</div>
