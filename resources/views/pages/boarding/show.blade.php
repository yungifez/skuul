@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('dormitories.index'), 'text' => 'Boarding'],
    ['href' => route('dormitories.show', $dormitory->id), 'text' => $dormitory->name, 'active'],
]])

@section('title', $dormitory->name)

@section('page_heading', $dormitory->name)

@section('page_actions')
    @if ($canManage)
        <april:button-link href="{{ route('boarding-rolls.index') }}" variant="ghost">Boarding rolls</april:button-link>
        <april:dropdown-menu>
            <slot:trigger>
                <april:button type="button" variant="outline" size="icon" class="size-11 select-none" aria-label="Actions for {{ $dormitory->name }}">
                    <x-lucide-ellipsis class="size-4" />
                </april:button>
            </slot:trigger>
            <slot:content>
                <april:dropdown-menu-item x-on:click="window.location.href = '{{ route('dormitories.edit', $dormitory->id) }}'">
                    <x-lucide-pencil class="mr-2 size-4" />Edit house
                </april:dropdown-menu-item>
                @if ($dormitory->is_active)
                    <form action="{{ route('dormitories.destroy', $dormitory->id) }}" method="POST" data-confirm="Archive {{ $dormitory->name }}? It stops taking placements.">
                        @csrf
                        @method('DELETE')
                        <april:dropdown-menu-item type="submit">
                            <x-lucide-archive class="mr-2 size-4" />Archive house
                        </april:dropdown-menu-item>
                    </form>
                @endif
            </slot:content>
        </april:dropdown-menu>
    @endif
@endsection

@section('content')
    <x-display-validation-errors />
    @livewire('show-dormitory', ['dormitory' => $dormitory])
@endsection
