@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('library-copies.index'), 'text' => 'Library', 'active'],
]])

@section('title', __('Library'))

@section('page_heading', __('Library'))

@section('page_actions')
    <april:button-link href="{{ route('library-loans.index') }}" variant="outline" class="h-11 select-none">
        <x-lucide-book-open-check class="mr-2 size-4" />
        Lending desk
    </april:button-link>
    @if ($canManage)
        <april:dropdown-menu>
            <slot:trigger>
                <april:button type="button" variant="ghost" size="icon" class="size-11 select-none" aria-label="More for the library">
                    <x-lucide-ellipsis class="size-4" />
                </april:button>
            </slot:trigger>
            <slot:content align="end">
                <april:dropdown-menu-item href="{{ route('library-reservations.index') }}"><x-lucide-list-ordered class="mr-2 size-4" />Queue</april:dropdown-menu-item>
                <april:dropdown-menu-item href="{{ route('library-rules.edit') }}"><x-lucide-settings-2 class="mr-2 size-4" />Lending rules</april:dropdown-menu-item>
            </slot:content>
        </april:dropdown-menu>
    @endif
@endsection

@section('content')
<div class="mx-auto flex w-full max-w-5xl flex-col gap-6">
    <p class="text-sm text-muted-foreground">
        {{ $onShelf }} on the shelf · {{ $out }} out
        @if ($overdue > 0)
            · <a href="{{ route('library-loans.index') }}" class="font-medium text-destructive underline-offset-4 hover:underline">{{ $overdue }} late</a>
        @endif
    </p>

    @if ($canManage)
        <livewire:library-shelving-form />
    @endif

    <livewire:library-copy-catalog />
</div>
@endsection
