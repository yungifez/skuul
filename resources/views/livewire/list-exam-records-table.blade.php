<div class="card">
    <div class="card-header">
        <h4 class="card-title">Manage exam record</h4>
    </div>
    <div class="card-body">
        <x-display-validation-errors/>
        {{-- loading spinner --}}
        <div class="d-flex justify-content-center">
            <div wire:loading class="spinner-border" role="status">
                <p class="sr-only">Loading.....</p>
            </div>
        </div>
        {{-- form for selecting exam record to display --}}
        <form wire:submit.prevent="fetchExamRecords('{{$exam}}','{{$section}}','{{$subject}}')" class="md:grid gap-4 grid-cols-4 grid-rows-1  my-3 items-end">
            <x-select id="exam-id" name="exam_id" label="Select exam" wire:model="exam">
                @foreach ($exams as $item)
                    <option value="{{$item['id']}}">{{$item['name']}}</option>
                @endforeach
            </x-select>
            <x-select id="class" name="class" label="Select class" wire:model="class">
                @foreach ($classes as $item)
                    <option value="{{$item['id']}}">{{$item['name']}}</option>
                @endforeach
            </x-select>
            <x-select id="subject" name='subject' id="subject" label="Select subject"   wire:model="subject" >
                @isset($subjects)
                    @foreach ($subjects as $subject)
                        <option value="{{$subject['id']}}">{{$subject['name']}}</option>
                    @endforeach
                @endisset
            </x-select>
            <x-select id="section" name="section" label="Section"  wire:model="section">
                @isset($sections)
                    @foreach ($sections as $item)
                        <option value="{{$item['id']}}">{{$item['name']}}</option>
                    @endforeach
                @endisset
            </x-select>
            <x-button label="View records" theme="primary" type="submit" class="w-"/>
        </form>
        
        {{-- table for displaying exam records --}}
        @if(isset($examSlots) && $students != null)
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{$examSelected->name}} exam records</h4>
                </div>
                <div class="card-body">
                    <div class="md:flex justify-between ">
                        <p class="">Exam: {{$examSelected->name}}</p>
                        <p class="">Class: {{$classSelected->name}}</p>
                        <p class="">Section: {{$sectionSelected->name}}</p>
                        <p class="">Subject: {{$subjectSelected->name}}</p>
                    </div>

                    <div class="overflow-x-scroll beautify-scrollbar">
                        <table class="border w-full my-4 table-auto text-center" id="list-exam-records">
                            <thead class="thead-dark">
                                <th class="border whitespace-nowrap p-4">S/N</th>
                                <th class="border whitespace-nowrap p-4">Name</th>
                                <th class="border whitespace-nowrap p-4">Admission number:</th>
                                {{--disolay all examslots as headers--}}
                                @foreach ($examSlots as $examSlot)
                                    <th class="border whitespace-nowrap p-4">{{$examSlot->name}} ({{$examSlot->total_marks}})</th>
                                @endforeach
                                <th class="border"></th>
                            </thead>
                            <tbody>
                                @foreach ($students->load('studentRecord') as $student)
                                <tr>
                                    <th class="border whitespace-nowrap p-4">{{ $students->perPage() * ($students->currentPage() - 1) + $loop->iteration }}</th>
                                    <td class="border whitespace-nowrap p-4">{{$student->name}}</td>
                                    <td class="border whitespace-nowrap p-4">{{$student->studentRecord->admission_number}}</td>
                                    @foreach ($examSlots as $examSlot)
                                        <td class="border whitespace-nowrap p-4">
                                            @php 
                                            $examRecord = $examRecords->where('user_id',$student->id)->where('subject_id', $subjectSelected->id)->where('exam_slot_id', $examSlot->id)->first()
                                            @endphp
                                           {{$examRecord ? $examRecord['student_marks'] : '0'}}
                                        </td>
                                    @endforeach
                                    @can('update exam record', )
                                        <td class="border whitespace-nowrap p-4">
                                            <x-modal title="Exam Records For {{$student->name}}" background-colour="bg-blue-600" button-text="Manage Marks" size="lg">
                                                <form action="{{route('exam-records.store')}}" method="POST" class="overflow-y-scroll w-full p-4 text-left">
                                                    @foreach ($examSlots as $examSlot)
                                                        @php 
                                                            $examRecord = $examRecords->where('user_id',$student->id)->where('subject_id', $subjectSelected->id)->where('exam_slot_id', $examSlot->id)->first();
                                                            $studentMarks = $examRecord ? $examRecord['student_marks'] : '0';
                                                        @endphp
                
                                                        <input type="hidden" name="exam_records[{{$loop->index}}][exam_slot_id]" value="{{$examSlot->id}}">
                                                        <x-input id="student-{{$student->id}}" name="exam_records[{{$loop->index}}][student_marks]" label="{{$examSlot->name}} ({{$examSlot->total_marks}})" type="number" placeholder="Enter marks" value="{{$studentMarks}}" min="0" max="{{$examSlot->total_marks}}"/>
                                                    @endforeach
                                                    <input type="hidden" name="subject_id" value="{{$subjectSelected->id}}">
                                                    <input type="hidden" name="user_id" value="{{$student->id}}">
                                                    <input type="hidden" name="section_id" value="{{$sectionSelected->id}}">
                                                    @csrf
                                                        <x-button label="Submit" theme="primary" type="submit" class="w-full md:w-4/12"/>
                                                </form>
                                            </x-modal>
                                        </td>
                                    @endcan
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                    </div>
                    {{$students->links()}}
                </div>
            </div>
        @elseif ($error)
            Action could not be completed because {{$error}}
        @endif
    </div>
</div>