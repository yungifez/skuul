@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('health-records.index'), 'text' => 'Health records'],
    ['text' => $enrollment->user?->name ?? $enrollment->admission_number, 'active'],
]])

@section('title', 'Health record')
@section('page_heading', $enrollment->user?->name ?? $enrollment->admission_number)

@section('page_actions')
    <april:button-link href="{{ route('health-records.index') }}" variant="outline">
        <x-lucide-arrow-left class="mr-2 size-4" />
        Back to health records
    </april:button-link>
@endsection

@php
    $canWrite = auth()->user()->can('create', App\Models\StudentHealthRecord::class);
    $fields = [
        ['name' => 'blood_group', 'label' => 'Blood group', 'hint' => 'Leave empty when the school has not been told'],
        ['name' => 'emergency_contact_name', 'label' => 'Emergency contact', 'hint' => 'The person to call first'],
        ['name' => 'emergency_contact_phone', 'label' => 'Contact number', 'hint' => null],
        ['name' => 'emergency_contact_relationship', 'label' => 'Relation to the child', 'hint' => null],
    ];
    $notes = [
        ['name' => 'conditions', 'label' => 'Conditions', 'hint' => 'Anything a first aider must know'],
        ['name' => 'allergies', 'label' => 'Allergies', 'hint' => 'Say what happens and what to do'],
        ['name' => 'medications', 'label' => 'Medication', 'hint' => 'What the child takes, and when'],
        ['name' => 'dietary_needs', 'label' => 'Food the child cannot eat', 'hint' => null],
        ['name' => 'notes', 'label' => 'Anything else', 'hint' => null],
    ];
@endphp

@section('content')
    <div class="space-y-6">
        @if ($errors->any())
            <april:alert variant="destructive">
                <slot:title>The health record was not saved</slot:title>
                <slot:description>{{ $errors->first() }}</slot:description>
            </april:alert>
        @endif

        <april:alert>
            <slot:icon><x-lucide-lock class="size-4" /></slot:icon>
            <slot:title>This page is not part of the student profile</slot:title>
            <slot:description>
                Every change is written to the audit log with your name. The log records which fields changed, never
                what they now say.
            </slot:description>
        </april:alert>

        <form method="POST" action="{{ route('health-records.update', $enrollment) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <april:card>
                <slot:title>In an emergency</slot:title>
                <slot:description>
                    {{ $enrollment->admission_number }} ·
                    @if ($record === null)
                        the school holds nothing for this child yet
                    @else
                        last saved {{ $record->updated_at->format('j M Y') }}
                        by {{ $record->updatedBy?->name ?? 'an unknown person' }}
                    @endif
                </slot:description>
                <slot:content>
                    <div class="grid gap-4 lg:grid-cols-2">
                        @foreach ($fields as $field)
                            <div class="flex flex-col gap-2">
                                <april:label for="{{ $field['name'] }}">{{ $field['label'] }}</april:label>
                                <input type="text" id="{{ $field['name'] }}" name="{{ $field['name'] }}"
                                    value="{{ old($field['name'], $record?->{$field['name']}) }}"
                                    @disabled(!$canWrite)
                                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:opacity-60">
                                @if ($field['hint'] !== null)
                                    <p class="text-xs text-muted-foreground">{{ $field['hint'] }}</p>
                                @endif
                                @error($field['name']) <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                            </div>
                        @endforeach
                    </div>
                </slot:content>
            </april:card>

            <april:card>
                <slot:title>What the school must know</slot:title>
                <slot:description>Write only what the school needs to keep the child safe.</slot:description>
                <slot:content>
                    <div class="grid gap-4 lg:grid-cols-2">
                        @foreach ($notes as $note)
                            <div class="flex flex-col gap-2">
                                <april:label for="{{ $note['name'] }}">{{ $note['label'] }}</april:label>
                                <textarea id="{{ $note['name'] }}" name="{{ $note['name'] }}" rows="3"
                                    @disabled(!$canWrite)
                                    class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:opacity-60">{{ old($note['name'], $record?->{$note['name']}) }}</textarea>
                                @if ($note['hint'] !== null)
                                    <p class="text-xs text-muted-foreground">{{ $note['hint'] }}</p>
                                @endif
                                @error($note['name']) <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                            </div>
                        @endforeach
                    </div>
                </slot:content>
            </april:card>

            @if ($canWrite)
                <div class="flex flex-wrap gap-3">
                    <april:button type="submit">
                        <x-lucide-save class="mr-2 size-4" />
                        Save the record
                    </april:button>
                    <april:button-link href="{{ route('health-records.index') }}" variant="outline">Cancel</april:button-link>
                </div>
            @else
                <p class="text-sm text-muted-foreground">You may read this record but not change it.</p>
            @endif
        </form>
    </div>
@endsection
