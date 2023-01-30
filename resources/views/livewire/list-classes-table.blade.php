<div class="card" >
    <div class="card-header">
        <h4 class="card-title">All Classes</h4>
    </div>
    <div class="card-body">
        <livewire:datatable :model="App\Models\School::class" uniqueId="class-list" :filters="[['name' => 'find' , 'arguments' => [auth()->user()->school_id]] , ['name' => 'myClasses'], ['name' => 'with', 'arguments' => ['classGroup']]]" :columns="
        [
            ['property' => 'name'] , 
            ['property' => 'name', 'name' => 'classGroup', 'relation' => 'classGroup'] , 
            ['type' => 'dropdown', 'name' => 'actions','links' => [
                ['href' => 'classes.edit', 'text' => 'Settings', 'icon' => 'fas fa-cog'],
                ['href' => 'classes.show', 'text' => 'View', 'icon' => 'fas fa-eye'],
            ]],
            ['type' => 'delete', 'name' => 'Delete', 'action' => 'classes.destroy']
         ]
        "/>
    </div>
</div>
