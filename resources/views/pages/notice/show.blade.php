@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('notices.index'), 'text'=> 'Notices'],
    ['href'=> route('notices.show', $notice->id), 'text'=> $notice->title, 'active'],
]])

@section('title', $notice->title)

@section('page_heading', $notice->title)

@section('content')
    @livewire('show-notice', ['notice' => $notice])
@endsection
