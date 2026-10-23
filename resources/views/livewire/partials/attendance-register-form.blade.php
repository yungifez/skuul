{{-- The list of one open register. Shared by the daily and the lesson register. --}}
@php
    $counts = collect($students)->groupBy(fn ($student) => $statusesByStudent[$student->id] ?? \App\Enums\AttendanceStatus::Present->value)->map->count();
    $otherCount = collect([\App\Enums\AttendanceStatus::LeftEarly, \App\Enums\AttendanceStatus::Remote, \App\Enums\AttendanceStatus::SchoolActivity, \App\Enums\AttendanceStatus::NotRecorded])->sum(fn ($status) => $counts->get($status->value, 0));
@endphp

<form wire:submit="save">
    <april:card>
        <slot:title>{{ $registerTitle }}</slot:title>
        <slot:description>{{ $students->count() }} {{ Str::plural('learner', $students->count()) }} · {{ \Illuminate\Support\Carbon::parse($attendedOn)->format('j F Y') }}</slot:description>
        <slot:content>
            <div class="flex flex-wrap items-center gap-2 border-b border-border pb-4" aria-live="polite" aria-label="Attendance totals">
                <april:badge variant="secondary">Present <span class="ml-1 font-bold">{{ $counts->get('present', 0) }}</span></april:badge>
                <april:badge variant="destructive">Absent <span class="ml-1 font-bold">{{ $counts->get('absent', 0) }}</span></april:badge>
                <april:badge variant="outline">Late <span class="ml-1 font-bold">{{ $counts->get('late', 0) }}</span></april:badge>
                <april:badge variant="outline">Excused <span class="ml-1 font-bold">{{ $counts->get('excused', 0) }}</span></april:badge>
                <april:badge variant="outline">Other <span class="ml-1 font-bold">{{ $otherCount }}</span></april:badge>
            </div>

            @if ($canTakeAttendance)
                <div class="flex flex-wrap gap-2 py-4">
                    <span class="mr-auto self-center text-sm text-muted-foreground">Quick mark</span>
                    <april:button type="button" variant="outline" wire:click="markAll('present')">Mark everybody present</april:button>
                    <april:button type="button" variant="outline" wire:click="markAll('absent')">Mark everybody absent</april:button>
                </div>
            @endif

            <div class="hidden overflow-x-auto md:block">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-border text-muted-foreground">
                            <th scope="col" class="px-3 py-3 font-medium">Learner</th>
                            <th scope="col" class="px-3 py-3 font-medium">Admission number</th>
                            <th scope="col" class="w-60 px-3 py-3 font-medium">Attendance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students as $student)
                            <tr wire:key="attendance-row-{{ $student->id }}" class="border-b border-border last:border-0">
                                <td class="px-3 py-3 font-medium">{{ $student->user?->name ?? 'Unnamed learner' }}</td>
                                <td class="px-3 py-3 text-muted-foreground">{{ $student->admission_number ?? '—' }}</td>
                                <td class="px-3 py-3">
                                    <select id="attendance-{{ $student->id }}" wire:model.live="statusesByStudent.{{ $student->id }}" aria-label="Attendance for {{ $student->user?->name ?? $student->admission_number }}" class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm" {{ ! $canTakeAttendance ? 'disabled' : '' }}>
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status->value }}">{{ $status->label() }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="divide-y divide-border md:hidden">
                @foreach ($students as $student)
                    <article wire:key="attendance-mobile-{{ $student->id }}" class="space-y-3 py-4 first:pt-4 last:pb-1">
                        <div class="flex min-w-0 items-start justify-between gap-3">
                            <p class="truncate font-medium">{{ $student->user?->name ?? 'Unnamed learner' }}</p>
                            <span class="shrink-0 text-xs text-muted-foreground">{{ $student->admission_number ?? '—' }}</span>
                        </div>
                        <label for="attendance-mobile-{{ $student->id }}" class="text-xs font-medium text-muted-foreground">Attendance</label>
                        <select id="attendance-mobile-{{ $student->id }}" wire:model.live="statusesByStudent.{{ $student->id }}" aria-label="Attendance for {{ $student->user?->name ?? $student->admission_number }}" class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm" {{ ! $canTakeAttendance ? 'disabled' : '' }}>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}">{{ $status->label() }}</option>
                            @endforeach
                        </select>
                    </article>
                @endforeach
            </div>

            @error('statusesByStudent')
                <p class="mt-3 text-sm text-destructive">{{ $message }}</p>
            @enderror
        </slot:content>
        @if ($canTakeAttendance)
            <slot:footer>
                <april:button type="submit" wire:loading.attr="disabled" wire:target="save">
                    <x-lucide-save class="mr-2 size-4" />
                    <span wire:loading.remove wire:target="save">Save register</span>
                    <span wire:loading wire:target="save">Saving…</span>
                </april:button>
            </slot:footer>
        @endif
    </april:card>
</form>
