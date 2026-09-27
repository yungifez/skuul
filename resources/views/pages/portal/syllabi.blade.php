@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('portal.overview'), 'text' => 'My school'],
    ['text' => 'Syllabi', 'active'],
]])

@section('title', 'Syllabi')
@section('page_heading', 'Syllabi')

@section('content')
    <div class="mx-auto flex w-full max-w-4xl flex-col gap-5">
        <p class="text-sm text-muted-foreground">
            What each course of {{ $studentRecord->user?->name }} plans to teach, week by week, and what the class has covered · {{ $studentRecord->school?->name }}.
        </p>

        @forelse ($syllabi as $syllabus)
            @php($state = $progress[$syllabus->id])
            <april:card>
                <slot:title>{{ $syllabus->courseOffering->subject->name }}</slot:title>
                <slot:description>
                    {{ $syllabus->name }} · {{ $syllabus->courseOffering->academicPeriod->label ?? $syllabus->courseOffering->academicPeriod->name }}
                </slot:description>
                <slot:content>
                    @if ($state['summary'])
                        <p class="mb-3 text-sm">
                            <span class="font-semibold">{{ $state['summary']['covered'] }} of {{ $state['summary']['total'] }}</span> topics covered
                            @if ($state['summary']['behind'] > 0)
                                · {{ $state['summary']['behind'] }} behind the plan
                            @elseif ($state['summary']['expected'] > 0)
                                · on track
                            @endif
                        </p>
                    @endif
                    @if ($syllabus->description)
                        <p class="mb-3 whitespace-pre-line text-sm">{{ $syllabus->description }}</p>
                    @endif
                    <ol class="divide-y rounded-md border text-sm">
                        @foreach ($syllabus->topics as $topic)
                            @php($record = $state['coverages']->get($topic->id))
                            <li @class(['flex flex-wrap items-start justify-between gap-2 p-2', 'bg-primary/5' => $state['currentWeek'] !== null && $topic->week === $state['currentWeek']])>
                                <div>
                                    <span class="text-muted-foreground">{{ $topic->week ? 'Week '.$topic->week : 'Unscheduled' }} ·</span>
                                    <span class="font-medium">{{ $topic->title }}</span>
                                    @if ($state['currentWeek'] !== null && $topic->week === $state['currentWeek'])
                                        <span class="text-xs font-medium text-primary">This week</span>
                                    @endif
                                    @if ($topic->objectives)
                                        <span class="block text-muted-foreground">{{ $topic->objectives }}</span>
                                    @endif
                                </div>
                                <span class="text-muted-foreground">{{ $record?->status->label() ?? 'Not yet taught' }}</span>
                            </li>
                        @endforeach
                    </ol>
                </slot:content>
            </april:card>
        @empty
            <april:card>
                <slot:content>
                    <p class="text-sm text-muted-foreground">No syllabus is published yet for these courses.</p>
                </slot:content>
            </april:card>
        @endforelse
    </div>
@endsection
