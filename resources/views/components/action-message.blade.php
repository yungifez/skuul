@props(['on'])
<div class="fixed flex flex-col items-end top-0 right-0 w-screen lg:w-4/12">
    <div class="bg-green-600 p-3 text-white rounded w-full" role="alert" x-data="{ shown: false, timeout: null }"
        x-init="@this.on('{{ $on }}', () => { clearTimeout(timeout); shown = true; timeout = setTimeout(() => { shown = false }, 5000);  })"
        x-show.transition.opacity.out.duration.1500ms="shown"
        style="display: none;"
            {{ $attributes->merge(['class' => 'small']) }}>
        <div class="alert-body">
            {{ $slot->isEmpty() ? 'Saved.' : $slot }}
        </div>
    </div>
</div>
