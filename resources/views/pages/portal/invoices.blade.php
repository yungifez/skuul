@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('portal.overview'), 'text' => 'My school'],
    ['text' => 'Invoices and payments', 'active'],
]])

@section('title', 'Invoices and payments')
@section('page_heading', 'Invoices and payments')

@section('content')
    <div class="space-y-6">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-foreground md:text-3xl">{{ $studentRecord->user?->name }}</h2>
            <p class="mt-1 text-sm text-muted-foreground">{{ $studentRecord->school?->name }} · Read-only account statement</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <april:card>
                <slot:title>Still owed</slot:title>
                <slot:description>What the school records as due.</slot:description>
                <slot:content><p class="text-2xl font-semibold">{{ number_format($balance, 2) }}</p></slot:content>
            </april:card>
            <april:card>
                <slot:title>Credit held</slot:title>
                <slot:description>Money paid that has not been used on an invoice.</slot:description>
                <slot:content><p class="text-2xl font-semibold">{{ number_format($unappliedCredit, 2) }}</p></slot:content>
            </april:card>
        </div>

        <april:card>
            <slot:title>Invoices</slot:title>
            <slot:description>Every invoice recorded for this learner at this school.</slot:description>
            <slot:content>
                @if ($invoices->isEmpty())
                    <x-empty-state icon="lucide-receipt" title="No invoices yet"
                        description="The school has not recorded an invoice for this learner." />
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="border-b text-muted-foreground">
                                <tr>
                                    <th class="p-4 font-medium">Invoice</th>
                                    <th class="p-4 font-medium">Issued</th>
                                    <th class="p-4 font-medium">Due</th>
                                    <th class="p-4 text-right font-medium">Total</th>
                                    <th class="p-4 text-right font-medium">Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invoices as $invoice)
                                    <tr class="border-b last:border-0">
                                        <td class="p-4 font-medium">{{ $invoice->name }}</td>
                                        <td class="p-4 text-muted-foreground">{{ $invoice->issue_date?->format('j M Y') }}</td>
                                        <td class="p-4 text-muted-foreground">{{ $invoice->due_date?->format('j M Y') }}</td>
                                        <td class="p-4 text-right">{{ $invoice->amount->plus($invoice->fine)->minus($invoice->waiver)->formatToLocale(app()->getLocale()) }}</td>
                                        <td class="p-4 text-right">{{ $invoice->balance->formatToLocale(app()->getLocale()) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </slot:content>
        </april:card>
    </div>
@endsection
