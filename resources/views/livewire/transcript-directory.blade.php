<div class="space-y-6">
    @can('create', App\Models\TranscriptSnapshot::class)
        <april:card>
            <slot:title>Issue a transcript</slot:title>
            <slot:description>Lifetime record of a learner’s latest published results. Issued transcripts stay fixed; corrections create a new revision.</slot:description>
            <slot:content>
                <form method="POST" action="{{ route('transcripts.store') }}" class="grid gap-4 lg:grid-cols-4 lg:items-end">
                    @csrf
                    <div class="flex min-w-0 flex-col gap-2">
                        <april:label for="transcript-student">Learner</april:label>
                        <april:native-select id="transcript-student" name="student_record_id" required class="w-full min-w-0">
                            <option value="">Choose a learner</option>
                            @foreach ($students as $student)
                                <option value="{{ $student->id }}" @selected(old('student_record_id') == $student->id)>
                                    {{ $student->user?->name ?? 'Unnamed' }} · {{ $student->admission_number }}
                                </option>
                            @endforeach
                        </april:native-select>
                        <x-field-error name="student_record_id" />
                    </div>

                    <div class="flex flex-col gap-2 lg:col-span-2">
                        <april:label for="transcript-reason">Reason for a revision</april:label>
                        <april:input id="transcript-reason" name="reason" value="{{ old('reason') }}" placeholder="Only needed when reissuing" />
                    </div>

                    <april:button type="submit">
                        <x-lucide-scroll-text class="mr-2 size-4" /> Issue transcript
                    </april:button>
                </form>
            </slot:content>
        </april:card>
    @endcan

    <april:card>
        <slot:title>Find a transcript</slot:title>
        <slot:description>Narrow the list to one learner.</slot:description>
        <slot:content>
            <div class="grid gap-4 lg:grid-cols-[minmax(0,2fr)_auto] lg:items-end">
                <div class="flex min-w-0 flex-col gap-2">
                    <april:label for="filter-student">Learner</april:label>
                    <select id="filter-student" wire:model.live="studentRecordId" class="h-10 w-full min-w-0 rounded-md border border-input bg-background px-3 text-sm">
                        <option value="">Every learner</option>
                        @foreach ($students as $student)
                            <option value="{{ $student->id }}">{{ $student->user?->name ?? 'Unnamed' }} · {{ $student->admission_number }}</option>
                        @endforeach
                    </select>
                </div>

                @if ($selectedStudent !== null)
                    <div>
                        <april:button type="button" variant="outline" wire:click="clearFilter">Clear filter</april:button>
                    </div>
                @endif
            </div>
            <p wire:loading class="mt-3 text-sm text-muted-foreground" role="status">Updating transcripts…</p>
        </slot:content>
    </april:card>

    <april:card>
        <slot:title>Issued transcripts</slot:title>
        <slot:description>Open a row to read the subjects the transcript carries.</slot:description>
        <slot:content>
            @if ($transcripts->isEmpty())
                @if ($selectedStudent !== null)
                    <x-empty-state icon="lucide-search-x" title="Nothing matches this filter" description="No transcript has been issued for that learner.">
                        <april:button type="button" variant="outline" wire:click="clearFilter">Show every transcript</april:button>
                    </x-empty-state>
                @else
                    <x-empty-state icon="lucide-scroll-text" title="No transcripts yet" description="Issue one above once a learner has at least one published subject result." />
                @endif
            @else
                <div x-data="{ open: null }" class="overflow-x-auto">
                    <table class="w-full min-w-[42rem] text-left text-sm">
                        <thead class="border-b border-border text-muted-foreground">
                            <tr>
                                <th scope="col" class="px-3 py-2">Learner</th>
                                <th scope="col" class="px-3 py-2">Revision</th>
                                <th scope="col" class="px-3 py-2">Subjects</th>
                                <th scope="col" class="px-3 py-2">Issued</th>
                                <th scope="col" class="px-3 py-2 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach ($transcripts as $transcript)
                                @php
                                    $results = $transcript->payload['results'] ?? [];
                                    $grouped = collect($results)->groupBy(fn (array $result): string => $result['academic_year'].' · '.$result['academic_period']);
                                @endphp
                                <tr wire:key="transcript-row-{{ $transcript->id }}">
                                    <td class="px-3 py-3 font-medium">
                                        {{ $transcript->studentRecord->user?->name ?? $transcript->studentRecord->admission_number }}
                                        <span class="block text-xs text-muted-foreground">{{ $transcript->studentRecord->admission_number }}</span>
                                    </td>
                                    <td class="px-3 py-3"><span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold">Revision {{ $transcript->revision }}</span></td>
                                    <td class="px-3 py-3">{{ count($results) }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-muted-foreground">{{ $transcript->issued_at->format('j M Y') }}</td>
                                    <td class="px-3 py-3 text-right">
                                        <button type="button" class="inline-flex h-9 items-center rounded-md border border-input bg-background px-3 text-sm font-medium hover:bg-accent" x-on:click="open = (open === {{ $transcript->id }} ? null : {{ $transcript->id }})" x-bind:aria-expanded="open === {{ $transcript->id }}" aria-controls="transcript-details-{{ $transcript->id }}">
                                            <span x-text="open === {{ $transcript->id }} ? 'Hide subjects' : 'Read subjects'">Read subjects</span>
                                        </button>
                                    </td>
                                </tr>
                                <tr id="transcript-details-{{ $transcript->id }}" wire:key="transcript-details-{{ $transcript->id }}" x-show="open === {{ $transcript->id }}" style="display: none">
                                    <td colspan="5" class="bg-muted/30 px-4 py-4">
                                        <div class="space-y-4">
                                            @if (filled($transcript->reason))
                                                <p class="text-sm text-muted-foreground">Reason for this revision: {{ $transcript->reason }}</p>
                                            @endif

                                            @foreach ($grouped as $heading => $groupResults)
                                                <div>
                                                    <p class="text-sm font-medium">{{ $heading }}</p>
                                                    <ul class="mt-2 divide-y divide-border rounded-md border border-border bg-background">
                                                        @foreach ($groupResults as $result)
                                                            <li class="flex items-center justify-between gap-4 px-3 py-2 text-sm">
                                                                <span>{{ $result['subject'] }}</span>
                                                                <span class="text-muted-foreground">{{ $result['percentage'] === null ? '—' : number_format($result['percentage'], 2).'%' }}</span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{ $transcripts->links('components.datatable-pagination-links-view') }}
            @endif
        </slot:content>
    </april:card>
</div>
