@props(['status'])

@php
    use App\Enums\AcademicPeriodStatus;

    $variant = match ($status) {
        AcademicPeriodStatus::Draft, AcademicPeriodStatus::Scheduled => 'secondary',
        AcademicPeriodStatus::Open => 'default',
        AcademicPeriodStatus::Closing, AcademicPeriodStatus::Closed, AcademicPeriodStatus::Archived => 'outline',
    };
@endphp

<april:badge variant="{{ $variant }}">{{ $status->label() }}</april:badge>
