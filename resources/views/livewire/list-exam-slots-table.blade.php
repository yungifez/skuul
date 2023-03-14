<div class="card">
    <div class="card-header">
        <h4 class="card-title">Exam slots in {{$exam->name}}</h4>
    </div>
    <div class="card-body">
        <livewire:datatable  uniqueId="list-exam-slot-table" :model="App\Models\ExamSlot::class"
        :filters="[
            ['name' => 'where' , 'arguments' => ['exam_id' , $exam->id]]
        ]"
        :columns="[
            ['property' => 'name'],
            ['property' => 'description'],
            ['property' => 'total_marks'],
            ['name' => 'Actions', 'type' => 'dropdown',  'can' => 'update exam slot'  , 'links' => [
                ['href' => 'exam-slots.edit', 'text' => 'edit', 'icon' => 'fas fa-cog', 'pre-route-parameters' => [$exam->id]],
            ]],
            ['type' => 'delete', 'name' => 'Delete', 'action' => 'exam-slots.destroy', 'pre-route-parameters' => [$exam->id],  'can' => 'delete exam slot']
        ]"/>
    </div>
</div>
