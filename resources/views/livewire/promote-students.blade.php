<div class="card">
    <div class="card-header">
        <h4 class="card-title">Promote student</h4>
    </div>
    <div class="card-body">
        <x-display-validation-errors/>
        {{--Form for selecting class--}}
        <form wire:submit="loadStudents" class="md:grid grid-cols-4 gap-2">
            <p class="font-bold col-span-4">Please select class and section</p>
            <div class="flex w-full flex-col gap-2">
                <april:label for="old-class">Old class</april:label>
                <april:select id="old-class" name="oldClass" wire:model.live="oldClass">
                @foreach ($classes as $class)
                    <option value="{{$class['id']}}">{{$class['name']}}</option>
                @endforeach

                </april:select>
                @error('oldClass')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex w-full flex-col gap-2">
                <april:label for="old-section">Old section</april:label>
                <april:select id="old-section" name="oldSection" wire:model.live="oldSection">
                @isset($oldSections)
                    @foreach ($oldSections as $section)
                        <option value="{{$section['id']}}">{{$section['name']}}</option>
                    @endforeach
                @endisset

                </april:select>
                @error('oldSection')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex w-full flex-col gap-2">
                <april:label for="new-class">New class</april:label>
                <april:select id="new-class" name="newClass" wire:model.live="newClass">
                @foreach ($classes as $class)
                    <option value="{{$class['id']}}">{{$class['name']}}</option>
                @endforeach

                </april:select>
                @error('newClass')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex w-full flex-col gap-2">
                <april:label for="new-section">New section</april:label>
                <april:select id="new-section" name="newSection" wire:model.live="newSection">
                @isset($newSections)
                    @foreach ($newSections as $section)
                        <option value="{{$section['id']}}">{{$section['name']}}</option>
                    @endforeach
                @endisset

                </april:select>
                @error('newSection')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>
            <april:button class="w-full  " type="submit">
                <x-lucide-key class="mr-2 size-4" />
                Fetch students
            </april:button>
        </form>
        <div wire:loading.remove.delay>
            <form action="{{ route('students.promote') }}" method="post" class="my-3 space-y-4">
                <div class="flex flex-wrap gap-2">
                    <april:button @click="setAllSelectsToPromote()" type="button">
                        Set all to promote
                    </april:button>
                    <april:button @click="setAllSelectsToDontPromote()" type="button">
                        Set all to keep
                    </april:button>
                </div>

                <input type="hidden" name="old_class_id" value="{{ $oldClass }}">
                <input type="hidden" name="old_section_id" value="{{ $oldSection }}">
                <input type="hidden" name="new_class_id" value="{{ $newClass }}">
                <input type="hidden" name="new_section_id" value="{{ $newSection }}">

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b text-muted-foreground">
                                <th class="p-3">Student</th>
                                <th class="p-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($students ?? [] as $student)
                                <tr class="border-b">
                                    <td class="p-3">{{ $student->name }} <span class="text-muted-foreground">· {{ $student->studentRecord->admission_number }}</span></td>
                                    <td class="p-3">
                                        <april:select name="student_id[]" id="student-{{ $student->id }}" class="promote">
                                            <option value="{{ $student->id }}">Promote</option>
                                            <option value="">Keep in current class</option>
                                        </april:select>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="p-6 text-center text-muted-foreground">
                                        Fetch students to review promotion choices.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @csrf
                <april:button class="w-full md:w-auto" type="submit">
                    <x-lucide-arrow-up-right class="mr-2 size-4" />
                    Promote selected students
                </april:button>
            </form>
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
