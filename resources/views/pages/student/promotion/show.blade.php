@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('students.promotions'), 'text'=> 'promotions'],
    ['href'=> route('students.promotions.show', $promotion->id), 'text'=> "View $promotion->label", 'active'],
]])

@section('title', __("$promotion->label"))

@section('page_heading', __("$promotion->label") )

@section('content')
    @livewire('show-promotion', ['promotion' => $promotion])
@endsection
