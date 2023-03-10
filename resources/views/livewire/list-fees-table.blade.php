<div class="card">
    <div class="card-header">
        <h2 class="card-title">Fees</h2>
    </div>
    <div class="card-body">
        <livewire:datatable unique_id="list-fees-table" :model="App\Models\Fee::class"
        :filters="[
            ['name' => 'whereRelation', 'arguments' => ['feeCategory','school_id', auth()->user()->school_id]],
            ['name' => 'with', 'arguments' => ['feeCategory']]
        ]"
        :columns="[
            ['property' => 'name'],
            ['property' => 'name', 'relation' => 'feeCategory', 'name' =>'Fee Category'],
            ['name' => 'Actions', 'type' => 'dropdown' , 'links' => [
                ['href' => 'fees.edit', 'text' => 'edit', 'icon' => 'fas fa-cog'],
            ]],
            ['type' => 'delete', 'name' => 'Delete', 'action' => 'fees.destroy',]
        ]"/>
    </div>
</div>
