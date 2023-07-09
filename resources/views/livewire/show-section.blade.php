<div class="card">
    <div class="card-header">
        <h2 class="card-title">{{$section->name}}</h2>
    </div>
    <div class="card-body">
        <h1 class="text-center text-xl md:text-3xl font-bold my-3">Students in section</h1>
        <livewire:datatable :model="App\Models\User::class" uniqueId="students-list-table" 
        :filters="[
            ['name' => 'where' , 'arguments' => ['school_id' , auth()->user()->school_id]], 
            ['name' => 'whereRelation' , 'arguments' => ['studentRecord','section_id' , $section->id]],
        ]"
        :columns="
            [
            ['property' => 'name', ] , 
            ['property' => 'email', ] , 
            ['type' => 'dropdown', 'name' => 'actions','links' => [
                ['href' => 'students.edit', 'text' => 'Manage Profile', 'icon' => 'fas fa-pen', ],
                ['href' => 'students.show', 'text' => 'View', 'icon' => 'fas fa-eye',  ],
            ]],
            ['type' => 'delete', 'name' => 'Delete', 'action' => 'students.destroy',  ]
         ]
        "/>
    </div>
</div>
