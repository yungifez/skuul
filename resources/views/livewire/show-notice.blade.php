<div class="card">
    <div class="card-header">
        <h4 class="card-title">View {{$notice->title}}</h4>
    </div>
    <div class="card-body">
        <p class="mb-3 text-base">
            {{$notice->content}}
        </p>
        <div class="my-6">
            @if($notice->hasManagedAttachment())
                <a class="inline-flex items-center gap-2 rounded bg-blue-600 px-4 py-2 text-white" href="{{ route('notices.attachments.download', $notice) }}">
                    <x-lucide-download class="size-4"  />
                    Download {{ $notice->attachment_name ?? 'attachment' }}
                </a>
            @endif
        </div>
    </div>
</div>
