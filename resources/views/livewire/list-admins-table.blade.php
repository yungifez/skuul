<div class="card">
    <div class="card-header">
        <h2 class="card-title">Students list</h2>
    </div>
    <div class="card-body">
        <livewire:datatable :model="App\Models\User::class" uniqueId="admins-list-table" :filters="[['name' => 'role', 'arguments' => ['admin']], ['name' => 'inSchool'], ['name' => 'orderBy' , 'arguments' => ['name']]]" :columns="[
            ['property' => 'name'] , 
            ['property' => 'email'] ,
            ['property' => 'gender'] ,
            ['type' => 'dropdown', 'name' => 'actions','links' => [
                ['href' => 'admins.edit', 'text' => 'Manage Profile', 'icon' => 'fas fa-pen',],
                ['href' => 'admins.show', 'text' => 'View', 'icon' => 'fas fa-eye',  ],
            ]],
            ['type' => 'delete', 'name' => 'Delete', 'action' => 'admins.destroy',]
         ]
        "/>
    </div>
</div>
