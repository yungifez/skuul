@extends('adminlte::page')

@section('title', __("$promotion->label"))

@section('content_header')
    <h1 class="font-weight-semibold">
        {{ __("$promotion->label") }}
    </h1>

    @livewire('show-set-school')

    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('students.promotions'), 'text'=> 'promotions'],
        ['href'=> route('students.promotions.show', $promotion->id), 'text'=> "View $promotion->label", 'active'],
    ]])
@endsection

@section('content')

    @livewire('show-promotion', ['promotion' => $promotion])
    @livewire('display-status')

    
@endsection
