<april:card><slot:title>Subject catalog</slot:title><slot:description>Subjects available for course offerings.</slot:description><slot:content><div wire:key="{{ $id }}-{{ $this->tableRevision }}"><april:data-table id="{{ $id }}" :data="$data" :columns="$columns" :pagination="$pagination" :per-page-options="$perPageOptions" row-key="{{ $rowKey }}" :searchable="$searchable" @query-change="$wire.updateTable($event.detail)"><slot:empty><div class="space-y-1"><p class="font-medium text-foreground">No subjects yet</p><p>Add a subject to the school catalog.</p></div></slot:empty><slot:actions>
    <x-table-actions :items="array_filter([
        $canEditSubjects ? ['label' => 'Edit subject', 'icon' => 'settings', 'url' => 'edit_url'] : null,
        $canDeleteSubjects ? ['label' => 'Delete subject', 'icon' => 'trash-2', 'url' => 'delete_url', 'type' => 'delete', 'confirm' => 'Delete this subject?'] : null,
    ])" />
</slot:actions></april:data-table></div></slot:content></april:card>
