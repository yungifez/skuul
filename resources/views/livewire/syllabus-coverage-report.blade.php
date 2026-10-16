<div>
    <april:card>
        <slot:title>Syllabus coverage</slot:title>
        <slot:description>Each class against its published plan. A topic planned for a past week counts as behind until it is covered or skipped.</slot:description>
        <slot:content>
            <div class="mb-4 flex flex-wrap items-end gap-4">
                <div class="flex flex-col gap-2">
                    <label for="coverage-period" class="text-sm font-medium">{{ school_term('period', 'Academic period') }}</label>
                    <select id="coverage-period" wire:model.live="academicPeriodId" class="h-10 rounded-md border border-input bg-background px-3 text-sm">
                        @foreach ($periods as $period)
                            <option value="{{ $period->id }}">{{ $period->academicYear?->name }} · {{ $period->label ?? $period->name }}</option>
                        @endforeach
                    </select>
                </div>
                <label class="flex h-10 items-center gap-2 text-sm">
                    <input type="checkbox" wire:model.live="onlyBehind" class="rounded border-input">
                    Only classes behind plan
                </label>
                <april:button type="button" variant="outline" wire:click="export" class="ml-auto">Download CSV</april:button>
            </div>

            @if ($rows->isEmpty())
                <p class="text-sm text-muted-foreground">No published syllabus in this {{ strtolower(school_term('period', 'period')) }}{{ $onlyBehind ? ' is behind plan' : ' yet' }}.</p>
            @else
                <div class="relative overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b text-muted-foreground">
                            <tr>
                                <th scope="col" class="px-2 py-2">Subject</th>
                                <th scope="col" class="px-2 py-2">Class</th>
                                <th scope="col" class="px-2 py-2">Covered</th>
                                <th scope="col" class="px-2 py-2">Behind</th>
                                <th scope="col" class="px-2 py-2"><span class="sr-only">Open</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($rows as $row)
                                <tr wire:key="coverage-row-{{ $row['syllabus']->id }}-{{ $row['id'] ?? 'all' }}">
                                    <td class="px-2 py-2 font-medium">{{ $row['syllabus']->courseOffering->subject->name }}</td>
                                    <td class="px-2 py-2">{{ $row['class'] }}</td>
                                    <td class="px-2 py-2">
                                        <div class="flex items-center gap-2">
                                            <div class="h-2 w-24 overflow-hidden rounded-full bg-muted" aria-hidden="true">
                                                <div class="h-full bg-primary" style="width: {{ $row['percent'] }}%"></div>
                                            </div>
                                            <span>{{ $row['covered'] }} of {{ $row['total'] }}</span>
                                        </div>
                                    </td>
                                    <td @class(['px-2 py-2', 'font-semibold text-destructive' => $row['behind'] > 0, 'text-muted-foreground' => $row['behind'] === 0])>
                                        {{ $row['behind'] > 0 ? $row['behind'].' '.\Illuminate\Support\Str::plural('topic', $row['behind']) : 'On track' }}
                                    </td>
                                    <td class="px-2 py-2 text-right">
                                        <a class="font-medium underline" href="{{ route('syllabi.show', $row['id'] === null ? $row['syllabus'] : [$row['syllabus'], 'class' => $row['id']]) }}">Open<span class="sr-only"> {{ $row['syllabus']->courseOffering->subject->name }} for {{ $row['class'] }}</span></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </slot:content>
    </april:card>
</div>
