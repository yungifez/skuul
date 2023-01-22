<div class="card">
    <div class="card-header">
        <h4 class="card-title">Graduands in this academic year</h4>
    </div>
    <div class="card-body">
        <livewire:datatable :model="App\Models\User::class" uniqueId="students-list-table" 
        :filters="[['name' => 'students'], ['name' => 'inSchool'], ['name' => 'orderBy' , 'arguments' => ['name']], ['name' => 'has', 'arguments' => ['graduatedStudentRecord']], ['name' => 'with', 'arguments' => ['graduatedStudentRecord', 'graduatedStudentRecord.myClass', 'graduatedStudentRecord.section']]]" :columns="[
            ['property' => 'name'] , 
            ['property' => 'email'] , 
            ['property' => 'admission_number' ,'relation' => 'graduatedStudentRecord'] , 
            ['property' => 'name', 'name' => 'From Class' ,'relation' => 'graduatedStudentRecord.myClass'] , 
            ['property' => 'name', 'name' => 'From section' ,'relation' => 'graduatedStudentRecord.section'] , 
            ['type' => 'dropdown', 'name' => 'actions','links' => [
                ['href' => 'students.edit', 'text' => 'Manage Profile', 'icon' => 'fas fa-pen',],
                ['href' => 'students.show', 'text' => 'View', 'icon' => 'fas fa-eye',  ],
            ]],
            ['type' => 'delete', 'name' => 'Reset', 'action' => 'students.graduations.reset',]
         ]
        "/>
    </div>
</div>
