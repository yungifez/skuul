@props(['status'])

@php
    use App\Enums\AcademicStructureStatus;

    $variant = match ($status) {
        AcademicStructureStatus::Draft => 'secondary',
        AcademicStructureStatus::Active => 'default',
        AcademicStructureStatus::Archived => 'outline',
    };
@endphp

<april:badge variant="{{ $variant }}">{{ $status->label() }}</april:badge>
