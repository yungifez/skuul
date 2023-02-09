<div class="card">
    <div class="card-header">
        <h4 class="card-title">Result Checker</h4>
    </div>
    <div class="card-body">
        @if (!auth()->user()->hasRole('student'))
            <x-display-validation-errors/>
            <x-loading-spinner/>
            {{-- form for selecting class and section to display --}}
            <form wire:submit.prevent="checkResult('{{$academicYear}}','{{$semester}}', '{{$student}}')" class="">
                <div class="md:flex gap-4 items-end">
                    <x-select id="academic-year" name="academic-year" label="Academic Year of exam"   wire:model="academicYear" group-class="md:w-3/12 ">
                        @isset($academicYears)
                            @foreach ($academicYears as $item)
                            <option value="{{$item['id']}}"> {{$item->name
                                }}</option>
                            @endforeach
                        @endisset
                    </x-select>
                    <x-select id="semester" name="semester" label="Semester of exam"   wire:model="semester" group-class="md:w-3/12">
                        <option value="">Entire Academic Year</option>
                        @isset($semesters)
                            @foreach ($semesters as $item)
                                <option value="{{$item['id']}}">{{$item['name']}}</option>
                            @endforeach
                        @endisset
                    </x-select>
                    {{--fields are not available to any role not in list--}}
                    @hasanyrole('super-admin|admin|teacher')
                        <x-select id="class" name="class" label="Current Class"   wire:model="class" group-class="md:w-3/12">
                            @isset($classes)
                            @foreach ($classes as $item)
                            <option value="{{$item['id']}}">{{$item['name']}}</option>
                            @endforeach
                            @endisset
                            
                        </x-select>
                        <x-select id="section" name="section" label="Current Section" fgroup-class="col-md-2" wire:model="section" group-class="md:w-3/12">
                            @isset($sections)
                                @foreach ($sections as $item)
                                    <option value="{{$item['id']}}">{{$item['name']}}</option>
                                @endforeach
                            @endisset
                        </x-select>
                    @endhasanyrole
                    <x-select id="student" name="student" label="Student"  wire:model="student" group-class="md:w-3/12">
                        @isset($students)
                            @foreach ($students as $item)
                                <option value="{{$item['id']}}">{{$item['name']}}</option>
                            @endforeach
                        @endisset
                    </x-select>
                </div>
                    
                <x-button label="Check result" theme="primary" type="submit" class="w-full md:w-3/12"/>
            </form>
        @endif
        @if ($preparedResults == true)
            @isset($exams)
                @foreach ($exams as $exam)
                <h3 class="md:text-xl font-bold text-center my-2">{{$studentName}}'s result in {{$exam->name}}</h3>
                    @if (!$exam->examSlots  ->isEmpty())
                        <div class="overflow-scroll beautify-scrollbar">
                            <table class="w-full " style="white-space: nowrap">
                                <tr>
                                    <th class="text-blue-500 border p-4">Subject</th>
                                    @foreach ($exam->examSlots as $examSlot)
                                        <th class="border p-4">{{$examSlot->name}} ({{$examSlot->total_marks}})</th>
                                    @endforeach
                                    <th class="text-green-500 border p-4">Total ({{$exam->examSlots->pluck('total_marks')->sum()}})</th>
                                </tr>

                                @foreach ($subjects as $subject)                                
                                    <tr>
                                        <th class="text-blue-600 border p-4">{{$subject->name}}</th>
                                        @foreach ($exam->examSlots as $examSlot)
                                            <td class="text-center border p-4">
                                                    {{$examRecords->where('subject_id' , $subject->id)->where('exam_slot_id' , $examSlot->id)->first()->student_marks ?? "No record"}} 
                                            </td>
                                        @endforeach
                                        <th class="border p-4 text-green-500">{{$examRecords->where('subject_id' , $subject->id)->whereIn('exam_slot_id', $exam->examSlots->pluck('id'))->sum('student_marks')}}</th>
                                    </tr>
                                @endforeach
                            </table>
                        </div>

                        <p class="my-3">Total marks obtained: {{$examRecords->whereIn('exam_slot_id', $exam->examSlots->pluck('id'))->sum('student_marks')}} / {{$exam->examSlots->pluck('total_marks')->sum() * $subjects->count()}}</p>
                    @else
                        <p>No exam records found</p>
                    @endif   
                @endforeach
            @endisset
        @elseif (isset($status))
            <P>{{$status}}</P>
        @endif
    </div> 
</div>
