<div class="card">
    <div class="card-header">
        <div class="card-title">Fee Categories</div>
    </div>
    <div class="card-body">
        <livewire:datatable unique_id="list-fee-categories-table" :model="App\Models\FeeCategory::class"
        :filters="[
            ['name' => 'where' , 'arguments' => ['school_id',auth()->user()->school_id]]
        ]"
        :columns="[
            ['property' => 'name'],
            ['name' => 'Actions', 'type' => 'dropdown' , 'links' => [
                ['href' => 'fee-categories.edit', 'text' => 'edit', 'icon' => 'fas fa-cog'],
            ]],
            ['type' => 'delete', 'name' => 'Delete', 'action' => 'fee-categories.destroy',]
        ]"/>
    </div>
</div>
