<div class="card">
    <div class="card-header">
        <h4 class="card-title">Promote student</h4>
    </div>
    <div class="card-body">
        <x-display-validation-errors/>
        {{--Form for selecting class--}}
        <form wire:submit.prevent="loadStudents" class="md:grid grid-cols-4 gap-2">
            <p class="font-bold col-span-4">Please select class and section</p>
            <x-select id="old-class" name="oldClass" label="Old class" wire:model="oldClass">
                @foreach ($classes as $class)
                    <option value="{{$class['id']}}">{{$class['name']}}</option>
                @endforeach
            </x-select>
            <x-select id="old-section" name="oldSection" label="Old section" wire:model="oldSection" >
                @isset($oldSections)
                    @foreach ($oldSections as $section)
                        <option value="{{$section['id']}}">{{$section['name']}}</option>
                    @endforeach
                @endisset
            </x-select>
            <x-select id="new-class" name="newClass" label="New class" wire:model="newClass" >
                @foreach ($classes as $class)
                    <option value="{{$class['id']}}">{{$class['name']}}</option>
                @endforeach
            </x-select>
            <x-select id="new-section" name="newSection" label="New section" wire:model="newSection" >
                @isset($newSections)
                    @foreach ($newSections as $section)
                        <option value="{{$section['id']}}">{{$section['name']}}</option>
                    @endforeach
                @endisset
            </x-select>
            <x-button label="Fetch students" class="w-full  " icon="fas fa-key" type="submit"/>
        </form>
        <x-loading-spinner />
        <div wire:loading.remove.delay>
            @if (isset($students))
            @if ($students->count() > 0)
            <form action="{{route('students.promote')}}" method="post" class=" my-3 p-3">
                <div class="grid grid-cols-1 lg:grid-cols-2 p-4 gap-4">
                    <x-button label="Set All To Promote" @click="setAllSelectsToPromote()" type="button"/>
                    <x-button label="Set All To Don't Promote" @click="setAllSelectsToDontPromote()" type="button"/>
                </div>
                        <input type="hidden" name="old_class_id" value="{{$oldClass}}">
                        <input type="hidden" name="old_section_id" value="{{$oldSection}}">
                        <input type="hidden" name="new_class_id" value="{{$newClass}}">    
                        <input type="hidden" name="new_section_id" value="{{$newSection}}">
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
                                                <x-select name="student_id[]" id="student-{{$student->id}}" class="promote">
                                                    <option value="{{$student['id']}}">Promote</option>
                                                    <option value="">Dont promote</option>
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

@push('scripts')

<script>
    function setAllSelectsToDontPromote() {
        let selects = document.getElementsByClassName('promote');
        for (let i = 0; i < selects.length; i++) {
            selects[i].selectedIndex = 1;
        }
    }

    function setAllSelectsToPromote() {
        let selects = document.getElementsByClassName('promote');
        for (let i = 0; i < selects.length; i++) {
            selects[i].selectedIndex = 0;
        }
    }
</script>

@endpush

