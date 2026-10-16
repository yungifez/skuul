<div>
    <april:card>
        <slot:title>Coverage</slot:title>
        <slot:description>What each class has actually been taught. Topics planned for past weeks that are not covered or skipped count as behind.</slot:description>
        <slot:content>
            <div class="mb-4 flex flex-wrap items-end gap-4">
                @if (count($tracks) > 1)
                    <div class="flex flex-col gap-2">
                        <label for="coverage-track" class="text-sm font-medium">{{ school_term('section', 'Section') }}</label>
                        <select id="coverage-track" wire:model.live="track" class="h-10 rounded-md border border-input bg-background px-3 text-sm">
                            @foreach ($tracks as $option)
                                <option value="{{ $option['id'] }}">{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                @if ($summary)
                    <p class="text-sm" id="coverage-summary">
                        <span class="font-semibold">{{ $summary['covered'] }} of {{ $summary['total'] }}</span> topics covered ({{ $summary['percent'] }}%)
                        @if ($summary['behind'] > 0)
                            · <span class="font-semibold text-destructive">{{ $summary['behind'] }} behind plan</span>
                        @elseif ($summary['expected'] > 0)
                            · <span class="text-muted-foreground">on track</span>
                        @endif
                    </p>
                @endif
            </div>

            <div class="relative overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b text-muted-foreground">
                        <tr>
                            <th scope="col" class="px-2 py-2">Week</th>
                            <th scope="col" class="px-2 py-2">Topic</th>
                            <th scope="col" class="px-2 py-2">Status</th>
                            <th scope="col" class="px-2 py-2">Taught on</th>
                            <th scope="col" class="px-2 py-2">Note</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach ($topics as $topic)
                            @php($record = $coverages->get($topic->id))
                            <tr wire:key="coverage-{{ $track }}-{{ $topic->id }}">
                                <td class="px-2 py-2 text-muted-foreground">{{ $topic->week ?? '—' }}</td>
                                <td class="px-2 py-2 font-medium">{{ $topic->title }}</td>
                                <td class="px-2 py-2">
                                    @if ($canRecord)
                                        <select aria-label="Coverage of {{ $topic->title }}" wire:change="mark({{ $topic->id }}, $event.target.value)" class="h-10 rounded-md border border-input bg-background px-2 text-sm">
                                            <option value="" @selected($record === null)>Not yet taught</option>
                                            @foreach ($statuses as $status)
                                                <option value="{{ $status->value }}" @selected($record?->status === $status)>{{ $status->label() }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        {{ $record?->status->label() ?? 'Not yet taught' }}
                                    @endif
                                </td>
                                <td class="px-2 py-2 text-muted-foreground">{{ $record?->covered_on?->format('M j') ?? '—' }}</td>
                                <td class="px-2 py-2">
                                    @if ($canRecord && $record !== null)
                                        <input type="text" maxlength="1000" value="{{ $record->note }}" aria-label="Note on {{ $topic->title }}" placeholder="Eg: carried over to week 6"
                                            wire:change="saveNote({{ $topic->id }}, $event.target.value)"
                                            class="h-10 w-full min-w-40 rounded-md border border-input bg-background px-2 text-sm">
                                    @else
                                        <span class="text-muted-foreground">{{ $record?->note ?? '—' }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </slot:content>
    </april:card>
</div>
