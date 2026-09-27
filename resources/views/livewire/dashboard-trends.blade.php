<div>
    @php
        $chartClass = 'rounded-none border-0 bg-transparent p-0 text-foreground';
        $delta = static fn (?float $recent, ?float $previous): ?float => $recent === null || $previous === null ? null : round($recent - $previous, 1);
    @endphp

    @if ($this->hasTrends())
        <section class="space-y-6" aria-labelledby="trends-overview">
            <div class="flex min-h-10 items-center border-b pb-2">
                <h2 id="trends-overview" class="font-semibold">Trends</h2>
            </div>

            <div class="grid gap-x-10 gap-y-12 lg:grid-cols-2">
                @if ($attendance !== null)
                    @php
                        $attendanceChange = $delta($attendance['recent'], $attendance['previous']);
                        $attendanceWeeks = collect($attendance['weeks'])->whereNotNull('rate')->values()->all();
                    @endphp
                    <div class="min-w-0" id="trend-attendance">
                        <div class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1">
                            <h3 class="text-sm font-medium">Attendance</h3>
                            <p class="text-sm text-muted-foreground">Last 4 weeks</p>
                        </div>
                        <p class="mt-1 flex flex-wrap items-baseline gap-x-3">
                            <span class="text-3xl font-semibold tabular-nums">{{ $attendance['recent'] === null ? '—' : $attendance['recent'].'%' }}</span>
                            @if ($attendanceChange !== null)
                                <span class="inline-flex items-center gap-1 text-sm tabular-nums text-muted-foreground">
                                    @if ($attendanceChange >= 0)
                                        <x-lucide-trending-up class="size-4" />
                                    @else
                                        <x-lucide-trending-down class="size-4" />
                                    @endif
                                    {{ $attendanceChange > 0 ? '+' : '' }}{{ $attendanceChange }} pts
                                </span>
                            @endif
                        </p>
                        <april:chart class="{{ $chartClass }} mt-4" label="Weekly attendance rate" :data="$attendanceWeeks" :config="['rate' => ['label' => 'Present %', 'color' => 'var(--chart-2)']]" xKey="week" type="area" height="200" :showLegend="false" />
                    </div>
                @endif

                @if ($fees !== null)
                    @php
                        $billedTotal = collect($fees)->sum('billed');
                        $collectedTotal = collect($fees)->sum('collected');
                    @endphp
                    <div class="min-w-0" id="trend-fees">
                        <div class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1">
                            <h3 class="text-sm font-medium">Fees collected</h3>
                            <p class="text-sm text-muted-foreground">Last 6 months</p>
                        </div>
                        <p class="mt-1 flex flex-wrap items-baseline gap-x-3">
                            <span class="text-3xl font-semibold tabular-nums">{{ money_text($collectedTotal) }}</span>
                            <span class="text-sm tabular-nums text-muted-foreground">of {{ money_text($billedTotal) }} billed</span>
                        </p>
                        <april:chart class="{{ $chartClass }} mt-4" label="Fees billed and collected by month" :data="$fees" :config="['billed' => ['label' => 'Billed', 'color' => 'hsl(var(--muted-foreground) / 0.45)'], 'collected' => ['label' => 'Collected', 'color' => 'var(--chart-2)']]" xKey="month" type="bar" height="200" />
                    </div>
                @endif

                @if ($owed !== null)
                    @php
                        $owedTotal = collect($owed)->sum('owed');
                        $lateTotal = collect($owed)->slice(1)->sum('owed');
                        $largest = max(1, collect($owed)->max('owed'));
                    @endphp
                    <div class="min-w-0" id="trend-owed">
                        <div class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1">
                            <h3 class="text-sm font-medium">Owed</h3>
                            <p class="text-sm text-muted-foreground">By how late</p>
                        </div>
                        <p class="mt-1 flex flex-wrap items-baseline gap-x-3">
                            <span class="text-3xl font-semibold tabular-nums">{{ money_text($owedTotal) }}</span>
                            <span class="text-sm tabular-nums text-muted-foreground">{{ money_text($lateTotal) }} late</span>
                        </p>
                        <ul class="mt-4 grid grid-cols-[auto_minmax(0,1fr)_auto] items-center gap-x-3 gap-y-3 text-sm">
                            @foreach ($owed as $bucket)
                                <li class="contents">
                                    <span class="truncate">{{ $bucket['label'] }}</span>
                                    <span class="relative h-2 overflow-hidden rounded-full bg-muted" aria-hidden="true">
                                        <span @class(['absolute inset-y-0 left-0 rounded-full', 'bg-muted-foreground/40' => $loop->first, 'bg-[var(--chart-5)]' => !$loop->first]) style="width: {{ $bucket['owed'] / $largest * 100 }}%"></span>
                                    </span>
                                    <span @class(['text-right tabular-nums', 'text-muted-foreground' => $bucket['owed'] == 0])>{{ $bucket['owed'] == 0 ? '—' : money_text($bucket['owed']) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($cash !== null)
                    @php
                        $cashIn = collect($cash)->sum('received');
                        $cashOut = collect($cash)->sum('spent');
                        $net = $cashIn - $cashOut;
                    @endphp
                    <div class="min-w-0" id="trend-cash">
                        <div class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1">
                            <h3 class="text-sm font-medium">Money in and out</h3>
                            <p class="text-sm text-muted-foreground">Last 6 months</p>
                        </div>
                        <p class="mt-1 flex flex-wrap items-baseline gap-x-3">
                            <span @class(['text-3xl font-semibold tabular-nums', 'text-destructive' => $net < 0])>{{ $net < 0 ? '−' : '' }}{{ money_text(abs($net)) }}</span>
                            <span class="text-sm tabular-nums text-muted-foreground">{{ money_text($cashIn) }} in · {{ money_text($cashOut) }} out</span>
                        </p>
                        <april:chart class="{{ $chartClass }} mt-4" label="Money received and spent by month" :data="$cash" :config="['received' => ['label' => 'In', 'color' => 'var(--chart-2)'], 'spent' => ['label' => 'Out', 'color' => 'var(--chart-5)']]" xKey="month" type="bar" height="200" />
                    </div>
                @endif

                @if ($enrolment !== null)
                    @php
                        $seats = collect($enrolment)->sum('capacity');
                        $enrolled = collect($enrolment)->sum('students');
                        $widest = max(1, collect($enrolment)->map(fn (array $level): int => max($level['students'], $level['capacity']))->max());
                    @endphp
                    <div class="min-w-0" id="trend-enrolment">
                        <div class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1">
                            <h3 class="text-sm font-medium">Enrolment by {{ strtolower(school_term('class_level', 'class')) }}</h3>
                            <p class="text-sm text-muted-foreground">{{ current_academic_year()?->name }}</p>
                        </div>
                        <p class="mt-1 flex flex-wrap items-baseline gap-x-3">
                            <span class="text-3xl font-semibold tabular-nums">{{ number_format($enrolled) }}</span>
                            <span class="text-sm tabular-nums text-muted-foreground">{{ $seats > 0 ? 'of '.number_format($seats).' seats' : 'students' }}</span>
                        </p>
                        <ul class="mt-4 space-y-3">
                            @foreach ($enrolment as $level)
                                <li class="grid grid-cols-[minmax(0,7rem)_minmax(0,1fr)_auto] items-center gap-3 text-sm">
                                    <span class="truncate">{{ $level['name'] }}</span>
                                    <span class="relative h-2 overflow-hidden rounded-full bg-muted" aria-hidden="true">
                                        @if ($level['capacity'] > 0)
                                            <span class="absolute inset-y-0 left-0 rounded-full bg-muted-foreground/20" style="width: {{ $level['capacity'] / $widest * 100 }}%"></span>
                                        @endif
                                        <span @class(['absolute inset-y-0 left-0 rounded-full', 'bg-[var(--chart-2)]' => $level['capacity'] === 0 || $level['students'] <= $level['capacity'], 'bg-destructive' => $level['capacity'] > 0 && $level['students'] > $level['capacity']]) style="width: {{ $level['students'] / $widest * 100 }}%"></span>
                                    </span>
                                    <span class="tabular-nums text-muted-foreground">{{ $level['students'] }}@if ($level['capacity'] > 0)<span class="text-muted-foreground/70"> / {{ $level['capacity'] }}</span>@endif</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($incidents !== null)
                    @php
                        $incidentWeeks = collect($incidents);
                        $thisWeek = $incidentWeeks->last()['incidents'];
                    @endphp
                    <div class="min-w-0" id="trend-incidents">
                        <div class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1">
                            <h3 class="text-sm font-medium">Incidents</h3>
                            <p class="text-sm text-muted-foreground">Last 8 weeks</p>
                        </div>
                        <p class="mt-1 flex flex-wrap items-baseline gap-x-3">
                            <span class="text-3xl font-semibold tabular-nums">{{ $thisWeek }}</span>
                            <span class="text-sm tabular-nums text-muted-foreground">this week · {{ $incidentWeeks->sum('incidents') }} in 8 weeks</span>
                        </p>
                        <april:chart class="{{ $chartClass }} mt-4" label="Incidents per week" :data="$incidents" :config="['incidents' => ['label' => 'Incidents', 'color' => 'var(--chart-5)']]" xKey="week" type="bar" height="200" :showLegend="false" />
                    </div>
                @endif
            </div>
        </section>
    @endif
</div>
