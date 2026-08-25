<april:card>
    <slot:title>Notices</slot:title>
    <slot:description>Keep your school community informed with current notices.</slot:description>
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
                        <p class="font-medium text-foreground">No notices yet</p>
                        <p>Published school notices will appear here.</p>
                    </div>
                </slot:empty>

                <slot:actions>
    <x-table-actions :items="array_filter([
        ['label' => 'View notice', 'icon' => 'eye', 'url' => 'view_url'],
        $canDeleteNotices ? ['label' => 'Delete notice', 'icon' => 'trash-2', 'url' => 'delete_url', 'type' => 'delete', 'confirm' => 'Delete this notice?'] : null,
    ])" />
</slot:actions>
            </april:data-table>
        </div>
    </slot:content>
</april:card>
