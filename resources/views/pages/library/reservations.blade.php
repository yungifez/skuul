@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('library-copies.index'), 'text' => 'Library'],
    ['href' => route('library-reservations.index'), 'text' => 'Queue', 'active'],
]])

@section('title', __('Library queue'))

@section('page_heading', __('Library queue'))

@section('content')
<div class="mx-auto flex w-full max-w-4xl flex-col gap-6">
    <p class="text-sm text-muted-foreground">
        A reservation is for a title, not one copy. The first copy back is kept for whoever waited longest, then goes to the next person when the hold runs out.
    </p>
    <livewire:library-reservation-queue />
</div>
@endsection
