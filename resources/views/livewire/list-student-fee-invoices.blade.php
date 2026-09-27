<section aria-labelledby="fee-invoices-heading" class="flex flex-col gap-3">
    <h2 id="fee-invoices-heading" class="text-base font-semibold">Fee invoices</h2>
    @if ($feeInvoices->isEmpty())
        <p class="text-sm text-muted-foreground">No invoices</p>
    @else
        <ul class="divide-y border-y">
            @foreach ($feeInvoices as $feeInvoice)
                @php
                    $state = match (true) {
                        $feeInvoice->balance->isLessThanOrEqualTo(0) => 'Paid',
                        $feeInvoice->paid->isGreaterThan(0) => 'Part paid',
                        default => 'Unpaid',
                    };
                @endphp
                <li wire:key="fee-invoice-{{ $feeInvoice->id }}">
                    <a href="{{ route('fee-invoices.show', $feeInvoice->id) }}" class="flex min-h-11 flex-wrap items-baseline justify-between gap-x-4 gap-y-1 py-3 text-sm hover:bg-muted/50">
                        <span class="min-w-0">
                            <span class="font-medium">{{ $feeInvoice->name }}</span>
                            <span class="block text-muted-foreground">Due {{ $feeInvoice->due_date->format('j M Y') }}</span>
                        </span>
                        <span class="text-right">
                            <span class="font-medium tabular-nums">{{ $feeInvoice->paid }} / {{ $feeInvoice->amount }}</span>
                            <span @class(['block', 'text-muted-foreground' => $state === 'Paid', 'text-destructive' => $state === 'Unpaid'])>{{ $state }}</span>
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</section>
