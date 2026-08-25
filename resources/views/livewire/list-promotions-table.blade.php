<april:card><slot:title>Promotion list</slot:title><slot:description>Promotions recorded for {{ $academicYear?->name ?? 'the selected academic year' }}.</slot:description><slot:content><div wire:key="{{ $id }}-{{ $this->tableRevision }}"><april:data-table id="{{ $id }}" :data="$data" :columns="$columns" :pagination="$pagination" :per-page-options="$perPageOptions" row-key="{{ $rowKey }}" :searchable="$searchable" @query-change="$wire.updateTable($event.detail)"><slot:empty><div class="space-y-1"><p class="font-medium text-foreground">No promotions yet</p><p>Promotions will appear here after they are recorded.</p></div></slot:empty><slot:actions>
    <x-table-actions :items="array_filter([
        $canViewPromotions ? ['label' => 'View promotion', 'icon' => 'eye', 'url' => 'view_url'] : null,
        $canResetPromotions ? ['label' => 'Reset promotion', 'icon' => 'rotate-ccw', 'url' => 'reset_url', 'type' => 'delete', 'confirm' => 'Reset this promotion?'] : null,
    ])" />
</slot:actions></april:data-table></div></slot:content></april:card>
