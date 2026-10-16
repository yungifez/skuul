@props(['on'])
<div class="pointer-events-none fixed inset-x-0 top-4 z-50 flex justify-center px-4 sm:justify-end">
    <div class="pointer-events-auto w-full max-w-md" role="alert" x-data="{ shown: false, timeout: null }"
        x-init="@this.on('{{ $on }}', () => { clearTimeout(timeout); shown = true; timeout = setTimeout(() => { shown = false }, 5000);  })"
        x-show.transition.opacity.out.duration.1500ms="shown"
        style="display: none;"
            {{ $attributes }}>
        <april:alert dismissOnTimeout="true">
            <slot:icon><x-lucide-check class="size-4" /></slot:icon>
            <slot:title>Success</slot:title>
            <slot:description>{{ $slot->isEmpty() ? 'Saved.' : $slot }}</slot:description>
        </april:alert>
    </div>
</div>
