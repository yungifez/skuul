<div class="card">
    <div class="card-header">
        <h4 class="card-title">Academic year list</h4>
    </div>
    <div class="card-body">
        <livewire:datatable :model="App\Models\AcademicYear::class" 
        :filters="[
            ['name' => 'where' , 'arguments' => ['school_id' , auth()->user()->school_id]]
        ]"
        :columns="[
            ['name' => 'Start Year', 'property' => 'start_year'],
            ['name' => 'Start Year', 'property' => 'stop_year'],
            ['name' => 'name'],
            ['type' => 'dropdown', 'name' => 'actions','links' => [
                ['href' => 'academic-years.edit', 'text' => 'Edit', 'icon' => 'fas fa-cog',],
                ['href' => 'academic-years.show', 'text' => 'View', 'icon' => 'fas fa-eye',  ],
            ]],
            ['type' => 'delete', 'name' => 'Delete', 'action' => 'academic-years.destroy',]
        ]"/>
    </div>
</div>
