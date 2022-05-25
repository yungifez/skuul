<div class="card">
    <div class="card-header">
        <h4 class="card-title">Exam tabilation</h4>
    </div>
    <div class="card-body">
        @livewire('display-validation-error')
        {{-- loading spinner --}}
        <div class="d-flex justify-content-center">
            <div wire:loading class="spinner-border" role="status">
                <p class="sr-only">Loading.....</p>
            </div>
        </div>
        {{-- form for selecting class and section to display --}}
        <form wire:submit.prevent="tabulate('{{$exam}}','{{$section}}')" class="d-md-flex my-3">
            <div class="d-md-flex col-md-10 px-0">
                <x-adminlte-select name="exam_id" label="Select exam"  fgroup-class="col-md-4" enable-old-support wire:model="exam">
                    @foreach ($exams as $item)
                        <option value="{{$item['id']}}">{{$item['name']}}</option>
                    @endforeach
                </x-adminlte-select>
                <x-adminlte-select name="class" label="Select class"  fgroup-class="col-md-4" enable-old-support wire:model="class">
                    @foreach ($classes as $item)
                        <option value="{{$item['id']}}">{{$item['name']}}</option>
                    @endforeach
                </x-adminlte-select>
                <x-adminlte-select name="section" label="Section" fgroup-class="col-md-4" wire:model="section">
                    @isset($sections)
                        @foreach ($sections as $item)
                            <option value="{{$item['id']}}">{{$item['name']}}</option>
                        @endforeach
                    @endisset
                </x-adminlte-select>
            </div>
            <div class='col-md-2 h-inherit mt-auto mb-auto'>
                <x-adminlte-button label="View records" theme="primary" type="submit" class="col-md-12"/>
            </div>
        </form>
    
        
        @isset($subjects)
                <p>Total obtainable marks in each subject: {{$totalMarksAttainableInEachSubject}}</p>
                <p>Total Marks accross all subjects: {{$totalMarksAttainableInEachSubject * $subjects->count()}}</p>
                @php
                    $heads = $subjects->sortBy('name')->pluck('name');
                    $heads = $heads->prepend('Admission number')->prepend('Student name')->prepend('S/N');
                    $heads = $heads->push('Total')->push('Percentage')->push('Grade');
                @endphp
                <x-adminlte-datatable id="class-list-table" :heads="$heads" Class='text-capitalize' class="my-2">
                    @foreach ($tabulatedRecords as $tabulatedRecord)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>{{$tabulatedRecord['student_name']}}</td>
                        <td>{{$tabulatedRecord['admission_number']}}</td>
                        @foreach ($tabulatedRecord['student_marks'] as $item)
                            <td>{{$item}}</td>
                        @endforeach
                        <td>{{$tabulatedRecord['total']}}</td>
                        <td>{{$tabulatedRecord['percent']}}</td>
                        <td>{{$tabulatedRecord['grade']}}</td>
                    </tr>
                    @endforeach
                </x-adminlte-datatable>
        @endisset
    </div>
</div>
