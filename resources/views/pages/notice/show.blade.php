@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('notices.index'), 'text'=> 'notices'],
    ['href'=> route('notices.show', $notice->id), 'text'=> "View $notice->title", 'active'],
]])

@section('title', __("View $notice->title"))

@section('page_heading', __("View $notice->title") )

@section('content')
    @livewire('show-notice', ['notice' => $notice])
@endsection
