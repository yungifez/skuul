<div class="card">
    <div class="card-header">
        <h4 class="card-title">Semester List for {{current_academic_year()->name}}</h4>
    </div>
    <div class="card-body">
        <livewire:datatable :model="App\Models\Semester::class"
        :filters="[
            ['name' => 'where' , 'arguments' => ['academic_year_id' , current_academic_year()->id]]
        ]"
        :columns="[
            ['property' => 'name'],
            ['property' => 'typeLabel', 'name' => 'Type'],
            ['name' => 'Dates', 'type' => 'academic-period-dates'],
            ['name' => 'Status', 'type' => 'academic-period-status', 'route-prefix' => 'semesters'],
            ['type' => 'dropdown', 'name' => 'actions','links' => [
                ['href' => 'semesters.edit', 'text' => 'Edit', 'icon' => 'settings',],
            ]],
            ['type' => 'delete', 'name' => 'Delete', 'action' => 'semesters.destroy',]
        ]" />
    </div>
</div>
