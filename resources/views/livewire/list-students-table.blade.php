<div class="card">
    <div class="card-header">
        <h2 class="card-title">Students list</h2>
    </div>
    <div class="card-body">
        <livewire:datatable :model="App\Models\User::class" uniqueId="students-list-table" :filters="[['name' => 'students'], ['name' => 'inSchool'], ['name' => 'orderBy' , 'arguments' => ['name']], ['name' => 'has', 'arguments' => ['StudentRecord']], ['name' => 'with' , 'arguments' => ['studentRecord','studentRecord.section', 'studentRecord.myClass']]]" :columns="[
            ['property' => 'name'] , 
            ['property' => 'email'] , 
            ['property' => 'admission_number' ,'relation' => 'studentRecord'] , 
            ['property' => 'name', 'name' => 'Class' ,'relation' => 'studentRecord.myClass'] , 
            ['property' => 'name', 'name' => 'section' ,'relation' => 'studentRecord.section'] , 
            ['type' => 'dropdown', 'name' => 'actions','links' => [
                ['href' => 'students.edit', 'text' => 'Manage Profile', 'icon' => 'fas fa-pen',],
                ['href' => 'students.show', 'text' => 'View', 'icon' => 'fas fa-eye',  ],
            ]],
            ['type' => 'delete', 'name' => 'Delete', 'action' => 'students.destroy',]
         ]
        "/>
    </div>
</div>
