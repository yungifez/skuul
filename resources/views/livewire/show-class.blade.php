<div class="card">
    <div class="card-header">
        <h2 class="card-title">{{$class->name}}</h2>
    </div>
    <div class="card-body">
        <h3 class="text-center text-lg md:text-3xl font-bold my-5">Sections in class</h1>
        <livewire:datatable :model="App\Models\MyClass::class" uniqueId="section-list-table" :filters="[['name' => 'find' , 'arguments' => [$class->id]], ['name' => 'sections']]" :columns="
            [
            ['property' => 'name'] ,
            ['type' => 'dropdown', 'name' => 'actions','links' => [
                ['href' => 'sections.edit', 'text' => 'Settings', 'icon' => 'settings'],
                ['href' => 'sections.show', 'text' => 'View', 'icon' => 'eye'],
            ]],
            ['type' => 'delete', 'name' => 'Delete', 'action' => 'sections.destroy']
         ]
        "/>
        <div class="my-8 rounded-lg border border-border bg-muted/30 p-5 text-sm text-muted-foreground">
            Learners, subjects, and timetables are now managed through academic levels and cycle sections. This legacy class remains available only for historical structural records.
        </div>
    </div>
</div>
