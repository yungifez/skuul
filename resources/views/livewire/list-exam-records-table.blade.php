<div class="card">
    <div class="card-header">
        <h4 class="card-title">Manage exam record</h4>
    </div>
    <div class="card-body">
        @livewire('display-validation-error')
        {{-- loading spinner --}}
        <div class="d-flex justify-content-center">
            <div wire:loading class="spinner-border" role="status">
                <p class="sr-only">Loading.....</p>
            </div>
        </div>
        {{-- form for selecting exam record to display --}}
        <form wire:submit.prevent="fetchExamRecords('{{$exam}}','{{$section}}','{{$subject}}')" class="d-md-flex my-3">
            <div  class="d-md-flex col-md-10 px-0">
                <x-adminlte-select name="exam_id" label="Select exam"  fgroup-class="col-md-3" enable-old-support wire:model="exam">
                    @foreach ($exams as $item)
                        <option value="{{$item['id']}}">{{$item['name']}}</option>
                    @endforeach
                </x-adminlte-select>
                <x-adminlte-select name="class" label="Select class"  fgroup-class="col-md-3" enable-old-support wire:model="class">
                    @foreach ($classes as $item)
                        <option value="{{$item['id']}}">{{$item['name']}}</option>
                    @endforeach
                </x-adminlte-select>
                <x-adminlte-select name='subject' id="subject" label="Select subject" fgroup-class="col-md-3" enable-old-support wire:model="subject" >
                    @isset($subjects)
                        @foreach ($subjects as $subject)
                            <option value="{{$subject['id']}}">{{$subject['name']}}</option>
                        @endforeach
                    @endisset
                </x-adminlte-select>
                <x-adminlte-select name="section" label="Section" fgroup-class="col-md-3" wire:model="section">
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
        
        {{-- table for displaying exam records --}}
        @isset($examSlots)
            <x-adminlte-card title="{{$examSelected->name}} exam records" theme="primary" icon=""  collapsible="" >
                <div class="d-md-flex">
                    <p class="col-md-3">Exam: {{$examSelected->name}}</p>
                    <p class="col-md-3">Class: {{$classSelected->name}}</p>
                    <p class="col-md-3">Section: {{$sectionSelected->name}}</p>
                    <p class="col-md-3">Subject: {{$subjectSelected->name}}</p>
                </div>
                <div>
                    <div class="table-responsive">
                        <table class="table  table-bordered table-hover">
                            <thead class="">
                                <th>S/N</th>
                                <th style="min-width: 200px; min-height: 40px;">Name</th>
                                <th>Admission number:</th>
                                {{--disolay all examslots as headers--}}
                                @foreach ($examSlots as $examSlot)
                                    <th style="min-width: 200px; min-height: 40px;" class="">{{$examSlot->name}} ({{$examSlot->total_marks}})</th>
                                @endforeach
                                <th></th>
                            </thead>
                            <tbody>
                                @foreach ($students as $student)
                                <tr>
                                    <th scope="row">{{$loop->index + 1}}</th>
                                    <td>{{$student->name}}</td>
                                    <td>{{$student->studentRecord->admission_number}}</td>
                                    @foreach ($examSlots as $examSlot)
                                        <td>
                                            @php 
                                            $examRecord = $examRecords->where('user_id',$student->id)->where('subject_id', $subjectSelected->id)->where('exam_slot_id', $examSlot->id)->first()
                                            @endphp
                                           {{$examRecord ? $examRecord['student_marks'] : '0'}}
                                        </td>
                                    @endforeach
                                    <td style="min-width: 200px; min-height: 40px;">
                                        @can('update exam record', )
                                            <x-adminlte-button label="Manage marks" data-toggle="modal" data-target="#manageStudentRecord-{{$student->id}}" class="bg-primary"/>
                                        @endcan   
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @can('update exam record', )
                            @foreach ($students as $student)
                            {{-- modal for managing student exam record --}}
                                <x-adminlte-modal id="manageStudentRecord-{{$student->id}}" title="Exam record for {{$student->name}}" size="lg" theme="primary" icon="fas fa-bell" v-centered static-backdrop scrollable>
                                    <form action="{{route('exam-records.store')}}" method="POST">
                                        @foreach ($examSlots as $examSlot)
                                            @php 
                                                $examRecord = $examRecords->where('user_id',$student->id)->where('subject_id', $subjectSelected->id)->where('exam_slot_id', $examSlot->id)->first();
                                                $studentMarks = $examRecord ? $examRecord['student_marks'] : '0';
                                            @endphp
    
                                            <input type="hidden" name="exam_records[{{$loop->index}}][exam_slot_id]" value="{{$examSlot->id}}">
                                            <x-adminlte-input name="exam_records[{{$loop->index}}][student_marks]" label="{{$examSlot->name}} ({{$examSlot->total_marks}})" type="number" placeholder="Enter marks" value="{{$studentMarks}}" min="0" max="{{$examSlot->total_marks}}"/>
                                        @endforeach
                                        <input type="hidden" name="subject_id" value="{{$subjectSelected->id}}">
                                        <input type="hidden" name="user_id" value="{{$student->id}}">
                                        <input type="hidden" name="section_id" value="{{$sectionSelected->id}}">
                                        @csrf
                                        <div class='col-md-6'>
                                            <x-adminlte-button label="Submit" theme="primary" type="submit" class="col-md-12"/>
                                        </div>
                                        <x-slot name="footerSlot">
                                            <x-adminlte-button theme="danger" label="Dismiss" data-dismiss="modal"/>
                                        </x-slot>
                                    </form>
                                </x-adminlte-modal>
                            @endforeach
                        @endcan
                    </div>
                </div>
            </x-adminlte-card>
        @elseif ($error)
            Action could not be completed because {{$error}}
        @endisset
    </div>
</div>