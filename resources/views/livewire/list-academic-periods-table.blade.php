<april:card>
    <slot:title>{{ school_terms('period', 'Academic periods') }} for {{ current_academic_year()?->name ?? 'the selected academic cycle' }}</slot:title>
    <slot:description>Open periods accept routine work. Closing and closed periods protect the school’s history.</slot:description>
    <slot:content>
        <div class="space-y-6">
            <x-display-validation-errors />

            <div wire:loading class="text-sm text-muted-foreground">Updating periods...</div>

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
                            <p class="font-medium text-foreground">No academic periods yet</p>
                            <p>Add a period to the selected academic cycle.</p>
                        </div>
                    </slot:empty>

                    <slot:cell-status-label>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-md border px-2 py-1 text-xs font-medium capitalize" x-text="row.status_label"></span>

                            <template x-if="row.status === 'open' && row.can_close">
                                <form method="POST" x-bind:action="row.begin_closing_url">
                                    @csrf
                                    <april:button type="submit" variant="outline" size="sm">
                                        <x-lucide-lock class="mr-1.5 size-3.5" />
                                        Start closing
                                    </april:button>
                                </form>
                            </template>

                            <template x-if="row.status === 'closing' && row.can_close">
                                <details class="rounded-md border p-2">
                                    <summary class="cursor-pointer text-xs font-medium">Confirm close</summary>
                                    <form method="POST" x-bind:action="row.close_url" class="mt-3 flex flex-wrap items-end gap-2">
                                        @csrf
                                        <label class="flex flex-col gap-1 text-xs">
                                            Closure note
                                            <input name="reason" maxlength="500" class="rounded-md border border-input bg-background px-2 py-1.5 text-sm" />
                                        </label>
                                        <label class="flex items-center gap-2 text-xs">
                                            <input type="checkbox" name="force" value="1" class="size-4 rounded border-input" />
                                            Force close
                                        </label>
                                        <april:button type="submit" size="sm">Confirm close</april:button>
                                    </form>
                                </details>
                            </template>

                            <template x-if="row.status === 'closed' && row.can_reopen">
                                <details class="rounded-md border p-2">
                                    <summary class="cursor-pointer text-xs font-medium">Reopen</summary>
                                    <form method="POST" x-bind:action="row.reopen_url" class="mt-3 flex flex-wrap items-end gap-2">
                                        @csrf
                                        <label class="flex flex-col gap-1 text-xs">
                                            Reason *
                                            <input name="reason" required maxlength="500" class="rounded-md border border-input bg-background px-2 py-1.5 text-sm" />
                                        </label>
                                        <april:button type="submit" variant="outline" size="sm">
                                            <x-lucide-lock-open class="mr-1.5 size-3.5" />
                                            Reopen
                                        </april:button>
                                    </form>
                                </details>
                            </template>
                        </div>
                    </slot:cell-status-label>

                    <slot:actions>
    <x-table-actions :items="array_filter([
        $canEditPeriods ? ['label' => 'Edit period', 'icon' => 'settings', 'url' => 'edit_url'] : null,
        $canDeletePeriods ? ['label' => 'Delete period', 'icon' => 'trash-2', 'url' => 'delete_url', 'type' => 'delete', 'confirm' => 'Delete this academic period?'] : null,
    ])" />
</slot:actions>
                </april:data-table>
            </div>
        </div>
    </slot:content>
</april:card>
