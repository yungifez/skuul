<april:card>
    <slot:title>Teachers</slot:title>
    <slot:description>Search and manage the teachers who belong to this school.</slot:description>
    <slot:content>
        <div class="space-y-6">
            <x-display-validation-errors />

            <div wire:loading class="text-sm text-muted-foreground">Updating teachers...</div>

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
                            <p class="font-medium text-foreground">No teachers yet</p>
                            <p>Add the first teacher for this school.</p>
                        </div>
                    </slot:empty>

                    <slot:cell-account-status>
                        <span class="capitalize" x-text="row.account_status ?? ''"></span>
                    </slot:cell-account-status>

                    <slot:actions>
    <x-table-actions :items="array_filter([
        ['label' => 'View teacher', 'icon' => 'eye', 'url' => 'view_url'],
        $canManageTeachers ? ['label' => 'Manage teacher', 'icon' => 'pencil', 'url' => 'manage_url'] : null,
        $canDeleteTeachers ? ['label' => 'Delete teacher', 'icon' => 'trash-2', 'url' => 'delete_url', 'type' => 'delete', 'confirm' => 'Delete this teacher?'] : null,
    ])" />
</slot:actions>
                </april:data-table>
            </div>
        </div>
    </slot:content>
</april:card>
