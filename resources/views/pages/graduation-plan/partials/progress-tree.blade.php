<ul class="space-y-2 {{ $depth > 0 ? 'mt-2 border-l pl-4' : '' }}">
    @foreach ($stages as $stage)
        <li>
            <div class="flex flex-wrap items-center justify-between gap-2 rounded-md border px-3 py-2">
                <div>
                    <a href="{{ route('graduation-plans.show', $stage['plan_id']) }}" class="font-medium underline-offset-4 hover:underline">
                        {{ $stage['name'] }}
                    </a>
                    <p class="text-xs text-muted-foreground">
                        @if ($stage['operator'] === 'any')
                            Any item (OR)
                        @elseif ($stage['operator'] === 'at_least')
                            At least {{ $stage['required_count'] }} items
                        @elseif ($stage['operator'] === 'at_least_credits')
                            At least {{ $stage['required_credits'] }} credits
                        @else
                            All items (AND)
                        @endif
                        @if ($stage['is_negated'])
                            · NOT this stage
                        @endif
                    </p>
                </div>
                <span class="text-sm font-medium {{ $stage['is_complete'] ? 'text-emerald-700 dark:text-emerald-300' : 'text-muted-foreground' }}">
                    {{ $stage['is_complete'] ? 'Complete' : 'Not complete' }}
                </span>
            </div>

            @if ($stage['stages'] !== [])
                @include('pages.graduation-plan.partials.progress-tree', ['stages' => $stage['stages'], 'depth' => $depth + 1])
            @endif
        </li>
    @endforeach
</ul>
