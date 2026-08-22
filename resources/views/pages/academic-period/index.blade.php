@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('academic-periods.index'), 'text'=> school_terms('period', 'Academic periods'), 'active'],
]])

@section('title', school_terms('period', 'Academic periods'))

@section('page_heading', school_terms('period', 'Academic periods'))

@section('content')
    <div class="space-y-6">
        <april:card>
            <slot:title>Working {{ school_term('period', 'academic period') }}</slot:title>
            <slot:description>Select the period staff are working in. This does not change historical records.</slot:description>
            <slot:content>@livewire('set-academic-period')</slot:content>
        </april:card>

        @can('create', \App\Models\AcademicPeriod::class)
            @livewire('create-academic-period-form')
        @endcan

        @livewire('list-academic-periods-table')
    </div>
@endsection
