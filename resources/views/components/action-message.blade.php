@props(['on'])
<div class="fixed flex flex-col items-end top-0 right-0 w-screen lg:w-4/12">
    <div class="w-full" role="alert" x-data="{ shown: false, timeout: null }"
        x-init="@this.on('{{ $on }}', () => { clearTimeout(timeout); shown = true; timeout = setTimeout(() => { shown = false }, 5000);  })"
        x-show.transition.opacity.out.duration.1500ms="shown"
        style="display: none;"
            {{ $attributes->merge(['class' => 'small']) }}>
        <april:alert dismissOnTimeout="true">
            <slot:icon><x-lucide-check class="size-4" /></slot:icon>
            <slot:title>Success</slot:title>
            <slot:description>{{ $slot->isEmpty() ? 'Saved.' : $slot }}</slot:description>
        </april:alert>
    </div>
</div>
