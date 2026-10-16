<div wire:key="{{ $id }}-{{ $this->tableRevision }}">
    <april:data-table id="{{ $id }}" :data="$data" :columns="$columns" :pagination="$pagination" :per-page-options="$perPageOptions" row-key="{{ $rowKey }}" :searchable="$searchable" @query-change="$wire.updateTable($event.detail)">
        <slot:empty>
            <div class="space-y-1">
                <p class="font-medium text-foreground">No subjects in the school catalogue yet</p>
                <p>Add a subject before assigning it to classes and reporting periods.</p>
            </div>
        </slot:empty>
        <slot:actions>
            <x-table-actions :items="array_filter([
                $canManageOfferings ? ['label' => 'Manage offerings', 'icon' => 'settings', 'url' => 'manage_url'] : null,
                $canCreateOfferings ? ['label' => 'Set up across levels', 'icon' => 'layers-3', 'url' => 'bulk_url'] : null,
                $canEditSubjects ? ['label' => 'Edit subject', 'icon' => 'pencil', 'url' => 'edit_url'] : null,
            ])" />
        </slot:actions>
    </april:data-table>
</div>
