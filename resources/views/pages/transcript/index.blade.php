@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['text' => 'Transcripts', 'active'],
]])

@section('title', 'Transcripts')
@section('page_heading', 'Transcripts')

@section('content')
    <div class="space-y-6">
        @if ($errors->has('transcript'))
            <april:alert variant="destructive">
                <slot:title>The transcript was not issued</slot:title>
                <slot:description>{{ $errors->first('transcript') }}</slot:description>
            </april:alert>
        @endif

        <livewire:transcript-directory />
    </div>
@endsection
