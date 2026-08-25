<div class="space-y-6">
    <x-display-validation-errors />

    <div wire:loading class="text-sm text-muted-foreground">Updating students...</div>

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
                    <p class="font-medium text-foreground">No students yet</p>
                    <p>Add the first student record for this school.</p>
                </div>
            </slot:empty>

            <slot:cell-student-record-status>
                <span class="capitalize" x-text="row.student_record?.status ?? ''"></span>
            </slot:cell-student-record-status>

            <slot:cell-account-status>
                <span class="capitalize" x-text="row.account_status ?? ''"></span>
            </slot:cell-account-status>

            <slot:actions>
    <x-table-actions :items="array_filter([
        ['label' => 'View student', 'icon' => 'eye', 'url' => 'view_url'],
        $canManageStudents ? ['label' => 'Manage student', 'icon' => 'pencil', 'url' => 'manage_url'] : null,
        $canDeleteStudents ? ['label' => 'Delete student', 'icon' => 'trash-2', 'url' => 'delete_url', 'type' => 'delete', 'confirm' => 'Delete this student?'] : null,
    ])" />
</slot:actions>
        </april:data-table>
    </div>
</div>
