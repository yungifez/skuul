@extends('layouts.print')

@section('title', 'Receipt '.$studentPayment->id)

@section('content')
    <main class="mx-auto max-w-2xl space-y-6 p-8">
        <div class="border-b pb-5"><p class="text-sm text-gray-500">Payment receipt</p><h1 class="text-2xl font-semibold">Receipt #{{ $studentPayment->id }}</h1><p class="text-sm text-gray-500">{{ $studentPayment->received_on?->format('j M Y') }}</p></div>
        <div class="grid gap-4 sm:grid-cols-2"><div><p class="text-sm text-gray-500">Student</p><p class="font-medium">{{ $studentPayment->studentRecord?->user?->name }}</p></div><div><p class="text-sm text-gray-500">Payment method</p><p class="font-medium">{{ $studentPayment->methodLabel() }}</p></div><div><p class="text-sm text-gray-500">Financial period</p><p class="font-medium">{{ $studentPayment->financialPeriod?->name ?: 'Legacy record' }}</p></div><div><p class="text-sm text-gray-500">Reference</p><p class="font-medium">{{ $studentPayment->reference ?: '—' }}</p></div></div>
        <div class="flex items-center justify-between border-y py-5"><span class="text-lg font-medium">Amount received</span><span class="text-2xl font-semibold">{{ $studentPayment->amount->formatToLocale(app()->getLocale()) }}</span></div>
        @if ($studentPayment->allocations->isNotEmpty())
            <div><h2 class="mb-2 font-medium">Applied to</h2><ul class="divide-y border-y">@foreach ($studentPayment->allocations as $allocation)<li class="flex justify-between py-3"><span>{{ $allocation->feeInvoice?->name }}</span><span>{{ $allocation->amount->formatToLocale(app()->getLocale()) }}</span></li>@endforeach</ul></div>
        @endif
        @if ($studentPayment->note)<p class="text-sm text-gray-600">{{ $studentPayment->note }}</p>@endif
    </main>
@endsection
