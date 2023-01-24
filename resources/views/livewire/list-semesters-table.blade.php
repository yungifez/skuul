<div class="card">
    <div class="card-header">
        <h4 class="card-title">Semester List for {{auth()->user()->school->academicYear->name}}</h4>
    </div>
    <div class="card-body">
        <livewire:datatable :model="App\Models\Semester::class" 
        :filters="[
            ['name' => 'where' , 'arguments' => ['academic_year_id' , auth()->user()->school->academicYear->id]]
        ]"
        :columns="[
            ['property' => 'name'],
            ['type' => 'dropdown', 'name' => 'actions','links' => [
                ['href' => 'semesters.edit', 'text' => 'Edit', 'icon' => 'fas fa-cog',],
            ]],
            ['type' => 'delete', 'name' => 'Delete', 'action' => 'semesters.destroy',]
        ]" />
    </div>
</div>
