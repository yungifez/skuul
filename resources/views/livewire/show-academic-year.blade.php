<div class="card">
    <div class="card-header">{{$academicYear->name}}</div>
    <div class="card-body">
        <h3 class="text-xl md:text-3xl my-3 text-center">Exams in academic year</h3>
        <livewire:datatable :model="App\Models\AcademicYear::class" 
        :filters="[
            ['name' => 'find' , 'arguments' => [ $academicYear->id]],
            ['name' => 'exams' ],
            ['name' => 'with', 'arguments' => ['semester']]
        ]"
        :columns="[
            ['property' => 'name'],
            ['property' => 'name', 'relation' => 'semester'],
            ['type' => 'dropdown', 'name' => 'actions','links' => [
                ['href' => 'exams.edit', 'text' => 'Edit', 'icon' => 'fas fa-cog',],
                ['href' => 'exams.show', 'text' => 'View', 'icon' => 'fas fa-eye',  ],
            ]],
            ['type' => 'delete', 'name' => 'Delete', 'action' => 'exams.destroy',]
        ]"/>
    </div>
</div>