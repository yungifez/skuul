<div class="card">
    <div class="card-header">
        <h4 class="card-title">Assign teachers to subjects</h4>
    </div>
    <div class="card-body">
        <form action="" wire:submit.prevent="fetchSubjects('{{$class}}', '{{$teacher}}')" class="md:grid gap-4 grid-cols-3 items-end">
            <x-select id="class-id" name="my_class_id" label="Select class"  wire:model="class">
                @foreach ($classes as $item)
                    <option value="{{$item['id']}}">{{$item['name']}}</option>
                @endforeach
            </x-select>
            <x-select id="teacher_id" name="teacherId" label="Select Teacher"  wire:model="teacher">
                @foreach ($teachers as $teacher)
                    <option value="{{$teacher->id}}">{{$teacher->name}}</option>
                @endforeach
            </x-select>
            <x-button label="Fetch Subjects" theme="primary" icon="fas fa-paper-plane" type="submit"  class="w-full"/>
            
        </form>
    </div>
    <div class="card-body">
        <x-loading-spinner />
        @isset($subjects)
            @if (!$subjects->isEmpty())
                <form action="{{route('subjects.assign-teacher-to-subject', $teacherState->id)}}" method="POST" >
                    <h4 class="text-bold text-center my-3 text-xl">Add or remove subjects you want {{$teacherState->firstname()}} to manage</h4>
                    <div class="overflow-scroll beautify-scrollbar w-full">
                        <table class="border w-full">
                            <thead>
                                <th class="p-2 border">Teacher</th>
                                <th class="p-2 border">Choose Action</th>
                            </thead>
                            <tbody>
                                @foreach ($subjects as $subject)
                                    <tr>
                                        <td class="border p-2 whitespace-nowrap">{{$subject->name}}</td>
                                        <td class="border p-2">
                                            <x-select name="subjects[]" id="subject-{{$subject->id}}" >
                                                <option value="{{$subject['id']}}">Include</option>
                                                <option value="">Dont Include</option>
                                            </x-select>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @csrf
                    <x-button label="Assign teacher to subjects" theme="primary" icon="fas fa-key" type="submit"/>
                </form>
            @else
                <div wire:loading.remove.delay class="m-2">
                    <x-alert title="No subjects in this class" />
                </div>
            @endif

        @endif
    </div>
</div>
