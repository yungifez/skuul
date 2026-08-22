<div class="card">
    <div class="card-header">
        <h2 class="card-title">Students list</h2>
    </div>
    <div class="card-body">
        <div class="py-3">
            <x-display-validation-errors/>
        </div>
        @unlessrole(['parent', 'student'])
            <livewire:datatable :model="App\Models\User::class" uniqueId="students-list-table" :filters="[['name' => 'students'], ['name' => 'ofSchool'], ['name' => 'orderBy' , 'arguments' => ['name']], ['name' => 'activeStudents'], ['name' => 'with' , 'arguments' => ['studentRecord', 'studentRecord.academicCycleSection.academicLevel']]]" :empty-state="[
                'heading' => 'No students yet',
                'description' => 'Add the first student record for this school.',
                'action' => [
                    'href' => route('students.create'),
                    'ability' => 'create',
                    'arguments' => [\App\Models\User::class, 'student'],
                    'label' => 'Add student',
                ],
            ]" :columns="[
                ['type' => 'image', 'property' => 'profile_photo_url'] ,
                ['property' => 'name'] ,
                ['property' => 'email'] ,
                ['property' => 'admission_number' ,'relation' => 'studentRecord'] ,
                ['property' => 'name', 'name' => 'Level' ,'relation' => 'studentRecord.academicCycleSection.academicLevel'] ,
                ['property' => 'name', 'name' => 'Section' ,'relation' => 'studentRecord.academicCycleSection'] ,
                ['name' => 'Enrollment', 'type' => 'enrollment-status', 'relation' => 'studentRecord'],
                ['name' => 'Account', 'type' => 'account-status'],
                ['type' => 'dropdown', 'name' => 'actions','links' => [
                    ['href' => 'students.edit', 'text' => 'Manage Profile', 'icon' => 'pencil', 'can' => 'update student'],
                    ['href' => 'students.show', 'text' => 'View', 'icon' => 'eye',],
                ]],
                ['type' => 'delete', 'name' => 'Delete', 'action' => 'students.destroy','can' => 'delete student']
            ]
            "/>
        @endhasanyrole
        @hasanyrole('parent')
            <livewire:datatable :model="App\Models\User::class" uniqueId="students-list-table" :filters="[
            ['name' => 'students'],
            ['name' => 'ofSchool'],
            ['name' => 'whereRelation', 'arguments' => ['parents', 'parent_records.user_id', auth()->user()->id]],
            ['name' => 'orderBy' , 'arguments' => ['name']],
            ['name' => 'activeStudents'],
            ['name' => 'with' , 'arguments' => ['studentRecord', 'studentRecord.academicCycleSection.academicLevel']]]"
            :columns="[
                ['property' => 'name'] ,
                ['property' => 'email'] ,
                ['property' => 'admission_number' ,'relation' => 'studentRecord'] ,
                ['property' => 'name', 'name' => 'Level' ,'relation' => 'studentRecord.academicCycleSection.academicLevel'] ,
                ['property' => 'name', 'name' => 'Section' ,'relation' => 'studentRecord.academicCycleSection'] ,
                ['name' => 'Enrollment', 'type' => 'enrollment-status', 'relation' => 'studentRecord'],
                ['type' => 'dropdown', 'name' => 'actions','links' => [
                    ['href' => 'students.show', 'text' => 'View', 'icon' => 'eye',  ],
                ]],
            ]
            "/>
        @endhasanyrole
    </div>
</div>
