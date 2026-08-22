@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('academic-cycle-sections.index'), 'text' => 'Cycle sections'],
    ['href' => route('academic-cycle-sections.create'), 'text' => 'Add', 'active'],
]])

@section('title', __('Add cycle section'))
@section('page_heading', __('Add cycle section'))

@section('content')
    <april:card class="mx-auto max-w-3xl">
        <slot:title>Add one home section for one cycle</slot:title>
        <slot:description>
            For example, Primary 4 · Green · 2026–2027. Fill in the first two steps and save. Everything else can wait.
        </slot:description>
        <slot:content>
            @if ($academicLevels->isEmpty())
                <x-empty-state
                    icon="lucide-graduation-cap"
                    title="Add an academic level first"
                    description="A cycle section always sits inside a level, such as Primary 4. Create the level once, then reuse it every cycle.">
                    <x-resource-create-action :href="route('academic-levels.create')" ability="create" :arguments="[\App\Models\AcademicLevel::class]">Add academic level</x-resource-create-action>
                </x-empty-state>
            @elseif ($academicYears->isEmpty())
                <x-empty-state
                    icon="lucide-calendar"
                    title="Add an academic cycle first"
                    description="A section serves one exact academic cycle, so the cycle has to exist before the section does.">
                    <april:button-link href="{{ route('academic-years.index') }}" variant="outline">Go to academic years</april:button-link>
                </x-empty-state>
            @else
                <x-academic-cycle-section-form
                    :action="route('academic-cycle-sections.store')"
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
