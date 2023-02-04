<div class="card">
    <div class="card-header">
        <h4 class="card-title">Promotion list</h4>
    </div>
    <div class="card-body">
        <livewire:datatable :model="App\Models\Promotion::Class" :filters="[['name' => 'where' , 'arguments' => ['academic_year_id', $academicYear->id]], ['name' => 'with' , 'arguments' => ['oldClass','newClass', 'oldSection', 'newSection']]]" :columns="[
                ['property' => 'name', 'name' => 'Old Class' ,'relation' => 'oldClass'] , 
                ['property' => 'name', 'name' => 'New Class' ,'relation' => 'newClass'] , 
                ['property' => 'name', 'name' => 'Old Section' ,'relation' => 'oldSection'] , 
                ['property' => 'name', 'name' => 'New Section' ,'relation' => 'newSection'] , 
                ['method' => 'count', 'name' => 'New Section' ,'relation' => 'students'] , 
                ['type' => 'dropdown', 'name' => 'actions','links' => [
                    ['href' => 'students.promotions.show', 'text' => 'View Promoted Students', 'icon' => 'fas fa-eye',],
                ]],
                ['type' => 'delete', 'name' => 'Delete', 'action' => 'students.promotions.reset',]
            ]"/>
    </div>
</div>
