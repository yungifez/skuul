<div class="card" >
    <div class="card-header">
        <h4 class="card-title">All Classes</h4>
    </div>
    <div class="card-body">
        <livewire:datatable :model="App\Models\School::class" uniqueId="class-list" :filters="[['name' => 'find' , 'arguments' => [current_school_id()]] , ['name' => 'myClasses'], ['name' => 'with', 'arguments' => ['classGroup']]]" :empty-state="['heading' => 'No classes yet', 'description' => 'Add a class to structure teaching and placement.', 'action' => ['href' => route('classes.create'), 'ability' => 'create', 'arguments' => [\App\Models\MyClass::class], 'label' => 'Add class']]" :columns="
        [
            ['property' => 'name'] ,
            ['property' => 'name', 'name' => 'classGroup', 'relation' => 'classGroup'] ,
            ['type' => 'dropdown', 'name' => 'actions','links' => [
                ['href' => 'classes.edit', 'text' => 'Settings', 'icon' => 'settings'],
                ['href' => 'classes.show', 'text' => 'View', 'icon' => 'eye'],
            ]],
            ['type' => 'delete', 'name' => 'Delete', 'action' => 'classes.destroy']
         ]
        "/>
    </div>
</div>
