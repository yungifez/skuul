<div class="space-y-6">
    <april:card>
        <slot:title class="flex flex-wrap items-center justify-between gap-3">
            <span>School administrators</span>
            @can('create admin')
                <april:button href="{{ route('admins.create') }}" wire:navigate>
                    <x-lucide-user-plus class="mr-2 size-4" />
                    Add administrator
                </april:button>
            @endcan
        </slot:title>
        <slot:description>People who can manage this school. Account access, invitations, and school membership are tracked independently.</slot:description>
        <slot:content>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                @foreach ([
                    ['label' => 'Total', 'value' => $totalAdmins, 'icon' => 'users', 'class' => 'bg-primary/10 text-primary'],
                    ['label' => 'Active', 'value' => $activeAdmins, 'icon' => 'circle-check', 'class' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'],
                    ['label' => 'Invited', 'value' => $invitedAdmins, 'icon' => 'mail', 'class' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400'],
                    ['label' => 'Suspended', 'value' => $suspendedAdmins, 'icon' => 'pause-circle', 'class' => 'bg-orange-500/10 text-orange-600 dark:text-orange-400'],
                    ['label' => 'Archived', 'value' => $archivedAdmins, 'icon' => 'archive', 'class' => 'bg-muted text-muted-foreground'],
                ] as $stat)
                    <div class="flex items-center gap-3 rounded-lg border p-3">
                        <div class="rounded-md p-2 {{ $stat['class'] }}">
                            <x-icon :name="'lucide-'.$stat['icon']" class="size-4" />
                        </div>
                        <div>
                            <p class="text-2xl font-semibold leading-none">{{ $stat['value'] }}</p>
                            <p class="mt-1 text-xs text-muted-foreground">{{ $stat['label'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </slot:content>
    </april:card>

    <april:card>
        <slot:title>Administrator directory</slot:title>
        <slot:description>Search the people with administrator access in the current school.</slot:description>
        <slot:content>
            <livewire:datatable :model="App\Models\User::class" uniqueId="admins-list-table" :filters="[['name' => 'role', 'arguments' => ['admin']], ['name' => 'ofSchool'], ['name' => 'orderBy', 'arguments' => ['name']]]" :columns="[
                ['type' => 'image', 'property' => 'profile_photo_url', 'img-class' => 'size-10 rounded-full object-cover'],
                ['property' => 'name'],
                ['property' => 'email'],
                ['property' => 'gender'],
                ['name' => 'Account', 'type' => 'account-status'],
                ['type' => 'dropdown', 'name' => 'actions', 'links' => [
                    ['href' => 'admins.edit', 'text' => 'Manage profile', 'icon' => 'pencil'],
                    ['href' => 'admins.show', 'text' => 'View profile', 'icon' => 'eye'],
                ]],
                ['type' => 'delete', 'name' => 'Delete', 'action' => 'admins.destroy']
            ]" />
        </slot:content>
    </april:card>
</div>
