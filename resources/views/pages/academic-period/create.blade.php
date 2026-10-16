@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('academic-years.show', current_academic_year()), 'text' => current_academic_year()->name],
    ['href' => route('academic-periods.create'), 'text' => 'Add period', 'active'],
]])

@section('title', __('Add reporting period'))
@section('page_heading', __('Add reporting period'))

@section('content')
    <april:card class="mx-auto max-w-3xl">
        <slot:title>Add a reporting period</slot:title>
        <slot:description>Set the dates before the period is scheduled or opened.</slot:description>
        <slot:content>
            <form action="{{ route('academic-periods.store') }}" method="POST" class="space-y-4">
                @csrf
                <x-display-validation-errors />
                <april:input-group id="name" name="name" label="Period name" placeholder="Term 1" value="{{ old('name') }}" required />
                <div class="grid gap-4 md:grid-cols-2">
                    <april:input-group id="starts-on" name="starts_on" type="date" label="Starts on" value="{{ old('starts_on') }}" />
                    <april:input-group id="ends-on" name="ends_on" type="date" label="Ends on" value="{{ old('ends_on') }}" />
                </div>
                <div class="flex w-full flex-col gap-2">
                    <april:label for="type">Period type</april:label>
                    <select id="type" name="type" class="h-10 rounded-md border border-input bg-background px-3 text-sm">
                        @foreach (\App\Enums\AcademicPeriodType::cases() as $type)
                            <option value="{{ $type->value }}" @selected(old('type', \App\Enums\AcademicPeriodType::Term->value) === $type->value)>{{ $type->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-wrap gap-3">
                    <april:button type="submit">Create period</april:button>
                    <april:button-link href="{{ route('academic-years.show', current_academic_year()) }}" variant="outline">Cancel</april:button-link>
                </div>
            </form>
        </slot:content>
    </april:card>
@endsection
