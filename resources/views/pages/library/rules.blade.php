@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('library-copies.index'), 'text' => 'Library'],
    ['href' => route('library-rules.edit'), 'text' => 'Lending rules', 'active'],
]])

@section('title', __('Lending rules'))

@section('page_heading', __('Lending rules'))

@section('content')
<div class="mx-auto flex w-full max-w-2xl flex-col gap-6">
    <livewire:library-lending-rules-form />
</div>
@endsection
