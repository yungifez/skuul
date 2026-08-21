<div class="card">
    <div class="card-header">
        <h4 class="card-title">Assign teachers to subjects</h4>
    </div>
    <div class="card-body">
        <form action="" wire:submit="fetchSubjects('{{$class}}', '{{$teacher}}')" class="md:grid gap-4 grid-cols-3 items-end">
            <div class="flex w-full flex-col gap-2">
                <april:label for="class-id">Select class</april:label>
                <april:select id="class-id" name="my_class_id" wire:model.live="class">
                @foreach ($classes as $item)
                    <option value="{{$item['id']}}">{{$item['name']}}</option>
                @endforeach

                </april:select>
                @error('my_class_id')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex w-full flex-col gap-2">
                <april:label for="teacher_id">Select Teacher</april:label>
                <april:select id="teacher_id" name="teacherId" wire:model.live="teacher">
                @foreach ($teachers as $teacher)
                    <option value="{{$teacher->id}}">{{$teacher->name}}</option>
                @endforeach

                </april:select>
                @error('teacherId')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>
            <april:button type="submit"  class="w-full">
                <x-lucide-send class="mr-2 size-4" />
                Fetch Subjects
            </april:button>

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
                                            <div class="flex w-full flex-col gap-2">
                                                <april:select name="subjects[]" id="subject-{{$subject->id}}">
                                                <option value="{{$subject['id']}}">Include</option>
                                                <option value="">Dont Include</option>

                                                </april:select>
                                                @error('subjects[]')
                                                    <p class="text-sm text-destructive">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @csrf
                    <april:button type="submit">
                        <x-lucide-key class="mr-2 size-4" />
                        Assign teacher to subjects
                    </april:button>
                </form>
            @else
                <div wire:loading.remove.delay class="m-2">
                    <april:alert>
                        <slot:title>No subjects in this class</slot:title>
                    </april:alert>
                </div>
            @endif

        @endif
    </div>
</div>
