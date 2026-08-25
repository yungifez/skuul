@extends('layouts.app', ['breadcrumbs' => [['href' => route('dashboard'), 'text' => 'Dashboard'], ['text' => 'Library', 'active']]])

@section('title', 'Library')
@section('page_heading', 'Library')

@section('content')
    <div class="flex flex-col gap-6">
        <april:card>
            <slot:title>{{ $studentRecord->user?->name ?? $studentRecord->admission_number }}</slot:title>
            <slot:description>{{ $studentRecord->school?->name }} · Books currently borrowed</slot:description>
            <slot:content>
                @if ($loans->isEmpty())
                    <p class="text-sm text-muted-foreground">There are no books on loan.</p>
                @else
                    <div class="flex flex-col divide-y">
                        @foreach ($loans as $loan)
                            <div class="flex flex-col gap-1 py-4 first:pt-0 last:pb-0 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="font-medium">{{ $loan->copy?->title?->title }}</p>
                                    <p class="text-sm text-muted-foreground">Copy {{ $loan->copy?->barcode }}</p>
                                </div>
                                <p class="text-sm {{ $loan->daysLate() > 0 ? 'text-destructive' : 'text-muted-foreground' }}">
                                    Due {{ $loan->due_on?->format('j M Y') }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>Library queue</slot:title>
            <slot:description>Titles reserved for this learner.</slot:description>
            <slot:content>
                @if ($reservations->isEmpty())
                    <p class="text-sm text-muted-foreground">There are no active reservations.</p>
                @else
                    <div class="flex flex-col divide-y">
                        @foreach ($reservations as $reservation)
                            <div class="flex flex-col gap-1 py-4 first:pt-0 last:pb-0 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="font-medium">{{ $reservation->title?->title }}</p>
                                    <p class="text-sm text-muted-foreground">{{ $reservation->status->label() }}</p>
                                </div>
                                @if ($reservation->holds_until)
                                    <p class="text-sm text-muted-foreground">Collect by {{ $reservation->holds_until->format('j M Y') }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </slot:content>
        </april:card>
    </div>
@endsection
