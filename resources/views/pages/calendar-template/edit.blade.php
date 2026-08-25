@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('organizations.index'), 'text' => 'Organizations'],
    ['href' => route('organizations.show', $organization), 'text' => $organization->name],
    ['href' => route('organizations.calendar-templates.index', $organization), 'text' => 'Calendar templates'],
    ['href' => route('organizations.calendar-templates.edit', [$organization, $calendarTemplate]), 'text' => $calendarTemplate->name, 'active'],
]])

@section('title', $calendarTemplate->name)
@section('page_heading', $calendarTemplate->name)

@section('content')
    <div class="space-y-6">
        <april:card>
            <slot:title>Template definition</slot:title>
            <slot:description>Changes shape future generated school years. Existing school years keep their own dated records.</slot:description>
            <slot:content><x-calendar-template-form :organization="$organization" :calendar-template="$calendarTemplate" /></slot:content>
        </april:card>

        <april:card>
            <slot:title>Generate a campus school year</slot:title>
            <slot:description>The generated school year starts as draft. Staff must review and schedule it before it opens.</slot:description>
            <slot:content>
                <form method="POST" action="{{ route('organizations.calendar-templates.cycles.store', [$organization, $calendarTemplate]) }}" class="grid gap-4 md:grid-cols-3 md:items-end">
                    @csrf
                    <div class="flex flex-col gap-2"><april:label for="school-id">Campus</april:label><select name="school_id" id="school-id" class="rounded-md border border-input bg-background px-3 py-2" required>@foreach ($organization->schools as $school)<option value="{{ $school->id }}">{{ $school->name }}</option>@endforeach</select></div>
                    <april:input-group name="starts_on" id="starts-on" type="date" label="School year starts on *" required />
                    <april:button type="submit">Generate draft school year</april:button>
                </form>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>Campus adoption</slot:title>
            <slot:description>Choose this template for a campus only when it intentionally differs from the organization default. State why for the audit trail.</slot:description>
            <slot:content class="space-y-3">
                @foreach ($organization->schools as $school)
                    <div class="flex flex-col gap-3 rounded-lg border p-4 lg:flex-row lg:items-end lg:justify-between">
                        <div><p class="font-medium">{{ $school->name }}</p><p class="text-sm text-muted-foreground">{{ $school->calendar_template_id === $calendarTemplate->id ? 'Uses this template' : ($school->calendar_template_id ? 'Uses another campus override' : 'Inherits the organization default') }}</p></div>
                        @if ($school->calendar_template_id === $calendarTemplate->id)
                            <form method="POST" action="{{ route('organizations.calendar-templates.campuses.inherit', [$organization, $calendarTemplate, $school]) }}" class="flex flex-wrap items-end gap-2">@csrf @method('DELETE')<april:input-group name="reason" id="inherit-reason-{{ $school->id }}" label="Reason *" required /><april:button type="submit" variant="outline">Return to default</april:button></form>
                        @else
                            <form method="POST" action="{{ route('organizations.calendar-templates.campuses.override', [$organization, $calendarTemplate, $school]) }}" class="flex flex-wrap items-end gap-2">@csrf <april:input-group name="reason" id="override-reason-{{ $school->id }}" label="Reason *" required /><april:button type="submit" variant="outline">Use this template</april:button></form>
                        @endif
                    </div>
                @endforeach
            </slot:content>
        </april:card>
    </div>
@endsection
