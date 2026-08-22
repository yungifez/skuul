<div class="card">
    <div class="card-header">
        <h4 class="card-title">Assign learners to parent</h4>
    </div>
    <div class="card-body">
        <x-display-validation-errors/>
        <p class="mb-5 text-sm text-muted-foreground">Choose a learner from their current home group. This does not change the learner's academic placement.</p>
        <form action="{{ route('parents.assign-student', $parent->id) }}" method="POST" class="grid gap-4 md:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto] md:items-end">
            <div class="flex w-full flex-col gap-2">
                <label for="academic-cycle-section" class="text-sm font-medium">Home group</label>
                <select id="academic-cycle-section" wire:model.live="academicCycleSectionId" class="h-10 rounded-md border border-input bg-background px-3 text-sm">
                    @forelse ($cycleSections as $cycleSection)
                        <option value="{{ $cycleSection['id'] }}">{{ $cycleSection['label'] }}</option>
                    @empty
                        <option value="">No active home groups in this academic cycle</option>
                    @endforelse
                </select>
            </div>
            <div class="flex w-full flex-col gap-2">
                <label for="student" class="text-sm font-medium">Learner</label>
                <select id="student" name="student_id" wire:model.live="studentId" @disabled($students === []) class="h-10 rounded-md border border-input bg-background px-3 text-sm disabled:cursor-not-allowed disabled:opacity-60">
                    @forelse ($students as $student)
                        <option value="{{ $student['id'] }}">{{ $student['name'] }}@if ($student['admission_number']) · {{ $student['admission_number'] }}@endif</option>
                    @empty
                        <option value="">No learners in this home group</option>
                    @endforelse
                </select>
            </div>
            @csrf
            <button type="submit" @disabled($studentId === null) class="inline-flex h-10 w-full items-center justify-center rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90 disabled:pointer-events-none disabled:opacity-50">
                Add learner
            </button>
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
                                <th class="p-4 border">Current home group</th>
                                <th class="p-4 border">Email</th>
                                <th class="p-4 border">
                                </th>
                            </tr>
                        </thead>
                        @foreach($children as $student)
                            <tr>
                                <td class="p-4 text-center border">{{$loop->iteration}}</td>
                                <td class="p-4 text-center border">{{ $student['name'] }}</td>
                                <td class="p-4 text-center border">{{ $student['cycle_section'] ?? 'Not currently placed' }}</td>
                                <td class="p-4 text-center border">{{ $student['email'] }}</td>
                                <td class="p-4 text-center border">
                                    <form action="{{route('parents.assign-student', $parent->id)}}" method="POST">
                                        <input type="hidden" name="student_id" value="{{ $student['id'] }}">
                                        <input type="hidden" name="assign" value="0">
                                        @csrf
                                        <april:button type="submit" class="w-full">
                                            Remove learner
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
