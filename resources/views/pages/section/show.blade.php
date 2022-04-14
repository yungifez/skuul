@extends('adminlte::page')

@section('title', __("View $section->name details"))

@section('content_header')
    <h1 class="font-weight-semibold">
        {{ __("View $section->name details") }}
    </h1>

    @livewire('show-set-school')

    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('sections.index'), 'text'=> 'sections'],
        ['href'=> route('sections.show', $section->id), 'text'=> "View $section->name details", 'active'],
    ]])
@endsection

@section('content')
    @livewire('show-section', ['section' => $section])

    @livewire('display-status')

    
@endsection
