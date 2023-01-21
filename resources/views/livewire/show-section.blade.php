<div class="card">
    <div class="card-header">
        <h2 class="card-title">{{$section->name}}</h2>
    </div>
    <div class="card-body">
        <h1 class="text-center text-xl md:text-3xl font-bold">Students in section</h1>
        <livewire:datatable :model="App\Models\StudentRecord::class" uniqueId="students-list-table" :filters="[['name' => 'where' , 'arguments' => ['section_id' , $section->id]], ['name' => 'with' , 'arguments' => ['user']]]" :columns="
            [
            ['property' => 'name', 'relation' => 'user'] , 
            ['property' => 'email', 'relation' => 'user'] , 
            ['type' => 'dropdown', 'name' => 'actions','links' => [
                ['href' => 'students.edit', 'text' => 'Settings', 'icon' => 'fas fa-cog', 'relation' => 'user'],
                ['href' => 'students.show', 'text' => 'View', 'icon' => 'fas fa-eye',  'relation' => 'user'],
            ]],
            ['type' => 'delete', 'name' => 'Delete', 'action' => 'students.destroy',  'relation' => 'user']
         ]
        "/>
    </div>
</div>
