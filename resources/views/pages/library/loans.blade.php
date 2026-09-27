@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('library-copies.index'), 'text' => 'Library'],
    ['href' => route('library-loans.index'), 'text' => 'Lending desk', 'active'],
]])

@section('title', __('Lending desk'))

@section('page_heading', __('Lending desk'))

@section('content')
<div class="mx-auto flex w-full max-w-4xl flex-col gap-6">
    <livewire:library-lending-desk />
</div>
@endsection
