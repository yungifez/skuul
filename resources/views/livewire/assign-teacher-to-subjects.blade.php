<div class="card">
    <div class="card-header">
        <h4 class="card-title">Assign a teacher to catalog subjects</h4>
    </div>
    <div class="card-body space-y-6">
        <p class="text-sm text-muted-foreground">This assigns a teacher to a subject generally. For a specific cycle, period, or home group, assign the teacher from the course offering instead.</p>
        <div class="grid gap-4 md:grid-cols-[minmax(0,1fr)_auto] md:items-end">
            <div class="flex flex-col gap-2">
                <label for="teacher-id" class="text-sm font-medium">Teacher</label>
                <select id="teacher-id" wire:model="teacherId" class="h-10 rounded-md border border-input bg-background px-3 text-sm">
                    @forelse ($teachers as $teacher)
                        <option value="{{ $teacher['id'] }}">{{ $teacher['name'] }}</option>
                    @empty
                        <option value="">No teachers in this school</option>
                    @endforelse
                </select>
            </div>
            <button type="button" wire:click="loadSubjects" @disabled($teacherId === null) class="inline-flex h-10 items-center justify-center rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground disabled:pointer-events-none disabled:opacity-50">Choose subjects</button>
        </div>

        @if ($teacherStateId !== null)
            <form action="{{ route('subjects.assign-teacher-to-subject', $teacherStateId) }}" method="POST" class="space-y-4">
                @csrf
                <fieldset>
                    <legend class="text-sm font-medium">Subjects for this teacher</legend>
                    <div class="mt-3 grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                        @forelse ($subjects as $subject)
                            <label class="flex items-center gap-3 rounded-md border border-border p-3 text-sm">
                                <input type="checkbox" name="subjects[]" value="{{ $subject['id'] }}" @checked($subject['assigned']) class="rounded border-input text-primary focus:ring-primary">
                                <span>{{ $subject['name'] }} <span class="text-muted-foreground">{{ $subject['short_name'] }}</span></span>
                            </label>
                        @empty
                            <p class="text-sm text-muted-foreground">No catalog subjects exist yet.</p>
                        @endforelse
                    </div>
                </fieldset>
                <april:button type="submit">Save subject assignments</april:button>
            </form>
        @endif
    </div>
</div>
