@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('academic-cycle-sections.index'), 'text' => school_terms('section', 'Sections')],
    ['href' => route('academic-cycle-sections.create'), 'text' => 'Add', 'active'],
]])

@section('title', __('Add '.strtolower(school_term('section', 'section'))))
@section('page_heading', __('Add '.strtolower(school_term('section', 'section'))))

@section('content')
    <april:card class="mx-auto max-w-3xl">
        <slot:title>Add one {{ strtolower(school_term('section', 'section')) }} for one {{ strtolower(school_term('academic_year', 'school year')) }}</slot:title>
        <slot:description>Create the group used for this school year.</slot:description>
        <slot:content>
            @if ($academicLevels->isEmpty())
                <x-empty-state
                    icon="lucide-graduation-cap"
                    title="Add a {{ strtolower(school_term('class_level', 'class')) }} first"
                    description="A {{ strtolower(school_term('section', 'section')) }} always sits inside a {{ strtolower(school_term('class_level', 'class')) }}, such as Primary 4. Create the {{ strtolower(school_term('class_level', 'class')) }} once, then reuse it every year.">
                    <x-resource-create-action :href="route('academic-levels.create')" ability="create" :arguments="[\App\Models\AcademicLevel::class]">Add {{ strtolower(school_term('class_level', 'class')) }}</x-resource-create-action>
                </x-empty-state>
            @elseif ($academicYears->isEmpty())
                <x-empty-state
                    icon="lucide-calendar"
                    title="Add a {{ strtolower(school_term('academic_year', 'school year')) }} first"
                    description="A {{ strtolower(school_term('section', 'section')) }} serves one exact {{ strtolower(school_term('academic_year', 'school year')) }}, so the year has to exist before the {{ strtolower(school_term('section', 'section')) }} does.">
                    <april:button-link href="{{ route('academic-years.index') }}" variant="outline">Go to academic years</april:button-link>
                </x-empty-state>
            @else
                <x-academic-cycle-section-form
                    :action="route('academic-cycle-sections.store', request()->boolean('setup') ? ['setup' => 1] : [])"
                    :academic-years="$academicYears"
                    :academic-levels="$academicLevels"
                    :teachers="$teachers"
                    :preselected-academic-year-id="$preselectedAcademicYearId"
                    :preselected-academic-level-id="$preselectedAcademicLevelId"
                    submit-label="Create draft section"
                    :cancel-href="route('academic-cycle-sections.index')" />
            @endif
        </slot:content>
    </april:card>
@endsection
