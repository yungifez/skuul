<div class="card">
    <div class="card-header">
        <h4 class="card-title">Custom timetable items</h4>
    </div>
    <div class="card-body">
        <livewire:datatable :model="App\Models\CustomTimetableItem::class"
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
