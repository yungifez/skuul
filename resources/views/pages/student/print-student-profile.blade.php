@extends('layouts.print')

@section('title', $student->name.' · Student record')

@section('back_url', route('students.show', $student))

@section('content')
    @php
        $section = $studentRecord?->academicCycleSection;
        $dash = '—';
        $address = collect([$student->address, $student->address_line_2, $student->city, $student->state, $student->postal_code, $student->country])->filter()->implode(', ');
    @endphp

    <div class="record">
        <div class="record-title">
            <div>
                <p class="eyebrow">Student record</p>
                <h2>{{ $student->name }}</h2>
                <p class="muted">
                    {{ $studentRecord?->admission_number ?: 'No admission number' }}
                    @if ($studentRecord)
                        · {{ $studentRecord->status->label() }}
                    @endif
                </p>
            </div>
            <img src="{{ $student->profile_photo_url }}" alt="" class="photo">
        </div>

        <section>
            <h3>Personal details</h3>
            <table class="facts">
                <tr>
                    <th>Full name</th><td>{{ $student->name }}</td>
                    <th>Gender</th><td>{{ $student->gender ? ucfirst($student->gender) : $dash }}</td>
                </tr>
                <tr>
                    <th>Date of birth</th><td>{{ $student->birthday ? \Illuminate\Support\Carbon::parse($student->birthday)->format('j M Y') : $dash }}</td>
                    <th>Nationality</th><td>{{ $student->nationality ? ucfirst($student->nationality) : $dash }}</td>
                </tr>
                <tr>
                    <th>Email</th><td>{{ $student->email ?: $dash }}</td>
                    <th>Phone</th><td>{{ $student->phone ?: $dash }}</td>
                </tr>
                <tr>
                    <th>Address</th><td colspan="3">{{ $address ?: $dash }}</td>
                </tr>
            </table>
        </section>

        <section>
            <h3>Enrollment</h3>
            @if ($studentRecord)
                <table class="facts">
                    <tr>
                        <th>Admission number</th><td>{{ $studentRecord->admission_number ?: $dash }}</td>
                        <th>Admitted</th><td>{{ $studentRecord->admission_date?->format('j M Y') ?? $dash }}</td>
                    </tr>
                    <tr>
                        <th>{{ school_term('class_level', 'Class') }}</th><td>{{ $section?->academicLevel?->name ?? $dash }}</td>
                        <th>{{ school_term('section', 'Section') }}</th><td>{{ $section?->label ?? $section?->name ?? $dash }}</td>
                    </tr>
                    <tr>
                        <th>Status</th><td colspan="3">{{ $studentRecord->status->label() }}</td>
                    </tr>
                </table>
            @else
                <p class="muted">Not enrolled in this school.</p>
            @endif
        </section>

        <section>
            <h3>Parents and guardians</h3>
            @if ($guardians->isEmpty())
                <p class="muted">None recorded.</p>
            @else
                <table class="list">
                    <thead><tr><th>Name</th><th>Phone</th><th>Email</th></tr></thead>
                    <tbody>
                        @foreach ($guardians as $guardian)
                            <tr>
                                <td>{{ $guardian->user?->name ?? $dash }}</td>
                                <td>{{ $guardian->user?->phone ?: $dash }}</td>
                                <td>{{ $guardian->user?->email ?: $dash }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>

        @if ($studentRecord)
            <section>
                <h3>Placement history</h3>
                @if ($studentRecord->placements->isEmpty())
                    <p class="muted">None recorded.</p>
                @else
                    <table class="list">
                        <thead><tr><th>{{ school_term('academic_year', 'School year') }}</th><th>{{ school_term('class_level', 'Class') }}</th><th>{{ school_term('section', 'Section') }}</th><th>From</th></tr></thead>
                        <tbody>
                            @foreach ($studentRecord->placements->sortByDesc('effective_on') as $placement)
                                <tr>
                                    <td>{{ $placement->academicYear?->name ?? $dash }}@if ($placement->academicPeriod) · {{ $placement->academicPeriod->name }}@endif</td>
                                    <td>{{ $placement->academicCycleSection?->academicLevel?->name ?? $dash }}</td>
                                    <td>{{ $placement->academicCycleSection?->label ?? $placement->academicCycleSection?->name ?? $dash }}</td>
                                    <td class="nowrap">{{ $placement->effective_on?->format('j M Y') ?? $dash }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </section>

            <section>
                <h3>Status history</h3>
                @if ($studentRecord->statusChanges->isEmpty())
                    <p class="muted">No changes.</p>
                @else
                    <table class="list">
                        <thead><tr><th>Change</th><th>Reason</th><th>By</th><th>From</th></tr></thead>
                        <tbody>
                            @foreach ($studentRecord->statusChanges->sortByDesc('effective_on') as $change)
                                <tr>
                                    <td class="nowrap">{{ $change->from_status->label() }} → {{ $change->to_status->label() }}</td>
                                    <td>{{ $change->reason ?: $dash }}</td>
                                    <td>{{ $change->changedBy?->name ?? $dash }}</td>
                                    <td class="nowrap">{{ $change->effective_on?->format('j M Y') ?? $dash }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </section>
        @endif

        <footer class="record-footer">
            <div class="signature">
                <span></span>
                Signature and school stamp
            </div>
            <p class="muted">Printed {{ school_now()->format('j M Y, H:i') }} by {{ auth()->user()->name }}</p>
        </footer>
    </div>
@endsection

@section('style')
    <style>
        .record { display: flex; flex-direction: column; gap: 1.5rem; color: #18181b; }
        .record-title { display: flex; justify-content: space-between; align-items: flex-start; gap: 1.5rem; }
        .record-title h2 { margin: 0.15rem 0; font-size: 1.6rem; font-weight: 600; letter-spacing: -0.01em; }
        .eyebrow { margin: 0; font-size: 0.75rem; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; color: #71717a; }
        .muted { margin: 0; color: #71717a; }
        .photo { width: 96px; height: 112px; flex-shrink: 0; border: 1px solid #d4d4d8; border-radius: 0.25rem; object-fit: cover; }
        .record section { break-inside: avoid; }
        .record h3 { margin: 0 0 0.5rem; padding-bottom: 0.35rem; border-bottom: 2px solid #18181b; font-size: 0.8rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; }
        .record table { width: 100%; border-collapse: collapse; }
        .record th, .record td { border: 1px solid #d4d4d8; padding: 0.5rem 0.65rem; vertical-align: top; text-align: left; }
        .facts th { width: 18%; background: #f4f4f5; font-weight: 500; color: #52525b; }
        .facts td { width: 32%; }
        .list thead th { background: #f4f4f5; font-size: 0.75rem; font-weight: 600; letter-spacing: 0.04em; text-transform: uppercase; color: #52525b; }
        .list tr { break-inside: avoid; }
        .nowrap { white-space: nowrap; }
        .record-footer { display: flex; justify-content: space-between; align-items: flex-end; gap: 2rem; margin-top: 1.5rem; font-size: 0.8rem; }
        .signature { display: flex; flex-direction: column; gap: 0.35rem; min-width: 240px; color: #52525b; }
        .signature span { display: block; height: 2.5rem; border-bottom: 1px solid #18181b; }
        @media (max-width: 640px) {
            .facts th, .facts td { display: block; width: auto; }
            .facts tr { display: block; }
            .record-footer { flex-direction: column; align-items: stretch; }
        }
        @media print {
            * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .record { gap: 1rem; font-size: 12px; }
        }
    </style>
@endsection
