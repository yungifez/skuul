@extends('layouts.app', ['breadcrumbs' => [['href' => route('dashboard'), 'text' => 'Dashboard'], ['text' => 'Notices', 'active']]])
@section('title', 'Notices')
@section('page_heading', 'Notices')
@section('content')
<div class="flex flex-col gap-4">
    @forelse($notices as $recipient)
        <april:card>
            <slot:title>{{ $recipient->notice->title }}</slot:title>
            <slot:description>Sent {{ $recipient->delivered_at?->diffForHumans() ?? 'recently' }}</slot:description>
            <slot:content>
                <div class="flex flex-col gap-3">
                    <div class="prose prose-sm max-w-none text-slate-700 dark:prose-invert dark:text-slate-200">{!! $recipient->notice->safe_content !!}</div>
                    @if($recipient->notice->hasManagedAttachment())
                        <april:button-link href="{{ route('notices.attachments.download', $recipient->notice) }}" variant="secondary" class="w-fit gap-2"><x-lucide-download class="size-4" />Download attachment</april:button-link>
                    @endif
                </div>
            </slot:content>
        </april:card>
    @empty
        <april:card><slot:title>No notices yet</slot:title><slot:description>Messages sent to this student will appear here.</slot:description></april:card>
    @endforelse
</div>
@endsection
