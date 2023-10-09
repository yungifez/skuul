<div>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">{{$feeInvoice->name}}</h2>
        </div>
        <form action="{{route('fee-invoices.update', $feeInvoice->id)}}" method="POST" class="card-body">
            <x-display-validation-errors />
            <div class=" md:grid grid-cols-2 gap-4">
                <x-input id="issue_date" name="issue_date" label="Issue Date" type="date" wire:ignore value="{{$feeInvoice->issue_date->format('Y-m-d')}}"/>
                <x-input id="due_date" name="due_date" label="Due Date" type="date" wire:ignore value="{{$feeInvoice->due_date->format('Y-m-d')}}"/>
                <x-textarea id="note" name="note" label="Note"  group-class="col-span-2" wire:ignore>
                    {{$feeInvoice->note}}
                </x-textarea>
                @method('PUT')
                @csrf
                <x-button label="Edit" icon="fas fa-pen" class="w-full md:w-1/2 "/>
            </div>
        </form>
    </div>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Student Information</h2>
        </div>
        <div class="card-body overflow-scroll beautify-scrollbar">
            <table class="w-full">
                <th class="border p-4">Student Name</th>
                <th class="border p-4">Student Admission Number</th>
                <th class="border p-4">Student Class</th>
                <tbody>
                    <tr>
                        <td class="border p-4 text-center">{{$feeInvoice->user->name}}</td>
                        <td class="border p-4 text-center">{{$feeInvoice->user->studentRecord->admission_number}}</td>
                        <td class="border p-4 text-center">{{$feeInvoice->user->studentRecord->myClass->name}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Fee Information</h2>
    </div>
    <div class="card-body">
        @foreach ($feeInvoice->feeInvoiceRecords as $record)
        <div class="overflow-scroll beautify-scrollbar md:grid grid-rows-1 md:grid-cols-6 gap-2 items-end border-b p-2 md:py-0">
            <form action="{{route('fee-invoice-records.update', $record->id)}}" method="POST" class="col-span-5 overflow-scroll beautify-scrollbar grid grid-rows-1 md:grid-cols-6 gap-2 items-center " x-data="{'amount': {{$record->amount->getAmount()->toInt()}}, 'waiver': {{$record->waiver->getAmount()->toInt()}}, 'fine': {{$record->fine->getAmount()->toInt()}}}">
                <p class="font-bold  md:font-bold">{{$record->fee->name }}</p>
                <x-input id="amount-{{$record['id']}}" name="amount" label="Amount" type="number" x-model.number="amount" error-bag="some-random-thing"/>
                <x-input id="name-{{$record['id']}}" name="waiver" label="Waiver" type="number"
                x-model.number="waiver" error-bag="some-random-thing"/>
                <x-input id="name-{{$record['id']}}" name="fine" label="fine" type="number"
                x-model.number="fine" error-bag="some-random-thing"/>
                <p x-text="'Total: ' + (amount - waiver + fine).toLocaleString()" class="md:place-self-center"></p>
                <input type="hidden" value="{{$record->fee->id}}">
                <x-button label="Edit" class="self-end" icon="fas fa-pen"/>
                @csrf
                @method('PUT')
            </form>
            <x-modal title="Confirm Delete" background-colour="bg-red-600" class="justify-center" modal-button-class="w-full my-5">
                <div class="text-gray-700 dark:text-white text-center">
                    <i class="fa fa-trash text-7xl" aria-hidden="true"></i>
                    <p class="my-2">Are you sure you want to delete this resource</p>
                </div>
                <x-slot:footer>
                    <form action="{{route('fee-invoice-records.destroy', $record->id)}}" method="POST">
                        <x-button class="bg-red-600" icon="fa fa-trash" >
                            Continue With Delete
                        </x-button>
                        @method('delete')
                        @csrf
                    </form>
                </x-slot:footer>
            </x-modal>
        </div>
        @endforeach
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Add Fee To This Invoice</h2>
    </div>
    <form action="{{route('fee-invoice-records.store')}}" method="POST" class="card-body">
        <x-display-validation-errors error-bag="store_fee_invoice"/>
        <div class="md:grid md:grid-cols-2 gap-4">
            <x-select name="feeCategory" id="feeCategories" wire:model.live="feeCategory" label="Fee Category">
                @foreach ($feeCategories as $item)
                    <option value="{{$item->id}}">{{$item->name}}</option>
                @endforeach
            </x-select>
            <x-select name="fee_id" id="feeCategories" wire:model.live="fee" label="Fee">
                @isset($fees)
                    @foreach ($fees as $item)
                        <option value="{{$item->id}}">{{$item->name}}</option>
                    @endforeach
                @endisset
            </x-select>
        </div>
        <div class="md:grid md:grid-cols-4 gap-4" x-data="{'amount': 0, 'waiver': 0, 'fine':0, 'paid':0}">
            <input type="hidden" name="fee_invoice_id" value="{{$feeInvoice->id}}">
            <x-input id="amount" name="amount" label="Amount" type="number" x-model.number="amount" error-bag="some-random-thing"/>
            <x-input id="waiver" name="waiver" label="Waiver" type="number"  x-model.number="waiver" error-bag="some-random-thing"/>
            <x-input id="fine" name="fine" label="Fine" type="number" x-model.number="fine" error-bag="some-random-thing"/>
            <p x-text="'Total: '+((parseInt(amount) - parseInt(waiver) + parseInt(fine) - parseInt(paid)) || 0).toLocaleString()" class="self-end p-6"></p>
            @csrf
        </div>
        <x-button label="Create" icon="fas fa-key" class="w-full md:w-1/4"/>
    </form>
</div>
</div>
