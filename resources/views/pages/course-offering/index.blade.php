@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['text' => school_terms('course', 'Course').' being taught', 'active'],
]])

@section('title', school_terms('course', 'Course').' being taught')
@section('page_heading', school_terms('course', 'Course').' being taught')

@section('page_actions')
    <div class="flex items-center gap-2">
        @can('create', \App\Models\CourseOffering::class)
            <april:button-link href="{{ route('course-offerings.create') }}" class="h-11 select-none">
                <x-lucide-plus class="mr-2 size-4" />Add {{ strtolower(school_term('course', 'subject')) }}
            </april:button-link>
        @endcan
        @can('viewAny', \App\Models\CourseOffering::class)
            <april:dropdown-menu>
                <slot:trigger>
                    <april:button type="button" variant="ghost" size="icon" class="size-11 select-none" aria-label="More actions">
                        <x-lucide-ellipsis class="size-4" />
                    </april:button>
                </slot:trigger>
                <slot:content align="end" class="w-60">
                    <april:dropdown-menu-item href="{{ route('course-offerings.bulk-create') }}"><x-lucide-list-checks class="mr-2 size-4" />Subject setup</april:dropdown-menu-item>
                    @can('create', \App\Models\CourseOffering::class)
                        <april:dropdown-menu-item href="{{ route('course-offerings.bulk-create.form') }}"><x-lucide-layers class="mr-2 size-4" />Set up across levels</april:dropdown-menu-item>
                        <april:dropdown-menu-item href="{{ route('course-offerings.roll-forward.show') }}"><x-lucide-copy class="mr-2 size-4" />Roll over subjects</april:dropdown-menu-item>
                    @endcan
                </slot:content>
            </april:dropdown-menu>
        @endcan
    </div>
@endsection

@section('content')
    <div class="mx-auto w-full max-w-4xl">
        <livewire:course-offering-directory />
    </div>
@endsection
