<ul class="space-y-2 {{ $depth > 0 ? 'mt-2 border-l pl-4' : '' }}">
    @foreach ($stages as $stage)
        <li>
            <div class="flex flex-wrap items-center justify-between gap-2 rounded-md border bg-muted/20 px-3 py-2">
                <div class="min-w-0">
                    <a href="{{ route('graduation-plans.show', $stage) }}" class="font-medium underline-offset-4 hover:underline">
                        {{ $stage->name }}
                    </a>
                    <p class="text-xs text-muted-foreground">
                        @if ($stage->completion_operator === 'any')
                            Any item (OR)
                        @elseif ($stage->completion_operator === 'at_least')
                            At least {{ $stage->required_count }} items
                        @elseif ($stage->completion_operator === 'at_least_credits')
                            At least {{ $stage->required_credits }} credits
                        @else
                            All items (AND)
                        @endif
                        @if ($stage->is_negated)
                            · NOT this stage
                        @endif
                    </p>
                </div>
                <span class="text-xs text-muted-foreground">
                    {{ $stage->requirements->count() }} {{ Str::plural('requirement', $stage->requirements->count()) }}
                    · {{ $stage->children->count() }} {{ Str::plural('nested stage', $stage->children->count()) }}
                </span>
            </div>

            @if ($stage->children->isNotEmpty())
                @include('pages.graduation-plan.partials.stage-tree', ['stages' => $stage->children, 'depth' => $depth + 1])
            @endif
        </li>
    @endforeach
</ul>
