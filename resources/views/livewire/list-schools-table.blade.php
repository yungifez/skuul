<april:card><slot:title>All schools</slot:title><slot:description>Schools available in the platform.</slot:description><slot:content><div wire:key="{{ $id }}-{{ $this->tableRevision }}"><april:data-table id="{{ $id }}" :data="$data" :columns="$columns" :pagination="$pagination" :per-page-options="$perPageOptions" row-key="{{ $rowKey }}" :searchable="$searchable" @query-change="$wire.updateTable($event.detail)"><slot:empty><div class="space-y-1"><p class="font-medium text-foreground">No schools yet</p><p>Add the first campus to begin school operations.</p></div></slot:empty><slot:actions>
    <x-table-actions :items="array_filter([
        ['label' => 'View school', 'icon' => 'eye', 'url' => 'view_url'],
        $canEditSchools ? ['label' => 'Edit school', 'icon' => 'settings', 'url' => 'edit_url'] : null,
        $canDeleteSchools ? ['label' => 'Delete school', 'icon' => 'trash-2', 'url' => 'delete_url', 'type' => 'delete', 'confirm' => 'Delete this school?'] : null,
    ])" />
</slot:actions></april:data-table></div></slot:content></april:card>
