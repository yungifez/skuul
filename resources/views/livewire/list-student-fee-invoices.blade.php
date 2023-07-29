<div class="card">
    <div class="card-header">
        <h2 class="card-title">
            Student Fee Invoices
        </h2>
    </div>
    <div class="card-body">
        @forelse ($student->feeInvoices as $feeInvoice)
            <a class="grid md:grid-cols-5 gap-y-3 my-3 border rounded p-5 items-center hover:bg-white hover:bg-opacity-30 " href="{{route('fee-invoices.show', $feeInvoice->id)}}">
                <p>{{$feeInvoice->name}}</p>
                <p>Amount: {{$feeInvoice->amount}}</p>
                <p>Paid: {{$feeInvoice->paid}}</p>
                <p>Due Date: {{$feeInvoice->due_date->toFormattedDateString()}}</p>
                @if ($feeInvoice->balance->isLessThanOrEqualTo(0))
                <div class=" font-bold text-green-400 capitalize rounded">
                    Paid
                </div>
                @elseif ($feeInvoice->paid->isGreaterThan(0))
                    <div class=" font-bold  text-yellow-400 capitalize rounded">
                        Paid Partially
                    </div>
                @else
                    <div class=" font-bold text-red-400 capitalize rounded">
                        <strong>Not</strong> Paid
                    </div>
                @endif            
            </a>
        @empty
            <p>No Fee Invoice Records</p>
        @endforelse
    </div>
</div>
