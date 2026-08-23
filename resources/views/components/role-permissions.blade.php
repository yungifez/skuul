@props(['grantable', 'held' => []])

{{-- The permissions of one campus, grouped by the word they end with, which is
     the thing they are about. --}}
@php
    $groups = collect($grantable)->groupBy(function (string $permission): string {
        $words = explode(' ', $permission);
        array_shift($words);

        return $words === [] ? $permission : implode(' ', $words);
    })->sortKeys();
    $held = collect($held)->all();
@endphp

<div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
    <div class="flex flex-col gap-1.5 border-b p-6">
        <h3 class="text-lg font-semibold leading-none tracking-tight">What the role may do</h3>
        <p class="text-sm text-muted-foreground">Only what you can do yourself is listed.</p>
    </div>

    @if ($groups->isEmpty())
        <p class="p-6 text-sm text-muted-foreground">You hold nothing at this campus that you could put in a role.</p>
    @else
        <div class="grid gap-6 p-6 sm:grid-cols-2">
            @foreach ($groups as $subject => $permissions)
                <div class="flex flex-col gap-2">
                    <p class="text-xs font-medium uppercase tracking-wider text-muted-foreground">{{ $subject }}</p>
                    @foreach ($permissions as $permission)
                        <label class="flex items-start gap-2 text-sm">
                            <input type="checkbox" name="permissions[]" value="{{ $permission }}" class="mt-0.5 size-4"
                                @checked(in_array($permission, $held, true))>
                            <span>{{ $permission }}</span>
                        </label>
                    @endforeach
                </div>
            @endforeach
        </div>
    @endif
</div>
