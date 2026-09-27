@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('syllabi.index'), 'text'=> 'Syllabi'],
    ['href'=> route('syllabi.show', $syllabus), 'text'=> $syllabus->name],
    ['href'=> route('syllabi.edit', $syllabus), 'text'=> 'Edit draft', 'active'],
]])

@section('title', __('Edit :name', ['name' => $syllabus->name]))

@section('page_heading', __('Edit :name', ['name' => $syllabus->name]))

@section('page_actions')
    <april:button-link href="{{ route('syllabi.show', $syllabus) }}" variant="outline">Preview</april:button-link>
    <form method="POST" action="{{ route('syllabi.publish', $syllabus) }}">
        @csrf
        <april:button type="submit">{{ $syllabus->revision_of_id === null ? 'Publish syllabus' : 'Publish revision '.$syllabus->revision }}</april:button>
    </form>
@endsection

@section('content')
    <div class="space-y-4">
        <april:card>
            <slot:title>Draft details</slot:title>
            <slot:description>
                {{ $syllabus->courseOffering->subject->name }} · {{ $syllabus->courseOffering->academicLevel->name }} · {{ $syllabus->courseOffering->academicPeriod->label ?? $syllabus->courseOffering->academicPeriod->name }}
                · Revision {{ $syllabus->revision }}
            </slot:description>
            <slot:content>
                @if ($syllabus->revisionOf !== null)
                    <p class="mb-4 rounded-md border bg-muted/40 p-3 text-sm">
                        Revises <a class="font-medium underline" href="{{ route('syllabi.show', $syllabus->revisionOf) }}">revision {{ $syllabus->revisionOf->revision }}</a>.
                        Students keep seeing that revision until this one is published.
                        @if ($syllabus->change_note)
                            <span class="mt-1 block text-muted-foreground">Reason: {{ $syllabus->change_note }}</span>
                        @endif
                    </p>
                @endif
                <form action="{{ route('syllabi.update', $syllabus) }}" method="POST" enctype="multipart/form-data" class="space-y-4 md:w-1/2">
                    @csrf
                    @method('PUT')
                    <x-display-validation-errors/>
                    <april:input-group id="name" name="name" label="Name *" value="{{ old('name', $syllabus->name) }}" />
                    <div class="flex w-full flex-col gap-2">
                        <april:label for="description">Overview</april:label>
                        <textarea id="description" name="description" rows="4" placeholder="What the course covers and how it is assessed (optional)"
                            class="flex min-h-[80px] rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            {{ field_error_bindings('description') }}>{{ old('description', $syllabus->description) }}</textarea>
                        <x-field-error name="description" />
                    </div>
                    <div class="flex w-full flex-col gap-2">
                        <april:input-group id="file" type="file" name="file" accept="application/pdf" label="{{ $syllabus->file ? 'Replace the attached PDF' : 'Attach a PDF (optional)' }}" />
                        @if ($syllabus->file)
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" name="remove_file" value="1" class="rounded border-input">
                                Remove the attached PDF
                            </label>
                        @endif
                    </div>
                    <april:button type="submit">Save details</april:button>
                </form>
            </slot:content>
        </april:card>

        @livewire('syllabus-topics-editor', ['syllabus' => $syllabus])

        @can('delete', $syllabus)
            <form method="POST" action="{{ route('syllabi.destroy', $syllabus) }}" data-confirm="Delete the draft {{ $syllabus->name }}?{{ $syllabus->revision_of_id !== null ? ' The published revision stays as it is.' : '' }}">
                @csrf
                @method('DELETE')
                <april:button type="submit" variant="ghost" class="text-destructive">Delete this draft</april:button>
            </form>
        @endcan
    </div>
@endsection
