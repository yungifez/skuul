<div>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">{{$feeInvoice->name}}</h2>
        </div>
        <form action="{{route('fee-invoices.update', $feeInvoice->id)}}" method="POST" class="card-body">
            <x-display-validation-errors />
            <div class=" md:grid grid-cols-2 gap-4">
                <april:input-group id="issue_date" name="issue_date" label="Issue Date" type="date" wire:ignore value="{{$feeInvoice->issue_date->format('Y-m-d')}}" />
                <april:input-group id="due_date" name="due_date" label="Due Date" type="date" wire:ignore value="{{$feeInvoice->due_date->format('Y-m-d')}}" />
                <div class="col-span-2 flex w-full flex-col gap-2">
                    <april:label for="note">Note</april:label>
                    <april:textarea id="note" name="note" wire:ignore>{{$feeInvoice->note}}</april:textarea>
                </div>
                @method('PUT')
                @csrf
                <april:button class="w-full md:w-1/2 ">
                    <x-lucide-pencil class="mr-2 size-4" />
                    Edit
                </april:button>
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
                        <th class="border p-4">Current {{ school_term('section', 'section') }}</th>
                <tbody>
                    <tr>
                        <td class="border p-4 text-center">{{$feeInvoice->user->name}}</td>
                        <td class="border p-4 text-center">{{ $feeInvoice->user->studentRecord?->admission_number ?? 'Not recorded' }}</td>
                        <td class="border p-4 text-center">{{ $feeInvoice->user->studentRecord?->academicCycleSection?->academicLevel?->label ?? $feeInvoice->user->studentRecord?->academicCycleSection?->academicLevel?->name ?? 'Not currently placed' }}@if ($feeInvoice->user->studentRecord?->academicCycleSection) · {{ $feeInvoice->user->studentRecord->academicCycleSection->label ?? $feeInvoice->user->studentRecord->academicCycleSection->name }}@endif</td>
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
                <april:input-group id="amount-{{$record['id']}}" name="amount" label="Amount" type="number" x-model.number="amount" error-bag="some-random-thing" />
                <april:input-group id="name-{{$record['id']}}" name="waiver" label="Waiver" type="number" x-model.number="waiver" error-bag="some-random-thing" />
                <april:input-group id="name-{{$record['id']}}" name="fine" label="fine" type="number" x-model.number="fine" error-bag="some-random-thing" />
                <p x-text="'Total: ' + (amount - waiver + fine).toLocaleString()" class="md:place-self-center"></p>
                <input type="hidden" value="{{$record->fee->id}}">
                <april:button class="self-end">
                    <x-lucide-pencil class="mr-2 size-4" />
                    Edit
                </april:button>
                @csrf
                @method('PUT')
            </form>
            <april:alert-dialog>
                <slot:trigger>
                    <april:button variant="outline" size="sm" type="button" class="w-full my-5">
                        <x-lucide-trash-2 class="mr-2 size-4" />
                        Delete
                    </april:button>
                </slot:trigger>
                <slot:content>
                    <april:alert-dialog-header>
                        <slot:title>Confirm Delete</slot:title>
                        <slot:description>Are you sure you want to delete this resource?</slot:description>
                    </april:alert-dialog-header>
                    <april:alert-dialog-footer>
                        <april:alert-dialog-cancel>Cancel</april:alert-dialog-cancel>
                        <form action="{{route('fee-invoice-records.destroy', $record->id)}}" method="POST">
                            @method('delete')
                            @csrf
                            <april:button type="submit" variant="destructive">
                                <x-lucide-trash-2 class="mr-2 size-4" />
                                Continue With Delete
                            </april:button>
                        </form>
                    </april:alert-dialog-footer>
                </slot:content>
            </april:alert-dialog>
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
            <div class="flex w-full flex-col gap-2">
                <april:label for="feeCategories">Fee Category</april:label>
                <april:select name="feeCategory" id="feeCategories" wire:model.live="feeCategory">
                @foreach ($feeCategories as $item)
                    <option value="{{$item->id}}">{{$item->name}}</option>
                @endforeach

                </april:select>
                @if (isset($errors) && $errors->has('feeCategory'))
                    <p class="text-sm text-destructive">{{ $errors->first('feeCategory') }}</p>
                @endif
            </div>
            <div class="flex w-full flex-col gap-2">
                <april:label for="feeCategories">Fee</april:label>
                <april:select name="fee_id" id="feeCategories" wire:model.live="fee">
                @isset($fees)
                    @foreach ($fees as $item)
                        <option value="{{$item->id}}">{{$item->name}}</option>
                    @endforeach
                @endisset

                </april:select>
                @if (isset($errors) && $errors->has('fee_id'))
                    <p class="text-sm text-destructive">{{ $errors->first('fee_id') }}</p>
                @endif
            </div>
        </div>
        <div class="md:grid md:grid-cols-4 gap-4" x-data="{'amount': 0, 'waiver': 0, 'fine':0, 'paid':0}">
            <input type="hidden" name="fee_invoice_id" value="{{$feeInvoice->id}}">
            <april:input-group id="amount" name="amount" label="Amount" type="number" x-model.number="amount" error-bag="some-random-thing" />
            <april:input-group id="waiver" name="waiver" label="Waiver" type="number" x-model.number="waiver" error-bag="some-random-thing" />
            <april:input-group id="fine" name="fine" label="Fine" type="number" x-model.number="fine" error-bag="some-random-thing" />
            <p x-text="'Total: '+((parseInt(amount) - parseInt(waiver) + parseInt(fine) - parseInt(paid)) || 0).toLocaleString()" class="self-end p-6"></p>
            @csrf
        </div>
        <april:button class="w-full md:w-1/4">
            <x-lucide-key class="mr-2 size-4" />
            Create
        </april:button>
    </form>
</div>
</div>
