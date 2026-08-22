<april:card>
    <slot:title>{{ school_terms('period', 'Academic periods') }} for {{ current_academic_year()?->name ?? 'the selected academic cycle' }}</slot:title>
    <slot:description>Open periods accept routine work. Closing and closed periods protect the school’s history.</slot:description>
    <slot:content>
        <livewire:datatable :model="App\Models\AcademicPeriod::class"
        :filters="[
            ['name' => 'where' , 'arguments' => ['academic_year_id' , current_academic_year_id()]]
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
    </slot:content>
</april:card>
