<div class="card">
    <div class="card-header">
        <div class="card-title">Timetables</div>
    </div>
    <div class="card-body space-y-5">
        @if (! $isStudent)
            <div class="flex max-w-xl flex-col gap-2">
                <label for="academic-cycle-section" class="text-sm font-medium">Home group</label>
                <select id="academic-cycle-section" wire:model.live="academicCycleSectionId" class="h-10 rounded-md border border-input bg-background px-3 text-sm">
                    @forelse ($cycleSections as $cycleSection)
                        <option value="{{ $cycleSection['id'] }}">{{ $cycleSection['label'] }}</option>
                    @empty
                        <option value="">No active home groups in this academic cycle</option>
                    @endforelse
                </select>
            </div>
        @endif

        @if ($academicCycleSectionId === null)
            <x-empty-state title="No home group selected" description="Set up an active home group for the current academic cycle before creating a timetable." />
        @elseif ($timetables === [])
            <x-empty-state title="No timetable yet" description="Create a draft timetable for this home group, then add lessons and publish it when it is ready." />
        @else
            <div class="overflow-x-auto rounded-lg border">
                <table class="w-full text-sm">
                    <thead class="bg-muted/60 text-left text-muted-foreground">
                        <tr>
                            <th class="px-4 py-3 font-medium">Timetable</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Published</th>
                            <th class="px-4 py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach ($timetables as $timetable)
                            <tr>
                                <td class="px-4 py-3"><p class="font-medium">{{ $timetable['name'] }}</p>@if ($timetable['description'])<p class="mt-1 text-muted-foreground">{{ $timetable['description'] }}</p>@endif</td>
                                <td class="px-4 py-3">{{ $timetable['status'] }}</td>
                                <td class="px-4 py-3">{{ $timetable['published_at'] ?? 'Not published' }}</td>
                                <td class="px-4 py-3 text-right"><a href="{{ route('timetables.show', $timetable['id']) }}" class="text-primary underline-offset-4 hover:underline">View</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
