<april:card><slot:title>Exam slots in {{ $exam->name }}</slot:title><slot:description>Define the assessed parts of this exam.</slot:description><slot:content><div wire:key="{{ $id }}-{{ $this->tableRevision }}"><april:data-table id="{{ $id }}" :data="$data" :columns="$columns" :pagination="$pagination" :per-page-options="$perPageOptions" row-key="{{ $rowKey }}" :searchable="$searchable" @query-change="$wire.updateTable($event.detail)"><slot:empty><div class="space-y-1"><p class="font-medium text-foreground">No exam slots yet</p><p>Add the first assessment slot.</p></div></slot:empty><slot:actions>
    <x-table-actions :items="array_filter([
        $canEditSlots ? ['label' => 'Edit slot', 'icon' => 'settings', 'url' => 'edit_url'] : null,
        $canDeleteSlots ? ['label' => 'Delete slot', 'icon' => 'trash-2', 'url' => 'delete_url', 'type' => 'delete', 'confirm' => 'Delete this exam slot?'] : null,
    ])" />
</slot:actions></april:data-table></div></slot:content></april:card>
