<div class="card">
    <div class="card-header">
        <h4 class="card-title">Result Checker</h4>
    </div>
    <div class="card-body">
        @if (!auth()->user()->hasRole('student'))
            <x-display-validation-errors/>
            <x-loading-spinner/>
            {{-- form for selecting class and section to display --}}
            <form wire:submit="checkResult('{{$academicYear}}','{{$semester}}', '{{$student}}')" class="">
                <div class="md:grid grid-cols-3 gap-4 items-end">
                    <x-select id="academic-year" name="academic-year" label="Academic Year of exam"   wire:model.live="academicYear" group-class=" ">
                        @isset($academicYears)
                            @foreach ($academicYears as $item)
                            <option value="{{$item['id']}}"> {{$item->name
                                }}</option>
                            @endforeach
                        @endisset
                    </x-select>
                    <x-select id="semester" name="semester" label="Semester of exam"   wire:model.live="semester" group-class="">
                        <option value="">Entire Academic Year</option>
                        @isset($semesters)
                            @foreach ($semesters as $item)
                                <option value="{{$item['id']}}" >{{$item['name']}}</option>
                            @endforeach
                        @endisset
                    </x-select>
                    {{--fields are not available to any role not in list--}}
                    @hasanyrole('super-admin|admin|teacher')
                        <x-select id="class" name="class" label="Current Class"   wire:model.live="class" group-class="">
                            @isset($classes)
                            @foreach ($classes as $item)
                            <option value="{{$item['id']}}">{{$item['name']}}</option>
                            @endforeach
                            @endisset
                            
                        </x-select>
                        <x-select id="section" name="section" label="Current Section" fgroup-class="col-md-2" wire:model.live="section" group-class="">
                            @isset($sections)
                                @foreach ($sections as $item)
                                    <option value="{{$item['id']}}">{{$item['name']}}</option>
                                @endforeach
                            @endisset
                        </x-select>
                    @endhasanyrole
                    <x-select id="student" name="student" label="Student"  wire:model.live="student" group-class="">
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
                <div class="overflow-scroll beautify-scrollbar">
                    <table class="border w-full">
                        <thead>
                            <th></th>
                            @foreach ($exams as $exam)
                                <th class="p-3 border whitespace-nowrap min-w-[4rem] leading-3" style="writing-mode: vertical-rl;text-orientation: sideways;">{{$exam->name}} ({{$exam->totalAttainableMarksInASubject}})</th>
                            @endforeach
                            <th class="p-3 border text-green-500 whitespace-nowrap min-w-[4rem] leading-3" style="writing-mode: vertical-rl;text-orientation: sideways;">Total ({{$exams->pluck('totalAttainableMarksInASubject')->sum()}})</th>
                            <th class="p-3 border text-green-500 whitespace-nowrap min-w-[4rem] leading-3" style="writing-mode: vertical-rl;text-orientation: sideways;">Grade</th>
                            <th class="p-3 border text-green-500 whitespace-nowrap min-w-[4rem] leading-3" style="writing-mode: vertical-rl;text-orientation: sideways;">Remark</th>
                        </thead>
                        <tbody>
                            @foreach ($subjects as $subject)
                                <tr>
                                    <th class="p-4 border">{{$subject->name}}</th>
                                    @foreach ($exams as $exam)
                                        <td class="p-4 border">
                                            {{$examRecords->whereIn('exam_slot_id', $exam->examSlots?->pluck('id'))->where('subject_id' , $subject->id)->pluck('student_marks')->sum()}} 
                                        </td>
                                    @endforeach
                                    <td class="p-4 border text-green-500 text-left">
                                        {{$examRecords->where('subject_id' , $subject->id)->pluck('student_marks')->sum()}} 
                                    </td>
                                    @php
                                        $grade = app(App\Services\GradeSystem\GradeSystemService::class)->getGrade($selectedClass->classGroup->id, ( $examRecords->where('subject_id' , $subject->id)->pluck('student_marks')->sum()/$exams->pluck('totalAttainableMarksInASubject')->sum()) * 100)
                                    @endphp
                                    <td class="p-4 border text-green-500">
                                        {{$grade->name ?? 'No Grade'}}
                                    </td>
                                    <td class="p-4 border text-green-500">
                                        {{$grade->remark ?? 'No Remark'}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div x-data="{'showBreakdown' : false}" class="my-5">
                    <x-button label="Show Full Breakdown" @click="showBreakdown = !showBreakdown" >
                        <i :class="{'fas fa-angle-up' : showBreakdown == false, 'fas fa-angle-down' : showBreakdown == true,}"></i>
                    </x-button>
                    <div x-show="showBreakdown" x-transition style="display: none">
                        @foreach ($exams as $exam)
                        @if (!$exam->examSlots->isEmpty())
                            <h3 class="md:text-xl font-bold text-center my-2">{{$studentName}}'s result in {{$exam->name}}</h3>
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
                        @endif   
                        @endforeach
                    </div>
                </div>
            @endisset
        @elseif (isset($status))
            <P>{{$status}}</P>
        @endif
    </div> 
</div>
