<div class="card">
    <div class="card-header">
        <h4 class="card-title">Manage exam record</h4>
    </div>
    <div class="card-body">
        <x-display-validation-errors/>

        <x-loading-spinner wire:target="exam"/>
        <x-loading-spinner wire:target="class"/>
        <x-loading-spinner wire:target="subject"/>
        <x-loading-spinner wire:target="section"/>

        {{-- form for selecting exam record to display --}}
        <form wire:submit="fetchExamRecords('{{$exam}}','{{$section}}','{{$subject}}')" class="md:grid gap-4 grid-cols-4 grid-rows-1  my-3 items-end">
            <div class="flex w-full flex-col gap-2">
                <april:label for="exam-id">Select exam</april:label>
                <april:select id="exam-id" name="exam_id" wire:model.live="exam">
                @foreach ($exams as $item)
                    <option value="{{$item['id']}}">{{$item['name']}}</option>
                @endforeach

                </april:select>
                @error('exam_id')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex w-full flex-col gap-2">
                <april:label for="class">Select class</april:label>
                <april:select id="class" name="class" wire:model.live="class">
                @foreach ($classes as $item)
                    <option value="{{$item['id']}}">{{$item['name']}}</option>
                @endforeach

                </april:select>
                @error('class')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex w-full flex-col gap-2">
                <april:label for="subject">Select subject</april:label>
                <april:select id="subject" name='subject' id="subject" wire:model.live="subject">
                @isset($subjects)
                    @foreach ($subjects as $subject)
                        <option value="{{$subject['id']}}">{{$subject['name']}}</option>
                    @endforeach
                @endisset

                </april:select>
                @error('subject')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex w-full flex-col gap-2">
                <april:label for="section">Section</april:label>
                <april:select id="section" name="section" wire:model.live="section">
                @isset($sections)
                    @foreach ($sections as $item)
                        <option value="{{$item['id']}}">{{$item['name']}}</option>
                    @endforeach
                @endisset

                </april:select>
                @error('section')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>
            <april:button type="submit" class="w-full ">
                View records
            </april:button>
        </form>

        <x-loading-spinner wire:target="fetchExamRecords"/>

        {{-- table for displaying exam records --}}
        @if(isset($examSlots) && isset($students) && $students != null)
            <div class="card" wire:loading.remove.delay wire:target="fetchExamRecords">
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

                    <april:input-group name="search" id="search" wire:model.live.debounce="search" placeholder="Search for student" />

                    @foreach ($students as $student)
                        <div wire:key="{{Str::Random('10')}}">
                            <div class=" relative bottom-20" id="student-{{$student->id}}"></div>
                            <form action="{{route('exam-records.store')}}#student-{{$student->id}}" class="md:grid grid-rows-1 grid-flow-col-dense gap-4 overflow-scroll beautify-scrollbar border-b items-center my-5 p-3 " method="POST">
                                <p class="md:w-40 font-bold">{{ $students->perPage() * ($students->currentPage() - 1) + $loop->iteration }}. {{$student->name}}</p>
                                @foreach ($examSlots as $examSlot)
                                    @php
                                        $examRecord = $examRecords->where('user_id',$student->id)->where('subject_id', $subjectSelected->id)->where('exam_slot_id', $examSlot->id)->first();
                                        $studentMarks = $examRecord ? $examRecord['student_marks'] : '0';
                                    @endphp
                                    @can('update exam record')

                                        <input type="hidden" name="exam_records[{{$loop->index}}][exam_slot_id]" value="{{$examSlot->id}}">
                                        <april:input-group id="student-{{$student->id}}" name="exam_records[{{$loop->index}}][student_marks]" label="{{$examSlot->name}} ({{$examSlot->total_marks}})" type="number" placeholder="Enter marks" value="{{$studentMarks}}" min="0" max="{{$examSlot->total_marks}}" class="min-w-[10rem]" />
                                    @else
                                        <p>{{$studentMarks}}</p>
                                    @endcan

                                @endforeach
                                <input type="hidden" name="subject_id" value="{{$subjectSelected->id}}">
                                <input type="hidden" name="user_id" value="{{$student->id}}">
                                <input type="hidden" name="section_id" value="{{$sectionSelected->id}}">
                                @csrf
                                @can('update exam record')

                                    <april:button type="submit" class="w-full min-w-[12rem] place-self-end">
                                        Submit
                                    </april:button>
                                @endcan
                            </form>
                        </div>
                    @endforeach

                {{$students->links('components.datatable-pagination-links-view')}}
                </div>
            </div>
        @elseif ($error)
            Action could not be completed because {{$error}}
        @endif
    </div>
</div>