<april:card><slot:title>Syllabi</slot:title><slot:description>Weekly teaching plans for each course offering. Only the published revision reaches students.</slot:description><slot:content><div wire:key="{{ $id }}-{{ $this->tableRevision }}"><april:data-table id="{{ $id }}" :data="$data" :columns="$columns" :pagination="$pagination" :per-page-options="$perPageOptions" row-key="{{ $rowKey }}" :searchable="$searchable" @query-change="$wire.updateTable($event.detail)"><slot:empty><div class="space-y-1"><p class="font-medium text-foreground">No syllabi yet</p><p>Upload a syllabus for a specific offering.</p></div></slot:empty><slot:actions>
    <x-table-actions :items="array_filter([
        $canReadSyllabi ? ['label' => 'View syllabus', 'icon' => 'eye', 'url' => 'view_url'] : null,
        $canDeleteSyllabi ? ['label' => 'Delete syllabus', 'icon' => 'trash-2', 'url' => 'delete_url', 'type' => 'delete', 'confirm' => 'Delete the draft :name?', 'names' => 'row.name', 'when' => 'row.can_delete'] : null,
    ])" />
</slot:actions></april:data-table></div></slot:content></april:card>
