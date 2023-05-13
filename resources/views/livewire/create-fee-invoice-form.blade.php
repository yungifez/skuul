<form action="{{route('fee-invoices.store')}}" method="POST">
    <x-display-validation-errors/>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Set Invoice Setttings</h2>
        </div>
        <div class="card-body md:grid gap-4">
            <x-input id="issue_date" name="issue_date" label="Issue Date" type="date" group-class="col-span-6" wire:ignore/>
            <x-input id="due_date" name="due_date" label="Due Date" type="date" group-class="col-span-6" wire:ignore/>
            <x-textarea id="note" name="note" label="Note"  group-class="col-span-12" wire:ignore/>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Students To Include</h2>
        </div>
        <div class="card-body">
            <x-loading-spinner wire:target="class"/>
            <x-loading-spinner wire:target="section"/>
            <x-loading-spinner wire:target="addStudent"/>
            <div class="md:grid md:grid-cols-3 gap-4">
                <x-select id="classes" name="" label="Class" wire:model="class">
                    @foreach ($classes as $item)
                        <option value="{{$item->id}}">{{$item->name}}</option>
                    @endforeach
                </x-select>
                <x-select id="section" name="" label="Section" wire:model="section">
                    <option value="">All Sections</option>
                    @isset($sections)
                        @foreach ($sections as $item)
                            <option value="{{$item->id}}">{{$item->name}}</option>
                        @endforeach
                    @endisset
                </x-select>
                <x-select id="student" name="" label="Student" wire:model="student">
                    <option value="">All Students</option>
                    @isset($students)
                        @foreach ($students as $item)
                            <option value="{{$item->id}}">{{$item->name}}</option>
                        @endforeach
                    @endisset
                </x-select>
                @php
                    $addStudentArgument = "$class";
                    if ($section != null && $section != 0) {
                        $addStudentArgument.=",$section";
                    }else{
                        $addStudentArgument.=",null";
                    }

                    if ($student != null && $student != 0) {
                        $addStudentArgument.=",$student";
                    }else{
                        $addStudentArgument.=",null";
                    }
                @endphp
                <x-button type="button" label="Add Student" wire:click="addStudent({{$addStudentArgument}})" class="w-full" wire:loading.attr="disabled"/>
            </div>
            @if (!$addedStudents->isEmpty())
                <div class="overflow-scroll beautify-scrollbar my-5">
                    <table class=" w-full border ">
                        <thead>
                            <th class="border p-4">S/N</th>
                            <th class="border p-4">Student Name</th>
                            <th class="border p-4">Email</th>
                            <th></th>
                        </thead>
                        <tbody>
                            @foreach ($addedStudents->SortBy('name') as $addedStudent)
                                <tr>
                                    <td class="border p-4 text-center">{{$loop->iteration}}</td>
                                    <td class="border p-4 text-center">{{$addedStudent['name']}}</td>
                                    <td class="border p-4 text-center">{{$addedStudent['email']}}</td>
                                    <td class="border p-4 text-center whitespace-nowrap">
                                        <input type="hidden" name="users[]" value="{{$addedStudent['id']}}">
                                        <x-button type="button" class="bg-red-600" label="Remove" wire:click="removeStudent({{$addedStudent['id']}})" wire:loading.disable/>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Fees To Include</h2>
        </div>
        <div class="card-body">
            <x-loading-spinner wire:target="addFees"/>
            <x-loading-spinner wire:target="feeCategory"/>
            <div class="md:grid grid-cols-2 items-end gap-4">
                <x-select id="fee" name="" label="Fee Category" wire:model="feeCategory">
                    @foreach ($feeCategories as $item)
                        <option value="{{$item->id}}">{{$item->name}}</option>
                    @endforeach
                </x-select>
                <x-select id="fee" name="" label="Fee" wire:model="fee">
                    @isset($fees)
                        <option value="">All Fees </option>
                        @foreach ($fees as $item)
                            <option value="{{$item->id}}">{{$item->name}}</option>
                        @endforeach
                    @endisset
                </x-select>
                <x-button type="button" label="Add Fee(s)" wire:click="addFee({{$feeCategory}}, {{$fee}})" class="w-full md:w-2/3" wire:loading.attr="disabled" />
            </div>
            @if (!$addedFees->isEmpty())
                <div class="overflow-scroll beautify-scrollbar my-5">
                    <table class="border w-full ">
                        <thead>
                            <th class="border p-4">S/N</th>
                            <th class="border p-4">Fee Name</th>
                            <th class="border p-4">Amount</th>
                            <th class="border p-4">Waiver</th>
                            <th class="border p-4">Fine</th>
                            <th class="border p-4">Total</th>
                        </thead>
                        <tbody >
                            @foreach ($addedFees as $index => $addedFee)
                                <tr x-data="{'amount': 0, 'waiver' : 0, 'fine' : 0}">
                                    <td class="border p-4 text-center">{{$loop->iteration}}</td>
                                    <td class="border p-4 text-center whitespace-nowrap">{{$addedFee['name']}}</td>
                                    <td class="border p-4 text-center whitespace-nowrap">
                                        <x-input type="number" :id="$addedFee['id'].'-amount'" name="records[{{$addedFee['id']}}][amount]" class="w-40 md:w-full" x-model.number="amount" />
                                    </td>
                                    <td class="border p-4 text-center whitespace-nowrap">
                                        <x-input type="number" :id="$addedFee['id'].'-waiver'" name="records[{{$addedFee['id']}}][waiver]" class="w-40 md:w-full" x-bind:max="amount" x-model.number="waiver"/>
                                    </td>
                                    <td class="border p-4 text-center whitespace-nowrap">
                                        <x-input type="number" :id="$addedFee['id'].'-fine'" name="records[{{$addedFee['id']}}][fine]" class="w-40 md:w-full" x-model.number="fine"/>
                                    </td>
                                    <td class="border p-4 text-center whitespace-nowrap">
                                       <p x-text="((parseInt(amount) - parseInt(waiver) + parseInt(fine) ) || 0).toLocaleString()"></p>
                                    </td>
                                    <td class="border p-4 text-center whitespace-nowrap">
                                        <input type="hidden" name="records[{{$addedFee['id']}}][fee_id]" value="{{$addedFee['id']}}">
                                        <x-button class="bg-red-600" label="Remove" wire:click="removeFee({{$index}})" type="button" wire:loading.attr="disabled"/>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
    @csrf
    <x-button label="Create Invoice" icon="fas fa-key" class="w-full md:w-3/12" wire:loading.attr="disabled"/>
</form>
