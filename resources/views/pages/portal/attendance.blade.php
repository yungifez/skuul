@extends('layouts.app', ['breadcrumbs' => [['href' => route('dashboard'), 'text' => 'Dashboard'], ['text' => 'Attendance', 'active']]])
@section('title', 'Attendance')
@section('page_heading', 'Attendance')
@section('content')
<april:card><slot:title>{{ $studentRecord->user?->name ?? $studentRecord->admission_number }}</slot:title><slot:description>Only days where the school took the register are included.</slot:description><slot:content><dl class="grid gap-4 sm:grid-cols-4"><div><dt class="text-sm text-muted-foreground">Attendance rate</dt><dd class="text-2xl font-semibold">{{ $attendance['rate'] === null ? '—' : $attendance['rate'].'%' }}</dd></div><div><dt class="text-sm text-muted-foreground">Present</dt><dd class="text-2xl font-semibold">{{ $attendance['present'] }}</dd></div><div><dt class="text-sm text-muted-foreground">Absent</dt><dd class="text-2xl font-semibold">{{ $attendance['absent'] }}</dd></div><div><dt class="text-sm text-muted-foreground">Recorded days</dt><dd class="text-2xl font-semibold">{{ $attendance['recorded'] }}</dd></div></dl></slot:content></april:card>
@endsection
