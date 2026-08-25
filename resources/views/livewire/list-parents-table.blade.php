<april:card>
    <slot:title>Parents</slot:title>
    <slot:description>Search and manage the parents who belong to this school.</slot:description>
    <slot:content>
        <div class="space-y-6">
            <x-display-validation-errors />

            <div wire:loading class="text-sm text-muted-foreground">Updating parents...</div>

            <div wire:key="{{ $id }}-{{ $this->tableRevision }}">
                <april:data-table
                    id="{{ $id }}"
                    :data="$data"
                    :columns="$columns"
                    :pagination="$pagination"
                    :per-page-options="$perPageOptions"
                    row-key="{{ $rowKey }}"
                    :searchable="$searchable"
                    @query-change="$wire.updateTable($event.detail)"
                >
                    <slot:empty>
                        <div class="space-y-1">
                            <p class="font-medium text-foreground">No parents yet</p>
                            <p>Add the first parent for this school.</p>
                        </div>
                    </slot:empty>

                    <slot:cell-account-status>
                        <span class="capitalize" x-text="row.account_status ?? ''"></span>
                    </slot:cell-account-status>

                    <slot:actions>
    <x-table-actions :items="array_filter([
        ['label' => 'View parent', 'icon' => 'eye', 'url' => 'view_url'],
        $canManageParents ? ['label' => 'Manage parent', 'icon' => 'pencil', 'url' => 'manage_url'] : null,
        $canAssignStudents ? ['label' => 'Assign students', 'icon' => 'users', 'url' => 'assign_url'] : null,
        $canDeleteParents ? ['label' => 'Delete parent', 'icon' => 'trash-2', 'url' => 'delete_url', 'type' => 'delete', 'confirm' => 'Delete this parent?'] : null,
    ])" />
</slot:actions>
                </april:data-table>
            </div>
        </div>
    </slot:content>
</april:card>
