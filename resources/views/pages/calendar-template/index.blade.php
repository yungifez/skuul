@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('organizations.index'), 'text' => 'Organizations'],
    ['href' => route('organizations.show', $organization), 'text' => $organization->name],
    ['href' => route('organizations.calendar-templates.index', $organization), 'text' => 'Calendar templates', 'active'],
]])

@section('title', __('Calendar templates'))
@section('page_heading', __('Calendar templates'))
@section('page_actions')
    <april:button-link href="{{ route('organizations.calendar-templates.create', $organization) }}" class="gap-1.5"><x-lucide-plus class="size-4" />Add template</april:button-link>
@endsection

@section('content')
    <april:card>
        <slot:title>{{ school_term('academic_year', 'School year') }} calendar templates</slot:title>
        <slot:description>Define the common shape once, then generate dated {{ strtolower(school_term('academic_year', 'school year')) }}s for each campus. A campus may override the default only deliberately.</slot:description>
        <slot:content>
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($organization->calendarTemplates as $template)
                    <a href="{{ route('organizations.calendar-templates.edit', [$organization, $template]) }}" class="rounded-lg border p-5 transition hover:bg-muted/50">
                        <div class="flex items-start justify-between gap-3"><p class="font-semibold">{{ $template->name }}</p>@if ($template->is_default)<april:badge>Default</april:badge>@endif</div>
                        <p class="mt-1 text-sm text-muted-foreground">{{ $template->periods->count() }} periods · {{ $template->cycle_length_days }} days</p>
                        <p class="mt-3 text-sm text-muted-foreground">{{ $template->auto_open ? 'Opens automatically' : 'Manual opening' }} · {{ $template->generate_ahead_weeks ?: 'No' }} weeks ahead</p>
                    </a>
                @empty
                    <div class="text-muted-foreground">No template exists yet. Start with the school’s actual terms or semesters, then add optional exam windows and holidays.</div>
                @endforelse
            </div>
        </slot:content>
    </april:card>
@endsection
