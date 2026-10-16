<div class="flex flex-col gap-4">
    @php
        // Built here, not inside the tag: a double quote inside a component
        // attribute ends the attribute, and Blade then leaves the tag raw.
        $rowActions = array_filter([
            ['label' => 'View invoice', 'icon' => 'eye', 'url' => 'view_url'],
            $canManageInvoices ? ['label' => 'Edit invoice', 'icon' => 'settings', 'url' => 'edit_url'] : null,
            $canPayInvoices ? ['label' => 'Take payment', 'icon' => 'credit-card', 'url' => 'pay_url'] : null,
            $canDeleteInvoices ? ['label' => 'Delete invoice', 'icon' => 'trash-2', 'method' => 'deleteInvoice', 'type' => 'action', 'confirm' => 'Delete :name?', 'names' => "row.name + ' for ' + row.student_name"] : null,
        ]);
        $controlClasses = 'h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
    @endphp

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-base font-semibold">Invoices</h2>
        <div class="grid grid-cols-2 gap-3 sm:w-96">
            <div>
                <label for="financial-period" class="sr-only">Financial period</label>
                <select id="financial-period" wire:model.live="financialPeriodId" class="{{ $controlClasses }}">
                    <option value="">All periods</option>
                    @foreach ($financialPeriods as $financialPeriod)
                        <option value="{{ $financialPeriod->id }}">{{ $financialPeriod->name }}{{ $financialPeriod->isClosed() ? ' · Closed' : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="invoice-status" class="sr-only">Invoice status</label>
                <select id="invoice-status" wire:model.live="status" class="{{ $controlClasses }}">
                    @foreach ($statuses as $invoiceStatus)
                        <option value="{{ $invoiceStatus }}">{{ ucfirst($invoiceStatus) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

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
                <p>No invoices</p>
            </slot:empty>

            <slot:cell-name>
                <a :href="row.view_url" class="font-medium hover:underline" x-text="row.name"></a>
            </slot:cell-name>

            <slot:actions>
    <x-table-actions :items="$rowActions" />
            </slot:actions>
        </april:data-table>
    </div>
</div>
