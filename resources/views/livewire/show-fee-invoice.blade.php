<div class="card">
    <div class="card-body">
        <h2 class="font-bold text-center text-3xl">#{{$feeInvoice->name}}</h2>
        <div class="md:flex justify-between my-3 parent-horizontal-align">
            <div class="py-3 border-t border-b horizontal-align">
                <h3 class="text-lg">From:</h3>
                <p class="font-bold">{{auth()->user()->school->name}}</p>
                <p class="">Address: {{auth()->user()->school->address}}</p>
                @if (($phone = auth()->user()->school->phone) != null)
                    <p class="">Phone: {{$phone}}</p>
                @endif
                @if (($email = auth()->user()->school->email) != null)
                    <p class="">Email: {{$email}}</p>
                @endif
            </div>
            <div class="py-3 border-t border-b horizontal-align">
                <h3 class="text-lg">To</h3>
                <p class="font-bold">{{$feeInvoice->user->name}}</p>
                <p>{{$feeInvoice->name}}</p>
                <p> Class: {{$feeInvoice->user->studentRecord->myClass->name}}</p>
                <p> Student Admission Number: {{$feeInvoice->user->studentRecord->admission_number}}</p>
            </div>
        </div>
        <div class="w-full my-4">
            <p>Fee Invoice Issue Date: {{$feeInvoice->issue_date->format('F j, Y')}}</p>
            <p>Fee Invoice Due Date: {{$feeInvoice->due_date->format('F j, Y')}}</p>
        </div>
        <div class="overflow-scroll beautify-scrollbar my-3">
            <table class="table-auto w-full">
                <thead>
                    <th class="p-4 border">S/N</th>
                    <th class="p-4 border">Fee Name</th>
                    <th class="p-4 border">Amount</th>
                    <th class="p-4 border">Waiver</th>
                    <th class="p-4 border">Fine</th>
                    <th class="p-4 border">Paid</th>
                </thead>
                <tbody>
                    @foreach ($feeInvoice->feeInvoiceRecords as $record)
                        <tr>
                            <td class="p-4 border">{{$loop->iteration}}</td>
                            <td class="p-4 border">{{$record->fee->name}}</td>
                            <td class="p-4 border">{{$record->amount->formatTo(app()->getLocale())}}</td>
                            <td class="p-4 border">{{$record->waiver->formatTo(app()->getLocale())}}</td>
                            <td class="p-4 border">{{$record->fine->formatTo(app()->getLocale())}}</td>
                            <td class="p-4 border">{{$record->paid->formatTo(app()->getLocale())}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="my-3 text-right">
            <p class="my-1">Total Amount: {{$feeInvoice->amount}}</p>
            <p class="my-1">Total Waiver: {{$feeInvoice->waiver}}</p>
            <p class="my-1">Total Fine: {{$feeInvoice->fine}}</p>
            <p class="my-1">Total Paid: {{$feeInvoice->paid}}</p>
            <p class="my-1">Pending Amount: <span class="text-red-700 dark:text-white">{{$feeInvoice->balance}}</span></p>
        </div>
        <div class="my-3 text-xl md:text-3xl status ">
            @if ($feeInvoice->balance->isLessThanOrEqualTo(0))
                <div class="p-4 text-center font-bold text-white bg-green-500 capitalize rounded">
                    This Invoice Has been Paid
                </div>
            @elseif ($feeInvoice->paid->isGreaterThan(0))
                <div class="p-4 text-center font-bold text-white bg-yellow-500 capitalize rounded">
                    This Invoice Has been Paid Partially
                </div>
            @else
                <div class="p-4 text-center font-bold text-white bg-red-500 capitalize rounded">
                    This Invoice Has <strong>Not</strong> been Paid
                </div>
            @endif
        </div>
    </div>
</div>
