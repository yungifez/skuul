@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('syllabi.index'), 'text'=> 'Syllabi', 'active'],
]])

@section('title',  __('Syllabi'))

@section('page_heading',  __('Syllabi'))

@section('page_actions')
    @can('viewCoverage', \App\Models\Syllabus::class)
        <april:button-link href="{{ route('syllabi.coverage') }}" variant="outline">Coverage</april:button-link>
    @endcan
    <x-resource-create-action :href="route('syllabi.create')" ability="create" :arguments="[\App\Models\Syllabus::class]">Add syllabus</x-resource-create-action>
@endsection

@section('content', )
    @if ($awaitingReview->isNotEmpty())
        <april:card class="mb-4">
            <slot:title>Awaiting your review</slot:title>
            <slot:description>Approve each plan before students see it, or send it back with a note.</slot:description>
            <slot:content>
                <ul class="divide-y rounded-md border">
                    @foreach ($awaitingReview as $pending)
                        <li class="flex flex-wrap items-center justify-between gap-2 p-3">
                            <div>
                                <p class="font-medium">{{ $pending->name }}</p>
                                <p class="text-sm text-muted-foreground">
                                    {{ $pending->courseOffering->subject->name }} · {{ $pending->courseOffering->academicLevel->name }}
                                    · Revision {{ $pending->revision }}{{ $pending->submittedBy ? ' · from '.$pending->submittedBy->name : '' }}
                                </p>
                            </div>
                            <april:button-link href="{{ route('syllabi.show', $pending) }}" variant="outline" size="sm">Review<span class="sr-only"> {{ $pending->name }}</span></april:button-link>
                        </li>
                    @endforeach
                </ul>
            </slot:content>
        </april:card>
    @endif
    @if ($lessonNotesAwaitingReview->isNotEmpty())
        <april:card class="mb-4">
            <slot:title>Lesson notes to review</slot:title>
            <slot:description>Weekly lesson notes that teachers sent for your approval.</slot:description>
            <slot:content>
                <ul class="divide-y rounded-md border">
                    @foreach ($lessonNotesAwaitingReview as $pending)
                        <li class="flex flex-wrap items-center justify-between gap-2 p-3">
                            <div>
                                <p class="font-medium">{{ $pending->name }}</p>
                                <p class="text-sm text-muted-foreground">
                                    {{ $pending->courseOffering->subject->name }} · {{ $pending->courseOffering->academicLevel->name }}
                                    · {{ trans_choice(':count note|:count notes', $pending->pending_lesson_notes) }}
                                </p>
                            </div>
                            <april:button-link href="{{ route('syllabi.lesson-notes', ['syllabus' => $pending, 'status' => \App\Enums\LessonNoteStatus::Submitted->value]) }}" variant="outline" size="sm">Review notes<span class="sr-only"> for {{ $pending->name }}</span></april:button-link>
                        </li>
                    @endforeach
                </ul>
            </slot:content>
        </april:card>
    @endif
    @livewire('list-syllabi-table')
@endsection
