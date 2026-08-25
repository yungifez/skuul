<div class="space-y-6">
    <april:card>
        <slot:title>School administrators</slot:title>
        <slot:description>People who can manage this school.</slot:description>
        <slot:content>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                @foreach ([['label' => 'Total', 'value' => $totalAdmins, 'icon' => 'users', 'class' => 'bg-primary/10 text-primary'], ['label' => 'Active', 'value' => $activeAdmins, 'icon' => 'circle-check', 'class' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'], ['label' => 'Invited', 'value' => $invitedAdmins, 'icon' => 'mail', 'class' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400'], ['label' => 'Suspended', 'value' => $suspendedAdmins, 'icon' => 'pause-circle', 'class' => 'bg-orange-500/10 text-orange-600 dark:text-orange-400'], ['label' => 'Archived', 'value' => $archivedAdmins, 'icon' => 'archive', 'class' => 'bg-muted text-muted-foreground']] as $stat)
                    <div class="flex items-center gap-3 rounded-lg border p-3"><div class="rounded-md p-2 {{ $stat['class'] }}"><x-icon :name="'lucide-'.$stat['icon']" class="size-4" /></div><div><p class="text-2xl font-semibold leading-none">{{ $stat['value'] }}</p><p class="mt-1 text-xs text-muted-foreground">{{ $stat['label'] }}</p></div></div>
                @endforeach
            </div>
        </slot:content>
    </april:card>
    <april:card>
        <slot:title>Administrator directory</slot:title>
        <slot:description>Search administrators in the current school.</slot:description>
        <slot:content>
            <div wire:key="{{ $id }}-{{ $this->tableRevision }}">
                <april:data-table id="{{ $id }}" :data="$data" :columns="$columns" :pagination="$pagination" :per-page-options="$perPageOptions" row-key="{{ $rowKey }}" :searchable="$searchable" @query-change="$wire.updateTable($event.detail)">
                    <slot:empty><div class="space-y-1"><p class="font-medium text-foreground">No administrators yet</p><p>Add the first administrator for this school.</p></div></slot:empty>
                    <slot:cell-account-status><span class="capitalize" x-text="row.account_status ?? ''"></span></slot:cell-account-status>
                    <slot:actions>
    <x-table-actions :items="array_filter([
        ['label' => 'View administrator', 'icon' => 'eye', 'url' => 'view_url'],
        $canManageAdmins ? ['label' => 'Manage administrator', 'icon' => 'pencil', 'url' => 'edit_url'] : null,
        $canDeleteAdmins ? ['label' => 'Delete administrator', 'icon' => 'trash-2', 'url' => 'delete_url', 'type' => 'delete', 'confirm' => 'Delete this administrator?'] : null,
    ])" />
</slot:actions>
                </april:data-table>
            </div>
        </slot:content>
    </april:card>
</div>
