<div class="card">
    <div class="card-header">
        <h4 class="card-title">Academic year list</h4>
    </div>
    <div class="card-body">
        <livewire:datatable :model="App\Models\AcademicYear::class"
        :filters="[
            ['name' => 'inSchool']
        ]"
        :columns="[
            ['name' => 'Start Year', 'property' => 'start_year'],
            ['name' => 'Stop Year', 'property' => 'stop_year'],
            ['name' => 'name'],
            ['name' => 'Dates', 'type' => 'academic-period-dates'],
            ['name' => 'Status', 'type' => 'academic-period-status', 'route-prefix' => 'academic-years'],
            ['type' => 'dropdown', 'name' => 'actions','links' => [
                ['href' => 'academic-years.edit', 'text' => 'Edit', 'icon' => 'settings',],
                ['href' => 'academic-years.show', 'text' => 'View', 'icon' => 'eye',  ],
            ]],
            ['type' => 'delete', 'name' => 'Delete', 'action' => 'academic-years.destroy',]
        ]"/>
    </div>
</div>
