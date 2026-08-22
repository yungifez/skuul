<div class="card">
    <div class="card-header">
        <h2 class="card-title">Fees</h2>
    </div>
    <div class="card-body">
        <livewire:datatable unique_id="list-fees-table" :model="App\Models\Fee::class"
        :empty-state="['heading' => 'No fees yet', 'description' => 'Add a fee after setting up its category.', 'action' => ['href' => route('fees.create'), 'ability' => 'create', 'arguments' => [\App\Models\Fee::class], 'label' => 'Add fee']]"
        :filters="[
            ['name' => 'whereRelation', 'arguments' => ['feeCategory','school_id', current_school_id()]],
            ['name' => 'with', 'arguments' => ['feeCategory']]
        ]"
        :columns="[
            ['property' => 'name'],
            ['property' => 'name', 'relation' => 'feeCategory', 'name' =>'Fee Category'],
            ['name' => 'Actions', 'type' => 'dropdown' , 'links' => [
                ['href' => 'fees.edit', 'text' => 'edit', 'icon' => 'settings'],
            ]],
            ['type' => 'delete', 'name' => 'Delete', 'action' => 'fees.destroy',]
        ]"/>
    </div>
</div>
