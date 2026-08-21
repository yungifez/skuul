<div class="card">
    <div class="card-header">
        <h4 class="card-title">Graduate student </h4>
    </div>
    <div class="card-body">
        <x-display-validation-errors/>
        <form wire:submit="loadStudents" class="md:grid grid-cols-2 gap-4">
            <div class="flex w-full flex-col gap-2">
                <april:label for="class">Class</april:label>
                <april:select id="class" name="class" wire:model.live="class">
                @foreach ($classes as $class)
                    <option value="{{$class['id']}}">{{$class['name']}}</option>
                @endforeach

                </april:select>
                @error('class')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex w-full flex-col gap-2">
                <april:label for="section">Section</april:label>
                <april:select id="section" name="section" wire:init="loadInitialSections" wire:model.live="section">
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

            <april:button type="submit" class="w-full md:w-6/12">
                <x-lucide-send class="mr-2 size-4" />
                Fetch students
            </april:button>
        </form>
        <div wire:loading.remove.delay>
            @if (isset($students))
                @if ($students->count() > 0)
                    <form ction="{{route('students.graduate')}}" method="post" class=" my-3 p-3">
                        <div class="overflow-scroll beautify-scrollbar w-full">
                            <div class="grid grid-cols-1 lg:grid-cols-2 p-4 gap-4">
                                <april:button @click="setAllSelectsToGraduate()" type="button">
                                    Set All To Graduate
                                </april:button>
                                <april:button @click="setAllSelectsToDontGraduate()" type="button">
                                    Set All To Don't Graduate
                                </april:button>
                            </div>
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
                                                <div class="flex w-full flex-col gap-2">
                                                    <april:select name="student_id[]" id="student-{{$student->id}}" class="graduate">
                                                    <option value="{{$student['id']}}">Graduate</option>
                                                    <option value="">Dont graduate</option>

                                                    </april:select>
                                                    @error('student_id[]')
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
                        <april:button class="w-full md:w-3/12 " type="submit">
                            <x-lucide-key class="mr-2 size-4" />
                            Graduate students
                        </april:button>
                    </form>
                @else
                    <april:alert variant="destructive" class="my-2" wire:key="{{Str::random('10')}}">
                        <slot:icon><x-lucide-ban class="size-4" /></slot:icon>
                        <slot:title>Danger</slot:title>
                        <slot:description>No students found</slot:description>
                    </april:alert>
                @endif
            @endif
        </div>
    </div>
</div>

@push('scripts')

<script>
    function setAllSelectsToDontGraduate() {
        let selects = document.getElementsByClassName('graduate');
        for (let i = 0; i < selects.length; i++) {
            selects[i].selectedIndex = 1;
        }
    }

    function setAllSelectsToGraduate() {
        let selects = document.getElementsByClassName('graduate');
        for (let i = 0; i < selects.length; i++) {
            selects[i].selectedIndex = 0;
        }
    }
</script>

@endpush
