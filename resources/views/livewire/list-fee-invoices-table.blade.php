<div class="card">
    <div class="card-header">
        <h2 class="card-title">Fee Invoices</h2>
    </div>
    <div class="card-body">
        <form action="" class="my-5 md:grid grid-cols-2 gap-4">
            <div class="flex w-full flex-col gap-2">
                <april:label for="year">Due Date Year</april:label>
                <april:select id="year" name="year" wire:model.live="year" x-data="{ years: [...Array(400)].map((_, i) => i + 1900) }">
                    <template x-for="yearOption in years" :key="yearOption">
                        <option :value="yearOption" x-text="yearOption"></option>
                    </template>
                </april:select>
            </div>
            <div class="flex w-full flex-col gap-2">
                <april:label for="invoice-status">Invoice status</april:label>
                <april:select name="" wire:model.live="status" id="invoice-status">
                @foreach ($statuses as $status)
                    <option value="{{$status}}">{{ucfirst($status)}}</option>
                @endforeach

                </april:select>
            </div>
        </form>
        <x-loading-spinner/>

        <div wire:loading.remove.delay class="my-3">
            @unlessrole(['student', 'parent'])
                <livewire:datatable :model="App\Models\FeeInvoice::class"
                :wire:key="Str::Random(10)"
                uniqueId="list-fee-invoices"
                :filters="array_merge([
                    ['name' => 'ofSchool'],
                    ['name' => 'whereYear', 'arguments' => ['due_date', $year]],
                    ['name' => 'orderBy', 'arguments' => ['due_date', 'desc']],
                    ['name' => 'with', 'arguments' => ['user','user.studentRecord.myClass','user.studentRecord.section']]
                ], $queryAddon)"
                :columns="[
                    ['property' => 'name',],
                    ['name' => 'Student\'s Name', 'property' => 'name', 'relation' => 'user'],
                    ['name' => 'paid'],
                    ['property'=>'balance'],
                    ['property' => 'due_date'],
                    ['name' => 'Actions', 'type' => 'dropdown' , 'links' => [
                        ['href' => 'fee-invoices.edit', 'text' => 'edit', 'icon' => 'settings'],
                        ['href' => 'fee-invoices.show', 'text' => 'view', 'icon' => 'eye'],
                        ['href' => 'fee-invoices.pay', 'text' => 'Add Payment   ', 'icon' => 'credit-card'],
                    ]],
                    ['type' => 'delete', 'name' => 'Delete', 'action' => 'fee-invoices.destroy',]
                ]"
                />
            @endhasanyrole
            @role('parent')
                <livewire:datatable :model="App\Models\FeeInvoice::class"
                :wire:key="Str::Random(10)"
                uniqueId="list-fee-invoices"
                :filters="array_merge([
                    ['name' => 'ofSchool'],
                    ['name' => 'whereRelation', 'arguments' => ['user.parents', 'parent_records.user_id', auth()->user()->id]],
                    ['name' => 'whereYear', 'arguments' => ['due_date', $year]],
                    ['name' => 'orderBy', 'arguments' => ['due_date', 'desc']],
                    ['name' => 'with', 'arguments' => ['user','user.studentRecord.myClass','user.studentRecord.section']]
                ], $queryAddon)"
                :columns="[
                    ['property' => 'name',],
                    ['name' => 'Student\'s Name', 'property' => 'name', 'relation' => 'user'],
                    ['name' => 'Class', 'property' => 'name', 'relation' => 'user.studentRecord.myClass'],
                    ['name' => 'Section', 'property' => 'name', 'relation' => 'user.studentRecord.section'],
                    ['name' => 'paid'],
                    ['property'=>'balance'],
                    ['property' => 'due_date'],
                    ['name' => 'Actions', 'type' => 'dropdown' , 'links' => [
                        ['href' => 'fee-invoices.show', 'text' => 'view', 'icon' => 'eye'],
                    ]],
                ]"
                />
            @endrole
            @role('student')
                <livewire:datatable :model="App\Models\FeeInvoice::class"
                :wire:key="Str::Random(10)"
                uniqueId="list-fee-invoices"
                :filters="array_merge([
                    ['name' => 'whereRelation', 'arguments' => ['user', 'id', auth()->user()->id]],
                    ['name' => 'whereYear', 'arguments' => ['due_date', $year]],
                    ['name' => 'orderBy', 'arguments' => ['due_date', 'desc']],
                    ['name' => 'with', 'arguments' => ['user','user.studentRecord.myClass','user.studentRecord.section']]
                ], $queryAddon)"
                :columns="[
                    ['property' => 'name',],
                    ['name' => 'paid'],
                    ['property'=>'balance'],
                    ['property' => 'due_date'],
                    ['name' => 'Actions', 'type' => 'dropdown' , 'links' => [
                        ['href' => 'fee-invoices.show', 'text' => 'view', 'icon' => 'eye'],
                    ]],
                ]"
                />
            @endrole
        </div>

    </div>
</div>
