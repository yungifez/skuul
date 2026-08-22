@extends('layouts.app', ['breadcrumbs' => [['href' => route('dashboard'), 'text' => 'Dashboard'], ['href' => route('schools.settings'), 'text' => 'School setup'], ['href' => route('schools.features.edit'), 'text' => 'School features', 'active']]])
@section('title', 'School features')
@section('page_heading', 'Choose the tools your school uses')
@section('content')
<form method="POST" action="{{ route('schools.features.update') }}" class="mx-auto max-w-3xl space-y-4">
    @csrf
    @method('PUT')
    <p class="text-muted-foreground">Turn a tool off to hide it from daily work. Existing records remain safe and available to authorised reporting.</p>
    @foreach (\App\Enums\Feature::cases() as $feature)
        <label class="flex items-center justify-between gap-4 rounded-lg border p-4"><span><span class="block font-medium">{{ $feature->label() }}</span><span class="text-sm text-muted-foreground">{{ $features[$feature->value] ? 'Available to this school' : 'Currently hidden' }}</span></span><input type="hidden" name="features[{{ $feature->value }}]" value="0"><input type="checkbox" name="features[{{ $feature->value }}]" value="1" {{ $features[$feature->value] ? 'checked' : '' }} class="size-4"></label>
    @endforeach
    <april:button type="submit">Save feature choices</april:button>
</form>
@endsection
