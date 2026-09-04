<div class="card">
    <div class="card-header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h4 class="card-title">View {{ $notice->title }}</h4>
            <april:badge variant="{{ $notice->status->value === 'published' ? 'default' : 'secondary' }}">
                {{ $notice->status->label() }}
            </april:badge>
        </div>
    </div>
    <div class="card-body">
        <div class="prose prose-sm max-w-none dark:prose-invert">{!! $content !!}</div>
        <div class="my-6">
            @if($notice->hasManagedAttachment())
                <a class="inline-flex items-center gap-2 rounded bg-blue-600 px-4 py-2 text-white" href="{{ route('notices.attachments.download', $notice) }}">
                    <x-lucide-download class="size-4"  />
                    Download {{ $notice->attachment_name ?? 'attachment' }}
                </a>
            @endif
        </div>
        @if ($notice->status->value !== 'published' && auth()->user()?->can('update', $notice))
            <button
                type="button"
                wire:click="publishNotice"
                wire:loading.attr="disabled"
                class="inline-flex h-10 items-center justify-center rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground hover:bg-primary/90 disabled:opacity-50"
            >
                <span wire:loading.remove wire:target="publishNotice">Publish notice</span>
                <span wire:loading wire:target="publishNotice">Publishing…</span>
            </button>
        @endif
        @error('notice')
            <p class="mt-3 text-sm text-destructive">{{ $message }}</p>
        @enderror
    </div>
</div>
