@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('schools.settings'), 'text' => 'School setup', 'active'],
]])

@section('title', __('School setup'))
@section('page_heading', __('Set up your school'))

@section('content')
    <div class="mx-auto max-w-6xl space-y-8">
        <section class="rounded-xl border bg-muted/40 p-6 md:p-8">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-muted-foreground">{{ $school->name }}</p>
            <h2 class="mt-2 text-2xl font-semibold tracking-tight">Make sure the essentials are ready before the school year starts.</h2>
            <p class="mt-2 max-w-3xl text-sm text-muted-foreground">Work through the cards below in order. You can return at any time; completed areas stay available for review.</p>
        </section>

        @if (count($needsAttention) > 0)
            <section class="rounded-xl border border-amber-500/30 bg-amber-500/10 p-6" aria-labelledby="needs-attention-heading">
                <div class="flex items-start gap-3">
                    <x-lucide-triangle-alert class="mt-0.5 size-5 shrink-0 text-amber-700 dark:text-amber-300" />
                    <div class="space-y-3">
                        <div>
                            <h2 id="needs-attention-heading" class="text-lg font-semibold">What needs attention</h2>
                            <p class="mt-1 text-sm text-muted-foreground">These areas are not ready yet. The reason is shown beside each one.</p>
                        </div>
                        <ul class="space-y-2 text-sm">
                            @foreach ($needsAttention as $item)
                                <li class="flex flex-col gap-1 sm:flex-row sm:gap-2">
                                    <span class="font-semibold">{{ $item['title'] }}:</span>
                                    <span>{{ $item['reason'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </section>
        @endif

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <april:card>
                <slot:title class="flex items-center justify-between gap-3"><span>School details</span><span class="flex items-center gap-1"><x-help-tooltip label="School details help">School name, address, contacts and logo.</x-help-tooltip><x-lucide-building-2 class="size-5 text-muted-foreground" /></span></slot:title>
                <slot:content><april:badge variant="secondary">Ready</april:badge></slot:content>
                <slot:footer><april:button-link href="{{ route('schools.edit', $school) }}" variant="link" size="none" class="gap-1 p-0">Review details <span aria-hidden="true">→</span></april:button-link></slot:footer>
            </april:card>

            <april:card>
                <slot:title class="flex items-center justify-between gap-3"><span>School calendar</span><span class="flex items-center gap-1"><x-help-tooltip label="School calendar help">Choose the school year and the terms your school uses.</x-help-tooltip><x-lucide-calendar-range class="size-5 text-muted-foreground" /></span></slot:title>
                <slot:content><april:badge variant="{{ $academicYear ? 'secondary' : 'outline' }}">{{ $academicYear ? 'Ready' : 'Needs attention' }}</april:badge></slot:content>
                <slot:footer><april:button-link href="{{ route('academic-years.index') }}" variant="link" size="none" class="gap-1 p-0">Manage school year <span aria-hidden="true">→</span></april:button-link></slot:footer>
            </april:card>

            <april:card>
                <slot:title class="flex items-center justify-between gap-3"><span>How teaching works</span><span class="flex items-center gap-1"><x-help-tooltip label="Teaching approach help">Choose whether learners stay together all day or move between subject classes.</x-help-tooltip><x-lucide-users class="size-5 text-muted-foreground" /></span></slot:title>
                <slot:content><april:badge variant="{{ $academicYear ? 'secondary' : 'outline' }}">{{ $academicYear ? 'Choose for this year' : 'Set the school year first' }}</april:badge></slot:content>
                <slot:footer>
                    @if ($academicYear)
                        <april:button-link href="{{ route('academic-years.instructional-model.edit', $academicYear) }}" variant="link" size="none" class="gap-1 p-0">Set teaching approach <span aria-hidden="true">→</span></april:button-link>
                    @endif
                </slot:footer>
            </april:card>

            <april:card>
                <slot:title class="flex items-center justify-between gap-3"><span>School language</span><span class="flex items-center gap-1"><x-help-tooltip label="School language help">Choose the familiar words your school uses for classes, terms, subjects and fees.</x-help-tooltip><x-lucide-languages class="size-5 text-muted-foreground" /></span></slot:title>
                <slot:footer><april:button-link href="{{ route('schools.operating-profile.edit') }}" variant="link" size="none" class="gap-1 p-0">Set school language <span aria-hidden="true">→</span></april:button-link></slot:footer>
            </april:card>

            <april:card>
                <slot:title class="flex items-center justify-between gap-3"><span>Grades and classes</span><span class="flex items-center gap-1"><x-help-tooltip label="Grades and classes help">Add the grades or classes your school teaches.</x-help-tooltip><x-lucide-presentation class="size-5 text-muted-foreground" /></span></slot:title>
                <slot:content><april:badge variant="{{ $academicLevelsCount ? 'secondary' : 'outline' }}">{{ $academicLevelsCount ? 'Ready' : 'Needs attention' }}</april:badge></slot:content>
                <slot:footer><april:button-link href="{{ route('academic-levels.index') }}" variant="link" size="none" class="gap-1 p-0">Manage grades and classes <span aria-hidden="true">→</span></april:button-link></slot:footer>
            </april:card>

            <april:card>
                <slot:title class="flex items-center justify-between gap-3"><span>Classes this year</span><span class="flex items-center gap-1"><x-help-tooltip label="Classes this year help">Create the arms, homerooms or sections that run this year.</x-help-tooltip><x-lucide-landmark class="size-5 text-muted-foreground" /></span></slot:title>
                <slot:content><april:badge variant="{{ $cycleSectionsCount ? 'secondary' : 'outline' }}">{{ $cycleSectionsCount ? 'Ready' : 'Needs attention' }}</april:badge></slot:content>
                <slot:footer><april:button-link href="{{ route('academic-cycle-sections.index') }}" variant="link" size="none" class="gap-1 p-0">Manage this year’s classes <span aria-hidden="true">→</span></april:button-link></slot:footer>
            </april:card>

            <april:card>
                <slot:title class="flex items-center justify-between gap-3"><span>Subjects being taught</span><span class="flex items-center gap-1"><x-help-tooltip label="Subjects being taught help">Choose the subjects that each grade or class will study.</x-help-tooltip><x-lucide-book-marked class="size-5 text-muted-foreground" /></span></slot:title>
                <slot:content><april:badge variant="{{ $courseOfferingsCount ? 'secondary' : 'outline' }}">{{ $courseOfferingsCount ? 'Ready' : 'Needs attention' }}</april:badge></slot:content>
                <slot:footer><april:button-link href="{{ route('course-offerings.index') }}" variant="link" size="none" class="gap-1 p-0">Manage subjects being taught <span aria-hidden="true">→</span></april:button-link></slot:footer>
            </april:card>

            @can('manage grading scale')
                <april:card>
                    <slot:title class="flex items-center justify-between gap-3"><span>Grading scales</span><span class="flex items-center gap-1"><x-help-tooltip label="Grading scales help">Set the familiar grade names teachers select when marking work.</x-help-tooltip><x-lucide-list-checks class="size-5 text-muted-foreground" /></span></slot:title>
                    <slot:footer><april:button-link href="{{ route('grading-scales.index') }}" variant="link" size="none" class="gap-1 p-0">Manage grading scales <span aria-hidden="true">→</span></april:button-link></slot:footer>
                </april:card>
            @endcan
        </section>

        <section class="space-y-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-muted-foreground">Run the school</p>
                <h2 class="text-xl font-semibold tracking-tight">The day-to-day areas your team manages</h2>
                <p class="text-sm text-muted-foreground">These areas hold the working rules and records your staff use after the school year is prepared.</p>
            </div>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <april:card>
                    <slot:title class="flex items-center justify-between gap-3"><span>Student records and admissions</span><span class="flex items-center gap-1"><x-help-tooltip label="Student records and admissions help">Admission numbers, learner details, required documents and placement into this year’s classes.</x-help-tooltip><x-lucide-user-round-plus class="size-5 text-muted-foreground" /></span></slot:title>
                    <slot:footer><april:button-link href="{{ route('students.index') }}" variant="link" size="none" class="gap-1 p-0">Manage students <span aria-hidden="true">→</span></april:button-link></slot:footer>
                </april:card>

                <april:card>
                    <slot:title class="flex items-center justify-between gap-3"><span>Parents and guardians</span><span class="flex items-center gap-1"><x-help-tooltip label="Parents and guardians help">Family contacts, parent access and the information staff can share with them.</x-help-tooltip><x-lucide-heart-handshake class="size-5 text-muted-foreground" /></span></slot:title>
                    <slot:footer><april:button-link href="{{ route('parents.index') }}" variant="link" size="none" class="gap-1 p-0">Manage families <span aria-hidden="true">→</span></april:button-link></slot:footer>
                </april:card>

                <april:card>
                    <slot:title class="flex items-center justify-between gap-3"><span>Fees and payments</span><span class="flex items-center gap-1"><x-help-tooltip label="Fees and payments help">Fee categories, invoices, payment records and the financial rules for this school.</x-help-tooltip><x-lucide-wallet-cards class="size-5 text-muted-foreground" /></span></slot:title>
                    <slot:footer><april:button-link href="{{ route('fee-invoices.index') }}" variant="link" size="none" class="gap-1 p-0">Manage fees and payments <span aria-hidden="true">→</span></april:button-link></slot:footer>
                </april:card>

                <april:card>
                    <slot:title class="flex items-center justify-between gap-3"><span>School communication</span><span class="flex items-center gap-1"><x-help-tooltip label="School communication help">Notices and announcements for learners, families and staff.</x-help-tooltip><x-lucide-megaphone class="size-5 text-muted-foreground" /></span></slot:title>
                    <slot:footer><april:button-link href="{{ route('notices.index') }}" variant="link" size="none" class="gap-1 p-0">Manage notices <span aria-hidden="true">→</span></april:button-link></slot:footer>
                </april:card>

                <april:card>
                    <slot:title class="flex items-center justify-between gap-3"><span>Staff access</span><span class="flex items-center gap-1"><x-help-tooltip label="Staff access help">Administrator accounts, invitations and who can carry out each task in the school.</x-help-tooltip><x-lucide-shield-check class="size-5 text-muted-foreground" /></span></slot:title>
                    <slot:footer><april:button-link href="{{ route('admins.index') }}" variant="link" size="none" class="gap-1 p-0">Manage staff access <span aria-hidden="true">→</span></april:button-link></slot:footer>
                </april:card>

                <april:card>
                    <slot:title class="flex items-center justify-between gap-3"><span>Timetable and school day</span><span class="flex items-center gap-1"><x-help-tooltip label="Timetable and school day help">Lesson times, rooms and the timetable learners and teachers follow each day.</x-help-tooltip><x-lucide-clock-3 class="size-5 text-muted-foreground" /></span></slot:title>
                    <slot:footer><april:button-link href="{{ route('timetables.index') }}" variant="link" size="none" class="gap-1 p-0">Manage timetable <span aria-hidden="true">→</span></april:button-link></slot:footer>
                </april:card>

                <april:card>
                    <slot:title class="flex items-center justify-between gap-3"><span>Tools your school uses</span><span class="flex items-center gap-1"><x-help-tooltip label="School tools help">Choose which optional school tools are available to staff and families.</x-help-tooltip><x-lucide-sliders-horizontal class="size-5 text-muted-foreground" /></span></slot:title>
                    <slot:footer><april:button-link href="{{ route('schools.features.edit') }}" variant="link" size="none" class="gap-1 p-0">Choose school tools <span aria-hidden="true">→</span></april:button-link></slot:footer>
                </april:card>
            </div>
        </section>
    </div>
@endsection
