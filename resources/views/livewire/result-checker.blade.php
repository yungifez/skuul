<div class="card">
    <div class="card-header">
        <h4 class="card-title">Result Checker</h4>
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
        <form wire:submit.prevent="checkResult('{{$semester}}', '{{$student}}')" class=" my-3">
            <div class="col-12 d-md-flex px-0">
                <x-adminlte-select name="academic-year" label="Academic Year"  fgroup-class="col-md-2" enable-old-support wire:model="academicYear">
                    @foreach ($academicYears as $item)
                        <option value="{{$item['id']}}">{{$item->name()}}</option>
                    @endforeach
                </x-adminlte-select>
                <x-adminlte-select name="semester" label="Semester"  fgroup-class="col-md-2" enable-old-support wire:model="semester">
                    @foreach ($semesters as $item)
                        <option value="{{$item['id']}}">{{$item->name}}</option>
                    @endforeach
                </x-adminlte-select>
            
                <x-adminlte-select name="class" label="Class"  fgroup-class="col-md-2" enable-old-support wire:model="class">
                    @foreach ($classes as $item)
                        <option value="{{$item['id']}}">{{$item['name']}}</option>
                    @endforeach
                </x-adminlte-select>
                <x-adminlte-select name="section" label="Section" fgroup-class="col-md-2" wire:model="section">
                    @isset($sections)
                        @foreach ($sections as $item)
                            <option value="{{$item['id']}}">{{$item['name']}}</option>
                        @endforeach
                    @endisset
                </x-adminlte-select>
                <x-adminlte-select name="student" label="Student" fgroup-class="col-md-4" wire:model="student">
                    @isset($students)
                        @foreach ($students as $item)
                            <option value="{{$item['id']}}">{{$item['name']}}</option>
                        @endforeach
                    @endisset
                </x-adminlte-select>
                
            </div>
            <div class='col-md-2 h-inherit mt-auto mb-auto'>
                    <x-adminlte-button label="Check result" theme="primary" type="submit" class="col-md-12"/>
                </div>
        </form>

        @isset($exams)
            @foreach ($exams as $exam)
                <x-adminlte-card title="{{$exam->name}}" theme="info" maximizable>
                   @if (!$exam->examSlots->isEmpty())
                        <div class="table-responsive">
                            <table class="table table-bordered" style="white-space: nowrap">
                                <tr>
                                    <th class="text-primary">Subject</th>
                                    @foreach ($exam->examSlots()->pluck('name') as $examSlot)
                                        <th>{{$examSlot}}</th>
                                    @endforeach
                                    <th class="text-primary">Total</th>
                                </tr>

                                @foreach ($subjects as $subject)                                
                                    <tr>
                                        <th>{{$subject->name}}</th>
                                        @foreach ($exam->examSlots as $examSlot)
                                            <td class="text-center+">
                                                @if ($examRecords->where('subject_id' , $subject->id)->where('exam_slot_id' , $examSlot->id)->first())
                                                    {{$examRecords->where('subject_id' , $subject->id)->where('exam_slot_id' , $examSlot->id)->first()->student_marks}}
                                                @else
                                                    No record
                                                @endif
                                            </td>
                                        @endforeach
                                        <th>{{$examRecords->where('subject_id' , $subject->id)->whereIn('exam_slot_id', $exam->examSlots()->pluck('id'))->sum('student_marks')}}</th>
                                    </tr>
                                @endforeach
                            </table>
                        </div>

                        <p>Total marks obtained - {{$examRecords->whereIn('exam_slot_id', $exam->examSlots()->pluck('id'))->sum('student_marks')}}</p>
                   @else
                       <p>No exam records found</p>
                   @endif
                </x-adminlte-card>      
            @endforeach
        @endisset
    </div> 
</div>
