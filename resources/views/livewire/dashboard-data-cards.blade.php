<div class="space-y-8">
    @if (auth()->user()->can(\App\Enums\PlatformPermission::AccessAllSchools) || $isOrganizationAdministrator || auth()->user()->hasRole(\App\Enums\Role::Admin))
        <april:card>
            <slot:title class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <span>
                    <span class="block text-xs font-semibold uppercase tracking-[0.2em] text-muted-foreground">School operations</span>
                    <span class="mt-2 block text-2xl font-semibold tracking-tight md:text-3xl">Your school at a glance</span>
                </span>
                <april:badge variant="secondary" class="w-fit gap-2">
                    <span class="size-2 rounded-full bg-emerald-500"></span>
                    Live school data
                </april:badge>
            </slot:title>
            <slot:description>Monitor the people and academic structure that keep {{ current_school()->name }} moving.</slot:description>
            <slot:content>
                <april:separator class="mb-6 mt-0" />
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="flex items-center gap-3 rounded-lg border bg-muted p-4">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-md bg-background text-primary">
                            <x-icon name="lucide-school" class="size-5" />
                        </span>
                        <div>
                            <p class="text-sm font-medium">Current school</p>
                            <p class="text-sm text-muted-foreground">{{ current_school()->name }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 rounded-lg border bg-muted p-4">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-md bg-background text-primary">
                            <x-icon name="lucide-calendar-range" class="size-5" />
                        </span>
                        <div>
                            <p class="text-sm font-medium">Current academic period</p>
                            <p class="text-sm text-muted-foreground">{{ current_academic_year()?->name ?? 'Not selected' }} · {{ current_academic_period()?->name ?? 'No academic period' }}</p>
                        </div>
                    </div>
                </div>
            </slot:content>
        </april:card>

        @if ($organization)
            <section class="space-y-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-muted-foreground">Organization context</p>
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
            $academicStats = [
                ['label' => 'Academic levels', 'value' => $academicLevels, 'icon' => 'presentation', 'permission' => 'read class', 'href' => route('academic-levels.index'), 'description' => 'Reusable levels configured for this school'],
                ['label' => 'Cycle sections', 'value' => $cycleSections, 'icon' => 'landmark', 'permission' => 'read section', 'href' => route('academic-cycle-sections.index'), 'description' => 'Home sections in the current cycle'],
                ['label' => 'Academic periods', 'value' => $academicPeriods, 'icon' => 'clock', 'permission' => 'read academic period', 'href' => route('academic-periods.index'), 'description' => 'Periods in the current academic cycle'],
                ['label' => 'Course offerings', 'value' => $courseOfferings, 'icon' => 'book-marked', 'permission' => 'read subject', 'href' => route('course-offerings.index'), 'description' => 'Subjects configured for the current cycle'],
            ];

            $peopleStats = [
                ['label' => 'Active students', 'value' => $students, 'icon' => 'users', 'permission' => 'read student', 'href' => route('students.index')],
                ['label' => 'Teachers', 'value' => $teachers, 'icon' => 'graduation-cap', 'permission' => 'read teacher', 'href' => route('teachers.index')],
                ['label' => 'Parents', 'value' => $parents, 'icon' => 'users', 'permission' => 'read parent', 'href' => route('parents.index')],
            ];
        @endphp

        <section class="space-y-4">
            <div class="flex flex-col justify-between gap-2 sm:flex-row sm:items-end">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-muted-foreground">Academic setup</p>
                    <h3 class="mt-1 text-xl font-semibold tracking-tight">The structure used for this cycle</h3>
                </div>
                <p class="text-sm text-muted-foreground">Updated from your current school context</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($academicStats as $stat)
                    @if ($stat['permission'] === null || auth()->user()->can($stat['permission']))
                        <april:card>
                            <slot:title class="flex items-center justify-between gap-3 text-base">
                                <span>{{ $stat['label'] }}</span>
                                <span class="flex size-9 items-center justify-center rounded-md bg-muted text-foreground">
                                    <x-icon :name="'lucide-'.$stat['icon']" class="size-4" />
                                </span>
                            </slot:title>
                            <slot:content class="flex flex-col">
                                <p class="text-3xl font-semibold tracking-tight">{{ number_format($stat['value']) }}</p>
                                <p class="mt-1 text-sm text-muted-foreground">{{ $stat['description'] }}</p>
                                <april:separator />
                                <april:button-link href="{{ $stat['href'] }}" variant="link" size="none" class="w-fit gap-1 p-0">
                                    View details <span aria-hidden="true">→</span>
                                </april:button-link>
                            </slot:content>
                        </april:card>
                    @endif
                @endforeach
            </div>
        </section>

        <section class="space-y-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-muted-foreground">People</p>
                <h3 class="mt-1 text-xl font-semibold tracking-tight">Everyone currently in scope</h3>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($peopleStats as $stat)
                    @if (auth()->user()->can($stat['permission']))
                        <april:card>
                            <slot:title class="flex items-center justify-between gap-3 text-base">
                                <span>{{ $stat['label'] }}</span>
                                <span class="flex size-9 items-center justify-center rounded-md bg-muted text-foreground">
                                    <x-icon :name="'lucide-'.$stat['icon']" class="size-4" />
                                </span>
                            </slot:title>
                            <slot:content class="flex flex-col">
                                <p class="text-3xl font-semibold tracking-tight">{{ number_format($stat['value']) }}</p>
                                <p class="mt-1 text-sm text-muted-foreground">{{ $stat['label'] }} currently in scope</p>
                                <april:separator />
                                <april:button-link href="{{ $stat['href'] }}" variant="link" size="none" class="w-fit gap-1 p-0">
                                    View details <span aria-hidden="true">→</span>
                                </april:button-link>
                            </slot:content>
                        </april:card>
                    @endif
                @endforeach
            </div>
        </section>
    @endif
</div>
