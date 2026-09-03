@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('portal.overview'), 'text' => 'My school'],
    ['text' => 'Boarding', 'active'],
]])

@section('title', 'Boarding')
@section('page_heading', 'Boarding')

@section('content')
    <april:card>
        <slot:title>{{ $studentRecord->user?->name ?? $studentRecord->admission_number }}</slot:title>
        <slot:description>Current boarding place at {{ $studentRecord->school->name }}.</slot:description>
        <slot:content>
            @if ($place === null)
                <x-empty-state icon="lucide-house" title="No current boarding place"
                    description="The school has not recorded a current house and bed for this learner." />
            @else
                <div class="flex items-center gap-3 rounded-lg border p-4">
                    <span class="flex size-10 items-center justify-center rounded-full bg-muted"><x-lucide-house class="size-5" /></span>
                    <div>
                        <p class="font-medium">{{ $place }}</p>
                        <p class="text-sm text-muted-foreground">House · room · bed</p>
                    </div>
                </div>
                @if ($latestRoll)
                    <div class="mt-4 flex items-center justify-between gap-3 rounded-lg border bg-muted/20 p-4">
                        <div>
                            <p class="text-sm font-medium">Latest boarding check</p>
                            <p class="mt-1 text-sm text-muted-foreground">{{ $latestRoll['type'] }} · {{ $latestRoll['taken_on'] }}</p>
                        </div>
                        <april:badge variant="outline">{{ $latestRoll['status'] }}</april:badge>
                    </div>
                @endif
            @endif
        </slot:content>
    </april:card>
@endsection
