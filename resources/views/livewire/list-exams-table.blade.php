<april:card><slot:title>Exam list for {{ current_school()?->academicPeriod?->name ?? 'the current period' }}</slot:title><slot:description>Manage scheduled exams.</slot:description><slot:content><div wire:key="{{ $id }}-{{ $this->tableRevision }}"><april:data-table id="{{ $id }}" :data="$data" :columns="$columns" :pagination="$pagination" :per-page-options="$perPageOptions" row-key="{{ $rowKey }}" :searchable="$searchable" @query-change="$wire.updateTable($event.detail)"><slot:empty><div class="space-y-1"><p class="font-medium text-foreground">No exams yet</p><p>Create an exam for the current academic period.</p></div></slot:empty><slot:actions>
    <x-table-actions :items="array_filter([
        $canUpdateExam ? ['label' => 'Edit exam', 'icon' => 'settings', 'url' => 'edit_url'] : null,
        $canDeleteExam ? ['label' => 'Delete exam', 'icon' => 'trash-2', 'url' => 'delete_url', 'type' => 'delete', 'confirm' => 'Delete this exam?'] : null,
    ])" />
</slot:actions></april:data-table></div></slot:content></april:card>
