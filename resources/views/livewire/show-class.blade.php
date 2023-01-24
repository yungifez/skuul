<div class="card">
    <div class="card-header">
        <h2 class="card-title">{{$class->name}}</h2>
    </div>
    <div class="card-body">
        <h1 class="text-center text-xl md:text-3xl font-bold">Sections in class</h1>
        <livewire:datatable :model="App\Models\MyClass::class" uniqueId="section-list-table" :filters="[['name' => 'find' , 'arguments' => [$class->id]], ['name' => 'sections']]" :columns="
            [
            ['property' => 'name'] , 
            ['type' => 'dropdown', 'name' => 'actions','links' => [
                ['href' => 'sections.edit', 'text' => 'Settings', 'icon' => 'fas fa-cog'],
                ['href' => 'sections.show', 'text' => 'View', 'icon' => 'fas fa-eye'],
            ]],
            ['type' => 'delete', 'name' => 'Delete', 'action' => 'sections.destroy']
         ]
        "/>
        <h1 class="text-center text-xl md:text-3xl font-bold">Students in class</h1>
        <livewire:datatable :model="App\Models\User::class" uniqueId="students-list-table" 
        :filters="[
            ['name' => 'where' , 'arguments' => ['school_id' , auth()->user()->school_id]], 
            ['name' => 'whereRelation' , 'arguments' => ['studentRecord','my_class_id' , $class->id]], 
            ['name' => 'with' , 'arguments' => ['studentRecord' ,'studentRecord.section']]
        ]" :columns="
            [
            ['property' => 'name'] , 
            ['property' => 'email'] , 
            ['property' => 'name', 'name' => 'section name' ,'relation' => 'studentRecord.section'] , 
            ['type' => 'dropdown', 'name' => 'actions','links' => [
                ['href' => 'students.edit', 'text' => 'Settings', 'icon' => 'fas fa-cog'],
                ['href' => 'students.show', 'text' => 'View', 'icon' => 'fas fa-eye',],
            ]],
            ['type' => 'delete', 'name' => 'Delete', 'action' => 'students.destroy']
         ]
        "/>
        <h1 class="text-center text-xl md:text-3xl font-bold">Subjects in class</h1>
        <livewire:datatable :model="App\Models\Subject::class" uniqueId="subjects-list-table" :filters="[['name' => 'where' , 'arguments' => ['my_class_id' , $class->id]]]" :columns="
            [
            ['property' => 'name'] , 
            ['method' => 'count' , 'name' => 'No of teachers', 'relation' => 'teachers'] , 
            ['type' => 'dropdown', 'name' => 'actions','links' => [
                ['href' => 'subjects.edit', 'text' => 'Settings', 'icon' => 'fas fa-cog'],
                ['href' => 'subjects.show', 'text' => 'View', 'icon' => 'fas fa-eye'],
            ]],
            ['type' => 'delete', 'name' => 'Delete', 'action' => 'subjects.destroy']
         ]
        "/>
    </div>
</div>