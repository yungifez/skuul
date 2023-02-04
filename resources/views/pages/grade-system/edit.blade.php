@extends('layouts.app', ['breadcrumbs' => [
	['href'=> route('dashboard'), 'text'=> 'Dashboard'],
	['href'=> route('grade-systems.index'), 'text'=> 'Grade systems' , ],
	['href'=> route('grade-systems.edit', $gradeSystem->id), 'text'=> "Edit $gradeSystem->name" , 'active']
]])
@section('title', __("Edit $gradeSystem->name"))

@section('page_heading', __("Edit $gradeSystem->name"))

@section('content')
	@livewire('edit-grade-system-form', ['grade' => $gradeSystem])
@endsection
