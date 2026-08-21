<div>
    <div class="fixed flex flex-col items-end top-0 right-0 w-screen lg:w-4/12" id="status-display">
        @if (session('danger'))
            <april:alert variant="destructive" dismissOnTimeout="true">
                <slot:icon><x-lucide-ban class="size-4" /></slot:icon>
                <slot:title>Danger</slot:title>
                <slot:description>{{ session('danger') }}</slot:description>
            </april:alert>
        @endif
        @if (session('success'))
            <april:alert dismissOnTimeout="true">
                <slot:icon><x-lucide-check class="size-4" /></slot:icon>
                <slot:title>Success</slot:title>
                <slot:description>{{ session('success') }}</slot:description>
            </april:alert>
        @endif
        @if (session('info'))
            <april:alert dismissOnTimeout="true">
                <slot:title>Info</slot:title>
                <slot:description>{{ session('info') }}</slot:description>
            </april:alert>
        @endif
        @if (session('status'))
            <april:alert dismissOnTimeout="true">
                <slot:icon><x-lucide-check class="size-4" /></slot:icon>
                <slot:title>Success</slot:title>
                <slot:description>{{ session('status') }}</slot:description>
            </april:alert>
        @endif
        <div x-data="{ showAlert: false }" x-show="showAlert" x-cloak>
            <april:alert variant="destructive">
                <slot:icon>
                    <span class="inline-flex items-center -space-x-1">
                        <x-lucide-signal class="size-4 rounded-full bg-background" />
                        <x-lucide-ban class="size-4 rounded-full bg-background" />
                    </span>
                </slot:icon>
                <slot:title>No Internet</slot:title>
                <slot:description @offline.window="showAlert = true" @online.window="showAlert = false">
                    Your device has gone offline
                </slot:description>
            </april:alert>
        </div>
    </div>
</div>
