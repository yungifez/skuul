@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('exams.index'), 'text'=> 'Exams'],
    ['href'=> route('exam-slots.index' ,$exam->id), 'text'=> 'Exam slots', 'active'],
]])

@section('title',  __("Exam Slots In $exam->name"))

@section('page_heading',   __("Exam Slots In $exam->name"))

@section('page_actions')
    <x-resource-create-action :href="route('exam-slots.create', $exam)" ability="create" :arguments="[\App\Models\ExamSlot::class]">Add exam slot</x-resource-create-action>
@endsection

@section('content', )
    @livewire('list-exam-slots-table', ['exam'=> $exam])
@endsection
