<div class="card">
    <div class="card-header flex flex-wrap items-center justify-between gap-3">
        <h2 class="card-title">{{$academicYear->name}}</h2>
        <x-academic-period-status-control :period="$academicYear" route-prefix="academic-years" />
    </div>
    <div class="card-body">
        <div class="mb-6 grid gap-4 text-sm text-muted-foreground md:grid-cols-2">
            <p>Starts: <span class="font-medium text-foreground">{{ $academicYear->starts_on?->format('M j, Y') ?? 'Not scheduled' }}</span></p>
            <p>Ends: <span class="font-medium text-foreground">{{ $academicYear->ends_on?->format('M j, Y') ?? 'Not scheduled' }}</span></p>
        </div>
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
                ['href' => 'exams.edit', 'text' => 'Edit', 'icon' => 'settings',],
                ['href' => 'exams.show', 'text' => 'View', 'icon' => 'eye',  ],
            ]],
            ['type' => 'delete', 'name' => 'Delete', 'action' => 'exams.destroy',]
        ]"/>
    </div>
</div>
