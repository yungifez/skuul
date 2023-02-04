@props(['submit'])

<div {{ $attributes->merge(['class' => 'md:grid grid-rows-1 grid-cols-12 gap-2']) }}>
    <div class="col-span-4">
        <h2 class="text-xl font-bold my-2">{{$title}}</h2>
        <p class="text-sm">{{$description}}</p>
    </div>
    <div class="col-span-8 card shadow-sm">
        <form wire:submit.prevent="{{ $submit }}">
            <div class="card-body">
            {{ $form }}
            </div>

            @if (isset($actions))
                <div class="border-t bg-slate-100 dark:bg-gray-800 px-3 flex justify-end ">
                    {{ $actions }}
                </div>
            @endif
        </form>
    </div>
</div>
