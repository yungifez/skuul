@props([
    'status',
    'action',
    'canUpdate' => false,
    'archiveNote' => 'Archiving hides the record from new work. Everything already recorded against it stays readable.',
])

@php
    use App\Enums\AcademicStructureStatus;

    $canActivate = $canUpdate && $status->canMoveTo(AcademicStructureStatus::Active);
    $canArchive = $canUpdate && $status->canMoveTo(AcademicStructureStatus::Archived);
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-2']) }}>
    <x-academic-structure-status :status="$status" />

    @if ($canActivate)
        <form method="POST" action="{{ $action }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="{{ AcademicStructureStatus::Active->value }}">
            <april:button type="submit" size="sm">
                <x-lucide-check class="mr-1.5 size-3.5" />
                Activate
            </april:button>
        </form>
    @endif

    @if ($canArchive)
        <details class="rounded-md border">
            <summary class="cursor-pointer px-3 py-1.5 text-sm font-medium">Archive</summary>
            <form method="POST" action="{{ $action }}" class="flex flex-col gap-2 border-t p-3">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="{{ AcademicStructureStatus::Archived->value }}">
                <p class="max-w-xs text-xs text-muted-foreground">{{ $archiveNote }}</p>
                <april:button type="submit" variant="outline" size="sm">
                    <x-lucide-archive class="mr-1.5 size-3.5" />
                    Confirm archive
                </april:button>
            </form>
        </details>
    @endif
</div>
