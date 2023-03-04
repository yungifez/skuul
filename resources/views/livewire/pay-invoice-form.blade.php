<div class="card">
    <div class="card-header">
        <h2 class="card-title">{{$feeInvoice->name}}</h2>
    </div>
    <div class="card-body">
        <x-display-validation-errors/>
        @foreach ($feeInvoice->feeInvoiceRecords as $record)
            <form action="{{route('fee-invoices-records.pay', $record->id)}}" method="POST" class="col-span-5 overflow-scroll beautify-scrollbar grid grid-rows-1 md:grid-cols-5 gap-2 items-center  border-b p-2 md:py-0" x-data="{'amount': {{$record->amount->getAmount()->toInt()}}, 'waiver': {{$record->waiver->getAmount()->toInt()}}, 'fine': {{$record->fine->getAmount()->toInt()}}, 'paid': {{$record->paid->getAmount()->toInt()}}, 'payment_amount' : 0 }">
                <p class="font-bold  md:font-bold">{{$record->fee->name }}</p>
                <x-input id="amount-{{$record['id']}}" name="pay" label="Payment Amount" type="number" x-model.number="payment_amount" error-bag="some-random-thing"/>
                <div class="md:place-self-center">
                    <p x-text="'Fee Amount: ' + amount"></p>
                    <p x-text="'Fee Waiver: ' + waiver"></p>
                    <p x-text="'Fee Fine: ' + fine"></p>
                    <p x-text="'Fee Amount Paid: ' + paid"></p>
                </div>
                <p x-text="'Due: ' + (amount - waiver + fine - paid - payment_amount).toLocaleString()" class="md:place-self-center"></p>
                <input type="hidden" value="{{$record->fee->id}}">
                <x-button label="Add Payment" class="self-" icon="fas fa-money-check-alt"/>
                @csrf
            </form>
        @endforeach
        <p class="my-3">
            Note: The due stated might not be fully precise
        </p>
    </div>
</div>
