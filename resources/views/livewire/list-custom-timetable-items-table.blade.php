<april:card>
    <slot:title>Custom timetable items</slot:title>
    <slot:description>Reusable activities for timetable planning.</slot:description>
    <slot:content><div wire:key="{{ $id }}-{{ $this->tableRevision }}"><april:data-table id="{{ $id }}" :data="$data" :columns="$columns" :pagination="$pagination" :per-page-options="$perPageOptions" row-key="{{ $rowKey }}" :searchable="$searchable" @query-change="$wire.updateTable($event.detail)"><slot:empty><div class="space-y-1"><p class="font-medium text-foreground">No timetable items yet</p><p>Add reusable items for timetable planning.</p></div></slot:empty><slot:actions>
    <x-table-actions :items="array_filter([
        $canEditItems ? ['label' => 'Edit item', 'icon' => 'settings', 'url' => 'edit_url'] : null,
        $canDeleteItems ? ['label' => 'Delete item', 'icon' => 'trash-2', 'url' => 'delete_url', 'type' => 'delete', 'confirm' => 'Delete this timetable item?'] : null,
    ])" />
</slot:actions></april:data-table></div></slot:content>
</april:card>
