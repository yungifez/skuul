<div class="card">
    <div class="card-header">
        <h2 class="card-title">Students list</h2>
    </div>
    <div class="card-body">
        <div class="py-3">
            <x-display-validation-errors/>
        </div>
        @unlessrole(['parent', 'student'])
            <livewire:datatable :model="App\Models\User::class" uniqueId="students-list-table" :filters="[['name' => 'students'], ['name' => 'inSchool'], ['name' => 'orderBy' , 'arguments' => ['name']], ['name' => 'has', 'arguments' => ['StudentRecord']], ['name' => 'with' , 'arguments' => ['studentRecord','studentRecord.section', 'studentRecord.myClass']]]" :columns="[
                ['type' => 'image', 'property' => 'profile_photo_url'] , 
                ['property' => 'name'] , 
                ['property' => 'email'] , 
                ['property' => 'admission_number' ,'relation' => 'studentRecord'] , 
                ['property' => 'name', 'name' => 'Class' ,'relation' => 'studentRecord.myClass'] , 
                ['property' => 'name', 'name' => 'section' ,'relation' => 'studentRecord.section'] , 
                ['property' => 'locked', 'name' => 'Locked' , 'type' => 'boolean-switch', 'action' => 'user.lock-account', 'field' => 'lock', 'true-statement' => 'Locked', 'false-statement' => 'Unlocked',  'can' => 'lock user'],
                ['type' => 'dropdown', 'name' => 'actions','links' => [
                    ['href' => 'students.edit', 'text' => 'Manage Profile', 'icon' => 'fas fa-pen', 'can' => 'update student'],
                    ['href' => 'students.show', 'text' => 'View', 'icon' => 'fas fa-eye',],
                ]],
                ['type' => 'delete', 'name' => 'Delete', 'action' => 'students.destroy','can' => 'delete student']
            ]
            "/>
        @endhasanyrole
        @hasanyrole('parent')
            <livewire:datatable :model="App\Models\User::class" uniqueId="students-list-table" :filters="[
            ['name' => 'students'], 
            ['name' => 'inSchool'],
            ['name' => 'whereRelation', 'arguments' => ['parents', 'parent_records.user_id', auth()->user()->id]],
            ['name' => 'orderBy' , 'arguments' => ['name']], 
            ['name' => 'has', 'arguments' => ['StudentRecord']], 
            ['name' => 'with' , 'arguments' => ['studentRecord','studentRecord.section', 'studentRecord.myClass']]]" 
            :columns="[
                ['property' => 'name'] , 
                ['property' => 'email'] , 
                ['property' => 'admission_number' ,'relation' => 'studentRecord'] , 
                ['property' => 'name', 'name' => 'Class' ,'relation' => 'studentRecord.myClass'] , 
                ['property' => 'name', 'name' => 'section' ,'relation' => 'studentRecord.section'] , 
                ['type' => 'dropdown', 'name' => 'actions','links' => [
                    ['href' => 'students.show', 'text' => 'View', 'icon' => 'fas fa-eye',  ],
                ]],
            ]
            "/>
        @endhasanyrole
    </div>
</div>
