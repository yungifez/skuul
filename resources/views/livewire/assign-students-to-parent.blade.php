<div class="card">
    <div class="card-header">
        <h4 class="card-title">Assign student to parent</h4>
    </div>
    <div class="card-body">
        <x-display-validation-errors/>
        {{-- form for selecting user --}}
        <form action="{{route('parents.assign-student', $parent->id)}}" method="POST" class=" md:grid grid-cols-3 gap-4">
            <div class="flex w-full flex-col gap-2">
                <april:label for="class">Class</april:label>
                <april:select id="class" name="class" wire:model.live="class">
                @isset($classes)
                    @foreach ($classes as $item)
                        <option value="{{$item['id']}}">{{$item['name']}}</option>
                    @endforeach
                @endisset

                </april:select>
                @error('class')
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
            <div class="flex w-full flex-col gap-2">
                <april:label for="student">Student</april:label>
                <april:select id="student" name="student_id" wire:model.live="student">
                @isset($students)
                    @foreach ($students as $item)
                        <option value="{{$item['id']}}">{{$item['name']}}</option>
                    @endforeach
                @endisset

                </april:select>
                @error('student_id')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>
            @csrf
            <april:button type="submit" class="w-full">
                Add student
            </april:button>
        </form>
        <x-loading-spinner/>

        <div class="my-3">
            <div class="table-responsive">
                <div class="overflow-scroll beautify-scrollbar">

                    <table id="children-list" class="w-full">
                        <thead class="">
                            <tr class=" text-white">
                                <th class="p-4 border">S/N</th>
                                <th class="p-4 border">Name</th>
                                <th class="p-4 border">Class</th>
                                <th class="p-4 border">section</th>
                                <th class="p-4 border">Email</th>
                                <th class="p-4 border">
                                </th>
                            </tr>
                        </thead>
                        @foreach($children as $student)
                            <tr>
                                <td class="p-4 text-center border">{{$loop->iteration}}</td>
                                <td class="p-4 text-center border">{{ $student->name}}</td>
                                <td class="p-4 text-center border">@isset ($student->studentRecord->myClass)
                                    {{$student->studentRecord->myClass->name}}
                                @endisset</td>
                                <td class="p-4 text-center border">@isset($student->studentRecord->section)
                                    {{$student->studentRecord->section->name}}
                                @endisset</td>
                                <td class="p-4 text-center border">{{ $student->email}}</td>
                                <td class="p-4 text-center border">
                                    <form action="{{route('parents.assign-student', $parent->id)}}" method="POST">
                                        <input type="hidden" name="student_id" value="{{$student->id}}">
                                        <input type="hidden" name="assign" value="0">
                                        @csrf
                                        <april:button type="submit" class="w-full">
                                            Remove student
                                        </april:button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
