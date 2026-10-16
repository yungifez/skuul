@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('portal.overview'), 'text' => 'My school'],
    ['text' => 'Programmes', 'active'],
]])

@section('title', 'Programmes')
@section('page_heading', 'Programmes')

@section('content')
    <div class="mx-auto flex w-full max-w-4xl flex-col gap-5">
        <p class="text-sm text-muted-foreground">
            Clubs and activities recorded for {{ $studentRecord->user?->name }} · {{ $studentRecord->school?->name }}.
        </p>

        @forelse ($participations as $participation)
            <april:card>
                <slot:title class="flex flex-wrap items-center gap-2">
                    <span>{{ $participation->program->name }}</span>
                    <april:badge variant="outline">{{ $participation->status->label() }}</april:badge>
                </slot:title>
                <slot:description>{{ $participation->program->type->label() }}</slot:description>
                <slot:content>
                    @if ($participation->program->description)
                        <p class="text-sm">{{ $participation->program->description }}</p>
                    @endif
                    <dl class="mt-3 flex flex-wrap gap-x-5 gap-y-2 text-sm text-muted-foreground">
                        @if ($participation->schedule)
                            <div><dt class="inline font-medium">Schedule:</dt> <dd class="inline">{{ $participation->schedule }}</dd></div>
                        @endif
                        @if ($participation->starts_on || $participation->ends_on)
                            <div>
                                <dt class="inline font-medium">Dates:</dt>
                                <dd class="inline">{{ $participation->starts_on?->format('M j, Y') ?? 'Not set' }} – {{ $participation->ends_on?->format('M j, Y') ?? 'Not set' }}</dd>
                            </div>
                        @endif
                    </dl>
                </slot:content>
            </april:card>
        @empty
            <x-empty-state
                icon="lucide-sparkles"
                title="No club or activity places yet"
                description="When the school records a place in a club or activity, it will appear here." />
        @endforelse
    </div>
@endsection
