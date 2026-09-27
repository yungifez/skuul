@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('course-offerings.index'), 'text' => 'Course offerings'],
    ['href' => route('course-offerings.edit', $courseOffering), 'text' => 'Edit roster', 'active'],
]])

@section('title', 'Edit roster')
@section('page_heading', 'Edit roster')

@section('content')
    <livewire:edit-course-offering-roster :course-offering="$courseOffering" :setup="request()->boolean('setup')" />
@endsection
