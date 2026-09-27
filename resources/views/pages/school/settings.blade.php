@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('schools.settings'), 'text' => 'School setup', 'active'],
]])

@section('title', __('School setup'))
@section('page_heading', __('Set up your school'))

@section('content')
    @php
        $dayToDay = [
            ['label' => 'Students', 'icon' => 'lucide-user-round-plus', 'href' => route('students.index')],
            ['label' => 'Parents and guardians', 'icon' => 'lucide-heart-handshake', 'href' => route('parents.index')],
            ['label' => 'Fees and payments', 'icon' => 'lucide-wallet-cards', 'href' => route('fee-invoices.index')],
            ['label' => 'Notices', 'icon' => 'lucide-megaphone', 'href' => route('notices.index')],
            ['label' => 'Staff access', 'icon' => 'lucide-shield-check', 'href' => route('admins.index')],
            ['label' => 'Timetables', 'icon' => 'lucide-clock-3', 'href' => route('timetables.index')],
            ['label' => 'School language', 'icon' => 'lucide-languages', 'href' => route('schools.operating-profile.edit')],
            ['label' => 'School tools', 'icon' => 'lucide-sliders-horizontal', 'href' => route('schools.features.edit')],
        ];
    @endphp
    <div class="mx-auto flex w-full max-w-4xl flex-col gap-10">
        <div class="flex flex-col gap-3 border-b pb-6 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-muted-foreground">{{ $school->name }}</p>
            <april:button-link href="{{ route('schools.setup', [$school, 'details']) }}" :variant="$setupChecklist['required_remaining'] > 0 ? 'default' : 'outline'" class="h-11">Continue guided setup</april:button-link>
        </div>

        <x-school-setup-checklist :checklist="$setupChecklist" />

        <section aria-labelledby="day-to-day-heading" class="flex flex-col gap-3">
            <h2 id="day-to-day-heading" class="text-lg font-semibold">Day to day</h2>
            <ul class="grid gap-x-6 sm:grid-cols-2">
                @foreach ($dayToDay as $area)
                    <li class="border-b">
                        <a href="{{ $area['href'] }}" class="group flex min-h-11 items-center gap-3 py-3 text-sm font-medium select-none">
                            <x-icon :name="$area['icon']" class="size-4 shrink-0 text-muted-foreground" aria-hidden="true" />
                            <span class="flex-1 group-hover:underline">{{ $area['label'] }}</span>
                            <x-lucide-chevron-right class="size-4 shrink-0 text-muted-foreground" aria-hidden="true" />
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    </div>
@endsection
