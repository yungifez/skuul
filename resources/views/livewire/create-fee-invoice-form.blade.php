<form action="{{route('fee-invoices.store')}}" method="POST">
    <x-display-validation-errors/>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Set Invoice Setttings</h2>
        </div>
        <div class="card-body md:grid gap-4">
            <april:input-group id="issue_date" name="issue_date" label="Issue Date" type="date" wire:ignore />
            <april:input-group id="due_date" name="due_date" label="Due Date" type="date" wire:ignore />
            <div class="col-span-12 flex w-full flex-col gap-2">
                <april:label for="note">Note</april:label>
                <april:textarea id="note" name="note" wire:ignore />
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Students To Include</h2>
        </div>
        <div class="card-body">
            <x-loading-spinner wire:target="academicLevel"/>
            <x-loading-spinner wire:target="cycleSection"/>
            <x-loading-spinner wire:target="addStudent"/>
            <div class="md:grid md:grid-cols-3 gap-4">
                <div class="flex w-full flex-col gap-2">
                    <april:label for="academic-level">{{ school_term('class_level', 'Class') }}</april:label>
                    <april:select id="academic-level" name="" wire:model.live="academicLevel">
                    @foreach ($academicLevels as $item)
                        <option value="{{$item->id}}">{{$item->label ?? $item->name}}</option>
                    @endforeach

                    </april:select>
                </div>
                <div class="flex w-full flex-col gap-2">
                    <april:label for="cycle-section">{{ school_term('section', 'Section') }}</april:label>
                    <april:select id="cycle-section" name="" wire:model.live="cycleSection">
                    <option value="">All {{ school_terms('section', 'sections') }}</option>
                    @isset($cycleSections)
                        @foreach ($cycleSections as $item)
                            <option value="{{$item->id}}" @selected($cycleSection == $item->id)>{{$item->label ?? $item->name}}</option>
                        @endforeach
                    @endisset

                    </april:select>
                </div>
                <div class="flex w-full flex-col gap-2">
                    <april:label for="student">Student</april:label>
                    <april:select id="student" name="" wire:model.live="student">
                    <option value="">All Students</option>
                    @isset($students)
                        @foreach ($students as $item)
                            <option value="{{$item->id}}" @selected($student == $item->id)>{{$item->name}}</option>
                        @endforeach
                    @endisset

                    </april:select>
                </div>
                @php
                    $addStudentArgument = "$academicLevel";
                    if ($cycleSection != null && $cycleSection != 0) {
                        $addStudentArgument.=",$cycleSection";
                    }else{
                        $addStudentArgument.=",null";
                    }

                    if ($student != null && $student != 0) {
                        $addStudentArgument.=",$student";
                    }else{
                        $addStudentArgument.=",null";
                    }
                @endphp
            <april:button type="button" wire:click="addStudent({{$addStudentArgument}})" class="w-full" wire:loading.attr="disabled" wire:target="addStudent">
                    Add Student
                </april:button>
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
                                        <input type="hidden" name="student_records[]" value="{{$addedStudent->studentRecord?->id}}">
                                        <april:button type="button" variant="destructive" wire:click="removeStudent({{$addedStudent['id']}})" wire:loading.attr="disabled" wire:target="removeStudent">
                                            Remove
                                        </april:button>
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
            <x-loading-spinner wire:target="addFee"/>
            <x-loading-spinner wire:target="feeCategory"/>
            <div class="md:grid grid-cols-2 items-end gap-4">
                <div class="flex w-full flex-col gap-2">
                    <april:label for="fee">Fee Category</april:label>
                    <april:select id="fee" name="" wire:model.live="feeCategory">
                    @foreach ($feeCategories as $item)
                        <option value="{{$item->id}}">{{$item->name}}</option>
                    @endforeach

                    </april:select>
                </div>
                <div class="flex w-full flex-col gap-2">
                    <april:label for="fee">Fee</april:label>
                    <april:select id="fee" name="" wire:model.live="fee">
                    @isset($fees)
                        <option value="">All Fees </option>
                        @foreach ($fees as $item)
                            <option value="{{$item->id}}" @selected($fee == $item->id)>{{$item->name}}</option>
                        @endforeach
                    @endisset

                    </april:select>
                </div>
                <april:button type="button" wire:click="addFee({{$feeCategory}}, {{$fee}})" class="w-full md:w-2/3" wire:loading.attr="disabled" wire:target="addFee">
                    Add Fee(s)
                </april:button>
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
                                        <april:input-group type="number" :id="$addedFee['id'].'-amount'" name="records[{{$addedFee['id']}}][amount]" class="w-40 md:w-full" x-model.number="amount" />
                                    </td>
                                    <td class="border p-4 text-center whitespace-nowrap">
                                        <april:input-group type="number" :id="$addedFee['id'].'-waiver'" name="records[{{$addedFee['id']}}][waiver]" class="w-40 md:w-full" x-bind :max="amount" x-model.number="waiver" />
                                    </td>
                                    <td class="border p-4 text-center whitespace-nowrap">
                                        <april:input-group type="number" :id="$addedFee['id'].'-fine'" name="records[{{$addedFee['id']}}][fine]" class="w-40 md:w-full" x-model.number="fine" />
                                    </td>
                                    <td class="border p-4 text-center whitespace-nowrap">
                                       <p x-text="((parseInt(amount) - parseInt(waiver) + parseInt(fine) ) || 0).toLocaleString()"></p>
                                    </td>
                                    <td class="border p-4 text-center whitespace-nowrap">
                                        <input type="hidden" name="records[{{$addedFee['id']}}][fee_id]" value="{{$addedFee['id']}}">
                                        <april:button variant="destructive" wire:click="removeFee({{$index}})" type="button" wire:loading.attr="disabled" wire:target="removeFee">
                                            Remove
                                        </april:button>
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
    <input type="hidden" name="idempotency_key" value="{{ old('idempotency_key', (string) \Illuminate\Support\Str::uuid()) }}">
    <april:button type="submit" class="w-full md:w-3/12">
        <x-lucide-key class="mr-2 size-4" />
        Create Invoice
    </april:button>
</form>
