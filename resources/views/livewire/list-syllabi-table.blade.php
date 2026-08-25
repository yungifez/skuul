<april:card><slot:title>Syllabi</slot:title><slot:description>Syllabi attached to course offerings.</slot:description><slot:content><div wire:key="{{ $id }}-{{ $this->tableRevision }}"><april:data-table id="{{ $id }}" :data="$data" :columns="$columns" :pagination="$pagination" :per-page-options="$perPageOptions" row-key="{{ $rowKey }}" :searchable="$searchable" @query-change="$wire.updateTable($event.detail)"><slot:empty><div class="space-y-1"><p class="font-medium text-foreground">No syllabi yet</p><p>Upload a syllabus for a specific offering.</p></div></slot:empty><slot:actions>
    <x-table-actions :items="array_filter([
        $canReadSyllabi ? ['label' => 'View syllabus', 'icon' => 'eye', 'url' => 'view_url'] : null,
        $canDeleteSyllabi ? ['label' => 'Delete syllabus', 'icon' => 'trash-2', 'url' => 'delete_url', 'type' => 'delete', 'confirm' => 'Delete this syllabus?'] : null,
    ])" />
</slot:actions></april:data-table></div></slot:content></april:card>
