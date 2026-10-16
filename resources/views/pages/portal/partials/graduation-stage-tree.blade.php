<ul class="space-y-2">
    @foreach ($stages as $stage)
        <li class="rounded-md border p-3">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <span class="font-medium">{{ $stage['name'] }}</span>
                <span class="text-sm">{{ $stage['is_complete'] ? 'Complete' : 'In progress' }}</span>
            </div>
            @if ($stage['required_credits'] !== null)
                <p class="mt-1 text-sm text-muted-foreground">Requires {{ $stage['required_credits'] }} credits</p>
            @endif
            @if ($stage['stages'] !== [])
                <div class="mt-3 border-l pl-3">
                    @include('pages.portal.partials.graduation-stage-tree', ['stages' => $stage['stages']])
                </div>
            @endif
        </li>
    @endforeach
</ul>
