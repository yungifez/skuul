<div class="mx-auto flex w-full max-w-3xl flex-col gap-10">
    @php
        $switchClasses = 'relative h-6 w-10 shrink-0 cursor-pointer appearance-none rounded-full transition-colors before:absolute before:left-0.5 before:top-0.5 before:size-5 before:rounded-full before:bg-background before:shadow before:transition-transform focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2';
    @endphp

    <p class="text-sm text-muted-foreground" id="features-on">{{ count(array_filter($enabled)) }} of {{ count($enabled) }} tools are on</p>

    @foreach ($groups as $group => $groupFeatures)
        <section aria-labelledby="group-{{ Str::slug($group) }}" class="flex flex-col gap-2">
            <h2 id="group-{{ Str::slug($group) }}" class="text-base font-semibold">{{ $group }}</h2>
            <ul class="divide-y border-y">
                @foreach ($groupFeatures as $feature)
                    @php($isOn = $enabled[$feature->value] ?? false)
                    <li wire:key="feature-{{ $feature->value }}">
                        <label for="feature-{{ $feature->value }}" class="flex min-h-11 cursor-pointer items-center justify-between gap-4 py-3 select-none">
                            <span class="min-w-0">
                                <span @class(['block font-medium', 'text-muted-foreground' => !$isOn])>{{ $feature->label() }}</span>
                                <span class="block text-sm text-muted-foreground">{{ $feature->description() }}</span>
                            </span>
                            <input type="checkbox" role="switch" id="feature-{{ $feature->value }}" wire:model.live="enabled.{{ $feature->value }}" @class([$switchClasses, 'bg-foreground before:translate-x-4' => $isOn, 'bg-input' => !$isOn])>
                        </label>
                        @if ($feature === \App\Enums\Feature::Portal && $isOn)
                            <fieldset class="mb-3 ml-4 border-l pl-4">
                                <legend class="mb-1 text-sm font-medium">Family pages</legend>
                                <ul class="divide-y">
                                    @foreach ($areas as $area)
                                        @php($isShown = $portalAreas[$area->value] ?? true)
                                        @php($needs = $areaNeeds[$area->value] ?? null)
                                        @php($isBlocked = $needs !== null && !($enabled[$needs->value] ?? false))
                                        <li wire:key="portal-area-{{ $area->value }}">
                                            <label for="portal-area-{{ $area->value }}" class="flex min-h-11 cursor-pointer items-center justify-between gap-4 py-2 select-none">
                                                <span class="min-w-0">
                                                    <span @class(['block text-sm', 'text-muted-foreground' => !$isShown || $isBlocked])>{{ $area->label() }}</span>
                                                    @if ($isBlocked)
                                                        <span class="block text-xs text-muted-foreground">Hidden while {{ $needs->label() }} is off</span>
                                                    @endif
                                                </span>
                                                <input type="checkbox" role="switch" id="portal-area-{{ $area->value }}" wire:model.live="portalAreas.{{ $area->value }}" @class([$switchClasses, 'bg-foreground before:translate-x-4' => $isShown, 'bg-input' => !$isShown])>
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                            </fieldset>
                        @endif
                    </li>
                @endforeach
            </ul>
        </section>
    @endforeach
</div>
