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

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <april:card>
                <slot:title class="flex items-center justify-between gap-3"><span>School details</span><x-lucide-building-2 class="size-5 text-muted-foreground" /></slot:title>
                <slot:description>School name, address, contacts and logo.</slot:description>
                <slot:content><april:badge variant="secondary">Ready</april:badge></slot:content>
                <slot:footer><april:button-link href="{{ route('schools.edit', $school) }}" variant="link" size="none" class="gap-1 p-0">Review details <span aria-hidden="true">→</span></april:button-link></slot:footer>
            </april:card>

            <april:card>
                <slot:title class="flex items-center justify-between gap-3"><span>School calendar</span><x-lucide-calendar-range class="size-5 text-muted-foreground" /></slot:title>
                <slot:description>{{ $academicYear ? $academicYear->name.' is the current school year.' : 'Choose the school year and the terms your school uses.' }}</slot:description>
                <slot:content><april:badge variant="{{ $academicYear ? 'secondary' : 'outline' }}">{{ $academicYear ? 'Ready' : 'Needs attention' }}</april:badge></slot:content>
                <slot:footer><april:button-link href="{{ route('academic-years.index') }}" variant="link" size="none" class="gap-1 p-0">Manage school year <span aria-hidden="true">→</span></april:button-link></slot:footer>
            </april:card>

            <april:card>
                <slot:title class="flex items-center justify-between gap-3"><span>How teaching works</span><x-lucide-users class="size-5 text-muted-foreground" /></slot:title>
                <slot:description>Choose whether learners stay together all day or move between subject classes.</slot:description>
                <slot:content><april:badge variant="{{ $academicYear ? 'secondary' : 'outline' }}">{{ $academicYear ? 'Choose for this year' : 'Set the school year first' }}</april:badge></slot:content>
                <slot:footer>
                    @if ($academicYear)
                        <april:button-link href="{{ route('academic-years.instructional-model.edit', $academicYear) }}" variant="link" size="none" class="gap-1 p-0">Set teaching approach <span aria-hidden="true">→</span></april:button-link>
                    @endif
                </slot:footer>
            </april:card>

            <april:card>
                <slot:title class="flex items-center justify-between gap-3"><span>Grades and classes</span><x-lucide-presentation class="size-5 text-muted-foreground" /></slot:title>
                <slot:description>{{ $academicLevelsCount ? number_format($academicLevelsCount).' grade or class levels are ready.' : 'Add the grades or classes your school teaches.' }}</slot:description>
                <slot:content><april:badge variant="{{ $academicLevelsCount ? 'secondary' : 'outline' }}">{{ $academicLevelsCount ? 'Ready' : 'Needs attention' }}</april:badge></slot:content>
                <slot:footer><april:button-link href="{{ route('academic-levels.index') }}" variant="link" size="none" class="gap-1 p-0">Manage grades and classes <span aria-hidden="true">→</span></april:button-link></slot:footer>
            </april:card>

            <april:card>
                <slot:title class="flex items-center justify-between gap-3"><span>Classes this year</span><x-lucide-landmark class="size-5 text-muted-foreground" /></slot:title>
                <slot:description>{{ $cycleSectionsCount ? number_format($cycleSectionsCount).' classes are prepared for the current school year.' : 'Create the arms, homerooms or sections that run this year.' }}</slot:description>
                <slot:content><april:badge variant="{{ $cycleSectionsCount ? 'secondary' : 'outline' }}">{{ $cycleSectionsCount ? 'Ready' : 'Needs attention' }}</april:badge></slot:content>
                <slot:footer><april:button-link href="{{ route('academic-cycle-sections.index') }}" variant="link" size="none" class="gap-1 p-0">Manage this year’s classes <span aria-hidden="true">→</span></april:button-link></slot:footer>
            </april:card>

            <april:card>
                <slot:title class="flex items-center justify-between gap-3"><span>Subjects being taught</span><x-lucide-book-marked class="size-5 text-muted-foreground" /></slot:title>
                <slot:description>{{ $courseOfferingsCount ? number_format($courseOfferingsCount).' subjects are set up for the current school year.' : 'Choose the subjects that each grade or class will study.' }}</slot:description>
                <slot:content><april:badge variant="{{ $courseOfferingsCount ? 'secondary' : 'outline' }}">{{ $courseOfferingsCount ? 'Ready' : 'Needs attention' }}</april:badge></slot:content>
                <slot:footer><april:button-link href="{{ route('course-offerings.index') }}" variant="link" size="none" class="gap-1 p-0">Manage subjects being taught <span aria-hidden="true">→</span></april:button-link></slot:footer>
            </april:card>
        </section>

        <section class="space-y-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-muted-foreground">Run the school</p>
                <h2 class="text-xl font-semibold tracking-tight">The day-to-day areas your team manages</h2>
                <p class="text-sm text-muted-foreground">These areas hold the working rules and records your staff use after the school year is prepared.</p>
            </div>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <april:card>
                    <slot:title class="flex items-center justify-between gap-3"><span>Student records and admissions</span><x-lucide-user-round-plus class="size-5 text-muted-foreground" /></slot:title>
                    <slot:description>Admission numbers, learner details, required documents and placement into this year’s classes.</slot:description>
                    <slot:footer><april:button-link href="{{ route('students.index') }}" variant="link" size="none" class="gap-1 p-0">Manage students <span aria-hidden="true">→</span></april:button-link></slot:footer>
                </april:card>

                <april:card>
                    <slot:title class="flex items-center justify-between gap-3"><span>Parents and guardians</span><x-lucide-heart-handshake class="size-5 text-muted-foreground" /></slot:title>
                    <slot:description>Family contacts, parent access and the information staff can share with them.</slot:description>
                    <slot:footer><april:button-link href="{{ route('parents.index') }}" variant="link" size="none" class="gap-1 p-0">Manage families <span aria-hidden="true">→</span></april:button-link></slot:footer>
                </april:card>

                <april:card>
                    <slot:title class="flex items-center justify-between gap-3"><span>Fees and payments</span><x-lucide-wallet-cards class="size-5 text-muted-foreground" /></slot:title>
                    <slot:description>Fee categories, invoices, payment records and the financial rules for this school.</slot:description>
                    <slot:footer><april:button-link href="{{ route('fee-invoices.index') }}" variant="link" size="none" class="gap-1 p-0">Manage fees and payments <span aria-hidden="true">→</span></april:button-link></slot:footer>
                </april:card>

                <april:card>
                    <slot:title class="flex items-center justify-between gap-3"><span>School communication</span><x-lucide-megaphone class="size-5 text-muted-foreground" /></slot:title>
                    <slot:description>Notices and announcements for learners, families and staff.</slot:description>
                    <slot:footer><april:button-link href="{{ route('notices.index') }}" variant="link" size="none" class="gap-1 p-0">Manage notices <span aria-hidden="true">→</span></april:button-link></slot:footer>
                </april:card>

                <april:card>
                    <slot:title class="flex items-center justify-between gap-3"><span>Staff access</span><x-lucide-shield-check class="size-5 text-muted-foreground" /></slot:title>
                    <slot:description>Administrator accounts, invitations and who can carry out each task in the school.</slot:description>
                    <slot:footer><april:button-link href="{{ route('admins.index') }}" variant="link" size="none" class="gap-1 p-0">Manage staff access <span aria-hidden="true">→</span></april:button-link></slot:footer>
                </april:card>

                <april:card>
                    <slot:title class="flex items-center justify-between gap-3"><span>Timetable and school day</span><x-lucide-clock-3 class="size-5 text-muted-foreground" /></slot:title>
                    <slot:description>Lesson times, rooms and the timetable learners and teachers follow each day.</slot:description>
                    <slot:footer><april:button-link href="{{ route('timetables.index') }}" variant="link" size="none" class="gap-1 p-0">Manage timetable <span aria-hidden="true">→</span></april:button-link></slot:footer>
                </april:card>
            </div>
        </section>
    </div>
@endsection
