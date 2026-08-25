<april:card><slot:title>Graduands in this academic year</slot:title><slot:description>Students with a completed graduation record.</slot:description><slot:content><div wire:key="{{ $id }}-{{ $this->tableRevision }}"><april:data-table id="{{ $id }}" :data="$data" :columns="$columns" :pagination="$pagination" :per-page-options="$perPageOptions" row-key="{{ $rowKey }}" :searchable="$searchable" @query-change="$wire.updateTable($event.detail)"><slot:empty><div class="space-y-1"><p class="font-medium text-foreground">No graduands yet</p><p>Graduated students will appear here.</p></div></slot:empty><slot:actions>
    <x-table-actions :items="array_filter([
        $canViewStudents ? ['label' => 'View student', 'icon' => 'eye', 'url' => 'view_url'] : null,
        $canManageStudents ? ['label' => 'Manage student', 'icon' => 'pencil', 'url' => 'edit_url'] : null,
        $canResetGraduations ? ['label' => 'Reset graduation', 'icon' => 'rotate-ccw', 'url' => 'reset_url', 'type' => 'delete', 'confirm' => 'Reset this graduation?'] : null,
    ])" />
</slot:actions></april:data-table></div></slot:content></april:card>
