<div class="card">
    <div class="card-header">
        <div class="card-title">Fee Categories</div>
    </div>
    <div class="card-body">
        <livewire:datatable unique_id="list-fee-categories-table" :model="App\Models\FeeCategory::class"
        :empty-state="['heading' => 'No fee categories yet', 'description' => 'Create a category to organize fees.', 'action' => ['href' => route('fee-categories.create'), 'ability' => 'create', 'arguments' => [\App\Models\FeeCategory::class], 'label' => 'Add fee category']]"
        :filters="[
            ['name' => 'inSchool']
        ]"
        :columns="[
            ['property' => 'name'],
            ['name' => 'Actions', 'type' => 'dropdown' , 'links' => [
                ['href' => 'fee-categories.edit', 'text' => 'edit', 'icon' => 'settings'],
            ]],
            ['type' => 'delete', 'name' => 'Delete', 'action' => 'fee-categories.destroy',]
        ]"/>
    </div>
</div>
