@props(['errorBag' => 'default'])

@if ($errors->$errorBag->any())
    <april:alert variant="destructive" dismissable class="my-3" wire:ignore>
        <slot:title>Whoops! Something went wrong.</slot:title>
        <slot:description>
            <ul class="list-disc space-y-1 pl-5">
                @foreach ($errors->$errorBag->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </slot:description>
    </april:alert>
@endif
