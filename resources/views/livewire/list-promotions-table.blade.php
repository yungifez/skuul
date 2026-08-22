<div class="card">
    <div class="card-header">
        <h4 class="card-title">Promotion list</h4>
    </div>
    <div class="card-body">
        <livewire:datatable :model="App\Models\Promotion::Class" :filters="[['name' => 'where' , 'arguments' => ['academic_year_id', $academicYear->id]], ['name' => 'with' , 'arguments' => ['sourceAcademicCycleSection.academicLevel', 'destinationAcademicCycleSection.academicLevel']]]" :columns="[
                ['property' => 'name', 'name' => 'From level' ,'relation' => 'sourceAcademicCycleSection.academicLevel'] ,
                ['property' => 'name', 'name' => 'From section' ,'relation' => 'sourceAcademicCycleSection'] ,
                ['property' => 'name', 'name' => 'To level' ,'relation' => 'destinationAcademicCycleSection.academicLevel'] ,
                ['property' => 'name', 'name' => 'To section' ,'relation' => 'destinationAcademicCycleSection'] ,
                ['method' => 'count', 'name' => 'Learners' ,'relation' => 'students'] ,
                ['type' => 'dropdown', 'name' => 'actions','links' => [
                    ['href' => 'students.promotions.show', 'text' => 'View Promoted Students', 'icon' => 'eye',],
                ]],
                ['type' => 'delete', 'name' => 'Delete', 'action' => 'students.promotions.reset',]
            ]"/>
    </div>
</div>
