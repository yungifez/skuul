<april:card><slot:title>Fee categories</slot:title><slot:description>Group fees into clear categories for billing.</slot:description><slot:content><div wire:key="{{ $id }}-{{ $this->tableRevision }}"><april:data-table id="{{ $id }}" :data="$data" :columns="$columns" :pagination="$pagination" :per-page-options="$perPageOptions" row-key="{{ $rowKey }}" :searchable="$searchable" @query-change="$wire.updateTable($event.detail)"><slot:empty><div class="space-y-1"><p class="font-medium text-foreground">No fee categories yet</p><p>Create a category to organize fees.</p></div></slot:empty><slot:actions>
    <x-table-actions :items="array_filter([
        $canEditCategories ? ['label' => 'Edit category', 'icon' => 'settings', 'url' => 'edit_url'] : null,
        $canDeleteCategories ? ['label' => 'Delete category', 'icon' => 'trash-2', 'url' => 'delete_url', 'type' => 'delete', 'confirm' => 'Delete this fee category?'] : null,
    ])" />
</slot:actions></april:data-table></div></slot:content></april:card>
