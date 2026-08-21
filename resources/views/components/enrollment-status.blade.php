@props(['enrollment'])

@php
    use App\Enums\EnrollmentStatus;

    $status = $enrollment?->status;
    $variant = match ($status) {
        EnrollmentStatus::Active => 'default',
        EnrollmentStatus::Suspended => 'secondary',
        EnrollmentStatus::Withdrawn => 'destructive',
        EnrollmentStatus::Transferred, EnrollmentStatus::Graduated, EnrollmentStatus::Archived => 'outline',
        default => 'outline',
    };
@endphp

@if ($status !== null)
    <april:badge variant="{{ $variant }}">{{ $status->label() }}</april:badge>
@else
    <span class="text-sm text-muted-foreground">No enrollment</span>
@endif
