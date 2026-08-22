<div class="card">
    <div class="card-header">
        <h4 class="card-title">Custom timetable items</h4>
    </div>
    <div class="card-body">
        <livewire:datatable :model="App\Models\CustomTimetableItem::class"
        :empty-state="['heading' => 'No timetable items yet', 'description' => 'Add reusable items for timetable planning.', 'action' => ['href' => route('custom-timetable-items.create'), 'ability' => 'create', 'arguments' => [\App\Models\CustomTimetableItem::class], 'label' => 'Add timetable item']]"
        :filters="[
            ['name' => 'inSchool']
        ]"
        :columns="[
            ['property' => 'name'],
            ['type' => 'dropdown' , 'name' => 'Actions', 'links' =>[
                ['href' => 'custom-timetable-items.edit', 'text' => 'edit', 'icon' => 'settings'],
            ]],
            ['type' => 'delete', 'name' => 'delete', 'action' => 'custom-timetable-items.destroy']
        ]"/>
    </div>
</div>
