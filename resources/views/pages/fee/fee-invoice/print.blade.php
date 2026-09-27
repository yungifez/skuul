@extends('layouts.print')

@php
    $hasLines = $feeInvoice->feeInvoiceRecords->isNotEmpty();
    $isReceipt = $hasLines && $feeInvoice->isSettled();
    $locale = app()->getLocale();
    $dash = '—';
    $section = $feeInvoice->studentRecord?->academicCycleSection;
    $school = current_school();
    $contact = collect([$school->phone, $school->email])->filter()->implode(' · ');
    $payments = $feeInvoice->allocations
        ->groupBy('student_payment_id')
        ->map(fn ($allocations) => [
            'payment' => $allocations->first()->studentPayment,
            'amount' => $allocations->reduce(fn ($sum, $allocation) => $sum === null ? $allocation->amount : $sum->plus($allocation->amount)),
        ])
        ->filter(fn (array $row): bool => $row['payment'] !== null && !$row['payment']->isReversed())
        ->sortBy(fn (array $row) => $row['payment']->received_on)
        ->values();
@endphp

@section('title', ($isReceipt ? 'Receipt · ' : 'Invoice · ').$feeInvoice->name)

@section('back_url', route('fee-invoices.show', $feeInvoice))

@section('content')
    <div class="record">
        <div class="record-title">
            <div>
                <p class="eyebrow">{{ $isReceipt ? 'Receipt' : 'Invoice' }}</p>
                <h2>{{ $feeInvoice->name }}</h2>
                <p class="muted">
                    Issued {{ $feeInvoice->issue_date?->format('j M Y') ?? $dash }}
                    @unless ($isReceipt)
                        · Due {{ $feeInvoice->due_date?->format('j M Y') ?? $dash }}
                    @endunless
                </p>
            </div>
            @if ($isReceipt)
                <div class="stamp" id="paid-stamp">Paid in full</div>
            @else
                <div class="amount-due">
                    <span>Amount due</span>
                    <strong>{{ $feeInvoice->balance->formatToLocale($locale) }}</strong>
                </div>
            @endif
        </div>

        <section>
            <h3>{{ $isReceipt ? 'Received from' : 'Billed to' }}</h3>
            <table class="facts">
                <tr>
                    <th>Student</th><td>{{ $feeInvoice->user?->name ?? $dash }}</td>
                    <th>Admission number</th><td>{{ $feeInvoice->studentRecord?->admission_number ?: $dash }}</td>
                </tr>
                <tr>
                    <th>{{ school_term('class_level', 'Class') }}</th><td>{{ $section?->academicLevel?->name ?? $dash }}</td>
                    <th>{{ school_term('section', 'Section') }}</th><td>{{ $section?->label ?? $section?->name ?? $dash }}</td>
                </tr>
                @if ($contact !== '')
                    <tr>
                        <th>School contact</th><td colspan="3">{{ $contact }}</td>
                    </tr>
                @endif
            </table>
        </section>

        <section>
            <h3>Fees</h3>
            <table class="list">
                <thead>
                    <tr>
                        <th>Fee</th>
                        <th class="num">Amount</th>
                        <th class="num">Waiver</th>
                        <th class="num">Fine</th>
                        <th class="num">Paid</th>
                        <th class="num">Owed</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($feeInvoice->feeInvoiceRecords as $record)
                        <tr>
                            <td>{{ $record->fee?->name ?? $dash }}</td>
                            <td class="num">{{ $record->amount->formatToLocale($locale) }}</td>
                            <td class="num">{{ $record->waiver->isPositive() ? $record->waiver->formatToLocale($locale) : $dash }}</td>
                            <td class="num">{{ $record->fine->isPositive() ? $record->fine->formatToLocale($locale) : $dash }}</td>
                            <td class="num">{{ $record->paid->isPositive() ? $record->paid->formatToLocale($locale) : $dash }}</td>
                            <td class="num">{{ $record->outstanding->formatToLocale($locale) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="muted">No fees are on this invoice yet.</td></tr>
                    @endforelse
                </tbody>
                @if ($hasLines)
                    <tfoot>
                        <tr>
                            <th>Total</th>
                            <th class="num">{{ $feeInvoice->amount->formatToLocale($locale) }}</th>
                            <th class="num">{{ $feeInvoice->waiver->formatToLocale($locale) }}</th>
                            <th class="num">{{ $feeInvoice->fine->formatToLocale($locale) }}</th>
                            <th class="num">{{ $feeInvoice->paid->formatToLocale($locale) }}</th>
                            <th class="num">{{ $feeInvoice->balance->formatToLocale($locale) }}</th>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </section>

        @if ($payments->isNotEmpty())
            <section>
                <h3>Payments received</h3>
                <table class="list">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Receipt</th>
                            <th>Method</th>
                            <th>Reference</th>
                            <th class="num">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $row)
                            <tr>
                                <td class="nowrap">{{ $row['payment']->received_on?->format('j M Y') ?? $dash }}</td>
                                <td>#{{ $row['payment']->id }}</td>
                                <td>{{ $row['payment']->methodLabel() }}</td>
                                <td>{{ $row['payment']->reference ?: $dash }}</td>
                                <td class="num">{{ $row['amount']->formatToLocale($locale) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>
        @endif

        @if (filled($feeInvoice->note))
            <section>
                <h3>Note</h3>
                <p class="note">{{ $feeInvoice->note }}</p>
            </section>
        @endif

        <footer class="record-footer">
            <div class="signature">
                <span></span>
                {{ $isReceipt ? 'Received by' : 'Authorised by' }} · signature and school stamp
            </div>
            <p class="muted">Printed {{ now()->format('j M Y, H:i') }} by {{ auth()->user()->name }}</p>
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
        .stamp { flex-shrink: 0; padding: 0.5rem 1rem; border: 3px solid #15803d; border-radius: 0.35rem; color: #15803d; font-size: 1.1rem; font-weight: 800; letter-spacing: 0.12em; text-transform: uppercase; transform: rotate(-4deg); }
        .amount-due { display: flex; flex-shrink: 0; flex-direction: column; align-items: flex-end; padding: 0.6rem 0.9rem; border: 1px solid #18181b; }
        .amount-due span { font-size: 0.75rem; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; color: #52525b; }
        .amount-due strong { font-size: 1.35rem; font-variant-numeric: tabular-nums; }
        .record section { break-inside: avoid; }
        .record h3 { margin: 0 0 0.5rem; padding-bottom: 0.35rem; border-bottom: 2px solid #18181b; font-size: 0.8rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; }
        .record table { width: 100%; border-collapse: collapse; }
        .record th, .record td { border: 1px solid #d4d4d8; padding: 0.5rem 0.65rem; vertical-align: top; text-align: left; }
        .facts th { width: 18%; background: #f4f4f5; font-weight: 500; color: #52525b; }
        .facts td { width: 32%; }
        .list thead th { background: #f4f4f5; font-size: 0.75rem; font-weight: 600; letter-spacing: 0.04em; text-transform: uppercase; color: #52525b; }
        .list tfoot th { background: #f4f4f5; }
        .list tr { break-inside: avoid; }
        .record .num { text-align: right; white-space: nowrap; font-variant-numeric: tabular-nums; }
        .nowrap { white-space: nowrap; }
        .note { margin: 0; white-space: pre-line; }
        .record-footer { display: flex; justify-content: space-between; align-items: flex-end; gap: 2rem; margin-top: 1.5rem; font-size: 0.8rem; }
        .signature { display: flex; flex-direction: column; gap: 0.35rem; min-width: 240px; color: #52525b; }
        .signature span { display: block; height: 2.5rem; border-bottom: 1px solid #18181b; }
        @media (max-width: 640px) {
            .record-title { flex-direction: column; }
            .amount-due { align-items: flex-start; }
            .facts th, .facts td { display: block; width: auto; }
            .facts tr { display: block; }
            .list { display: block; overflow-x: auto; }
            .record-footer { flex-direction: column; align-items: stretch; }
        }
        @media print {
            * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .record { gap: 1rem; font-size: 12px; }
        }
    </style>
@endsection
