@props(['grantable', 'model' => 'permissions'])

{{-- The permissions of one campus, grouped by the word they end with, which is
     the thing they are about. --}}
@php
    $groups = collect($grantable)->groupBy(function (string $permission): string {
        $words = explode(' ', $permission);
        array_shift($words);

        return $words === [] ? $permission : implode(' ', $words);
    })->sortKeys();
@endphp

<fieldset class="flex flex-col gap-3" {{ field_error_bindings($model) }}>
    <legend class="text-base font-semibold">What the role may do</legend>
    <p class="text-sm text-muted-foreground">Only what you can do yourself at this campus is listed.</p>

    @if ($groups->isEmpty())
        <p class="text-sm text-muted-foreground">You hold nothing at this campus that you could put in a role.</p>
    @else
        <div class="grid gap-x-6 gap-y-4 sm:grid-cols-2">
            @foreach ($groups as $subject => $permissions)
                <div class="flex flex-col">
                    <p class="text-xs font-medium uppercase text-muted-foreground">{{ $subject }}</p>
                    @foreach ($permissions as $permission)
                        <label class="flex min-h-11 select-none items-center gap-3 text-sm">
                            <input type="checkbox" wire:model="{{ $model }}" value="{{ $permission }}" class="size-4 rounded border-input">
                            <span>{{ $permission }}</span>
                        </label>
                    @endforeach
                </div>
            @endforeach
        </div>
    @endif
    <x-field-error :name="$model" />
    <x-field-error :name="$model.'.*'" />
</fieldset>
