<div class="card">
    <div class="card-header">
        <h4 class="card-title">Exam list for semester {{ auth()->user()->school->semester->name}} </h4>
    </div>
    <div class="card-body">
        <livewire:datatable  uniqueId="list-exams-table" :model="App\Models\Exam::class"
        :filters="[
            ['name' => 'where' , 'arguments' => ['semester_id' , auth()->user()->School->semester->id]]
        ]"
        :columns="[
            ['property' => 'name'],
            ['property' => 'start_date'],
            ['property' => 'stop_date'],
            ['property' => 'active', 'type' => 'boolean-switch', 'action' => 'exams.set-active-status', 'field' => 'status', 'true-statement' => 'Active', 'false-statement' => 'Inactive'],
            ['property' => 'publish_result','type' => 'boolean-switch', 'action' => 'exams.set-publish-result-status', 'field' => 'status', 'true-statement' => 'Published', 'false-statement' => 'Not published'],
            ['name' => 'Actions', 'type' => 'dropdown' , 'links' => [
                ['href' => 'exams.edit', 'text' => 'edit', 'icon' => 'fas fa-cog'],
                ['href' => 'exam-slots.index', 'text' => 'Manage exam slots', 'icon' => 'fas fa-cog'],
                ['href' => 'exam-slots.create', 'text' => 'Create exam slots', 'Create exam slot', 'icon' => 'fas fa-key'],
            ]],
            ['type' => 'delete', 'name' => 'Delete', 'action' => 'exams.destroy',]
        ]"/>
    </div>
</div>
