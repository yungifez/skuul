<april:card>
    <slot:title>School calendars</slot:title>
    <slot:description>Each calendar carries its dates, reporting periods, and lifecycle state.</slot:description>
    <slot:content>
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
                        <p class="font-medium text-foreground">No school calendars yet</p>
                        <p>Set up a calendar to define the reporting periods staff will use.</p>
                    </div>
                </slot:empty>

                <slot:cell-status-label>
                    <span class="rounded-md border px-2 py-1 text-xs font-medium capitalize" x-text="row.status_label"></span>
                </slot:cell-status-label>

                <slot:actions>
    <x-table-actions :items="array_filter([
        ['label' => 'View academic year', 'icon' => 'eye', 'url' => 'view_url'],
        $canDeleteYears ? ['label' => 'Delete calendar', 'icon' => 'trash-2', 'url' => 'delete_url', 'type' => 'delete', 'confirm' => 'Delete this calendar?'] : null,
    ])" />
</slot:actions>
            </april:data-table>
        </div>
    </slot:content>
</april:card>
