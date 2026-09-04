@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('gradebooks.index'), 'text' => 'Gradebooks', 'active'],
]])

@section('title', 'Gradebooks')
@section('page_heading', 'Gradebooks')

@section('content')
    <april:card>
        <slot:title>Gradebooks for {{ $academicPeriod->displayName }}</slot:title>
        <slot:description>Open a subject gradebook for {{ $academicYear->name }}. You are viewing your working term.</slot:description>
        <slot:content>
            @if ($courseOfferings->isEmpty())
                <div class="space-y-3 py-6 text-center">
                    <p class="font-medium">No gradebooks are available for this working term.</p>
                    <p class="text-sm text-muted-foreground">Add subjects to this year first, then open their gradebooks here.</p>
                    <april:button-link href="{{ route('course-offerings.index') }}" variant="outline">View subjects being taught</april:button-link>
                </div>
            @else
                <div class="grid gap-2 md:hidden" role="note">
                    <p class="text-xs text-muted-foreground">Swipe horizontally to view all gradebook columns.</p>
                </div>
                <div class="overflow-x-auto rounded-md border" role="region" aria-label="Gradebook list" tabindex="0">
                    <table class="w-full min-w-[680px] align-middle text-sm">
                        <thead class="border-b text-left text-muted-foreground">
                            <tr>
                                <th class="px-3 py-2">Subject</th>
                                <th class="px-3 py-2">Class</th>
                                <th class="px-3 py-2">Sections</th>
                                <th class="px-3 py-2">Status</th>
                                <th class="px-3 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($courseOfferings as $courseOffering)
                                <tr>
                                    <td class="px-3 py-3 font-medium">
                                        {{ $courseOffering->subject->name }}
                                        @if ($courseOffering->subject->short_name)
                                            <span class="ml-1 text-muted-foreground">{{ $courseOffering->subject->short_name }}</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3">{{ $courseOffering->academicLevel->name }}</td>
                                    <td class="px-3 py-3 text-muted-foreground">
                                        {{ $courseOffering->cycleSections->isEmpty() ? school_roster_label($courseOffering->roster_mode) : $courseOffering->cycleSections->map(fn ($section) => $section->label ?? $section->name)->join(', ') }}
                                    </td>
                                    <td class="px-3 py-3"><april:badge>{{ $courseOffering->status->label() }}</april:badge></td>
                                    <td class="px-3 py-3 text-right">
                                        <april:button-link href="{{ route('course-offerings.gradebook.show', $courseOffering) }}" variant="outline" size="sm">Open gradebook</april:button-link>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $courseOfferings->links('components.pagination-links-view') }}</div>
            @endif
        </slot:content>
    </april:card>
@endsection
