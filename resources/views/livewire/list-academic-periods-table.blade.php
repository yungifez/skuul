<div class="card">
    <div class="card-header">
        <h4 class="card-title">AcademicPeriod List for {{current_academic_year()->name}}</h4>
    </div>
    <div class="card-body">
        <livewire:datatable :model="App\Models\AcademicPeriod::class"
        :filters="[
            ['name' => 'where' , 'arguments' => ['academic_year_id' , current_academic_year()->id]]
        ]"
        :columns="[
            ['property' => 'name'],
            ['property' => 'typeLabel', 'name' => 'Type'],
            ['name' => 'Dates', 'type' => 'academic-period-dates'],
            ['name' => 'Status', 'type' => 'academic-period-status', 'route-prefix' => 'academic-periods'],
            ['type' => 'dropdown', 'name' => 'actions','links' => [
                ['href' => 'academic-periods.edit', 'text' => 'Edit', 'icon' => 'settings',],
            ]],
            ['type' => 'delete', 'name' => 'Delete', 'action' => 'academic-periods.destroy',]
        ]" />
    </div>
</div>
