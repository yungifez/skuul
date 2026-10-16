<div class="card">
    <div class="card-header">
        <div class="flex flex-wrap items-center gap-x-3 gap-y-2 text-sm text-muted-foreground">
            <april:badge variant="{{ $notice->status->value === 'published' ? 'default' : 'secondary' }}">
                {{ $notice->status->label() }}
            </april:badge>
            <span>Shows {{ \Illuminate\Support\Carbon::parse($notice->start_date)->format('j M Y') }} to {{ \Illuminate\Support\Carbon::parse($notice->stop_date)->format('j M Y') }}</span>
            @if ($audience !== null)
                <span>For {{ $audience }}</span>
                @if ($notice->send_email)
                    <span>Also sent by email</span>
                @endif
            @endif
        </div>
    </div>
    <div class="card-body">
        <div class="rich-text max-w-none text-sm">{!! $content !!}</div>
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
        <x-field-error name="notice" class="mt-3" />
    </div>
</div>
