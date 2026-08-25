<div>
    <div
        id="status-display"
        class="pointer-events-none fixed right-0 top-4 z-[60] flex w-full max-w-md flex-col gap-3 p-4"
        x-data="{
            notifications: @js($notifications),
            nextId: {{ count($notifications) }},
            add(notification) {
                if (!notification?.message) {
                    return;
                }

                const item = {
                    id: ++this.nextId,
                    type: notification.type ?? 'success',
                    title: notification.type === 'danger' ? 'Action failed' : (notification.type === 'info' ? 'Info' : 'Success'),
                    message: notification.message,
                };

                this.notifications.push(item);
                this.schedule(item.id);
            },
            schedule(id) {
                setTimeout(() => this.remove(id), 5000);
            },
            remove(id) {
                this.notifications = this.notifications.filter((notification) => notification.id !== id);
            },
        }"
        x-init="notifications.forEach((notification) => schedule(notification.id))"
        x-on:status-message.window="add($event.detail)"
    >
        <template x-for="notification in notifications" :key="notification.id">
            <div
                x-cloak
                x-show="true"
                x-transition
                role="alert"
                class="pointer-events-auto relative flex w-full gap-x-3 rounded-lg border border-slate-700 bg-slate-950 p-4 text-white shadow-lg"
                :class="notification.type === 'danger' ? 'border-red-400/70' : 'border-slate-700'"
            >
                <div class="flex items-start pt-0.5">
                    <span class="flex size-4 items-center justify-center rounded-full border text-xs font-bold" x-text="notification.type === 'danger' ? '!' : '✓'"></span>
                </div>
                <div class="w-full">
                    <h5 class="mb-1 font-medium leading-none tracking-tight" x-text="notification.title"></h5>
                    <div class="text-sm" x-text="notification.message"></div>
                </div>
                <button type="button" class="absolute right-4 top-4 rounded-sm opacity-70 transition-opacity hover:opacity-100" aria-label="Dismiss notification" x-on:click="remove(notification.id)">
                    <x-lucide-x class="size-4" />
                </button>
            </div>
        </template>

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
