<div class="card">
    <div class="card-header">
        <h4 class="card-title">Graduate student </h4>
    </div>
    <div class="card-body">
        <x-display-validation-errors/>
        <form wire:submit.prevent="loadStudents" class="md:grid grid-cols-2 gap-4">
            <x-select id="class" name="class" label="Class" wire:model="class" >
                @foreach ($classes as $class)
                    <option value="{{$class['id']}}">{{$class['name']}}</option>
                @endforeach
            </x-select>
            <x-select id="section" name="section" label="Section" wire:init="loadInitialSections" wire:model="section" >
                @isset($sections)
                    @foreach ($sections as $item)
                        <option value="{{$item['id']}}">{{$item['name']}}</option>
                    @endforeach
                @endisset
            </x-select>
           
            <x-button label="Fetch students" icon="fas fa-paper-plane" type="submit" class="w-full md:w-6/12"/>
        </form>
        <div wire:loading.remove.delay>
            @if (isset($students))
                @if ($students->count() > 0)
                    <form ction="{{route('students.graduate')}}" method="post" class=" my-3 p-3">
                        <div class="overflow-scroll beautify-scrollbar w-full">
                            <table class="border w-full">
                                <thead>
                                    <th class="p-2 border">Student</th>
                                    <th class="p-2 border">Choose Action</th>
                                </thead>
                                <tbody>
                                    @foreach ($students as $student)
                                        <tr>
                                            <td class="border p-2 whitespace-nowrap">{{$student->name}}</td>
                                            <td class="border p-2">
                                                <x-select name="student_id[]" id="student-{{$student->id}}" >
                                                    <option value="{{$student['id']}}">Graduate</option>
                                                    <option value="">Dont graduate</option>
                                                </x-select>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @csrf
                        <x-button label="Promote students" class="w-full md:w-3/12 " icon="fas fa-key" type="submit"/>
                    </form> 
                @else
                    <x-alert title="Danger" id="{{Str::random('10')}}" class="my-2" wire:key="{{Str::random('10')}}">
                        <p>No students found</p>
                    </x-alert>
                @endif
            @endif
        </div>
    </div>
</div>

