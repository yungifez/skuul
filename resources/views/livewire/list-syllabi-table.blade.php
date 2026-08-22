<div class="card">
    <div class="card-header">
        <div class="card-title">Syllabi</div>
    </div>
    <div class="card-body">
        <p class="mb-5 text-sm text-muted-foreground">Each syllabus belongs to one course offering, so its subject, academic level, and period remain unambiguous.</p>
        <div wire:loading.remove.delay>
            <livewire:datatable unique-id="list-syllabi-table" :model="App\Models\Syllabus::class"
            :filters="[
                ['name' => 'inSchool'],
                ['name' => 'with', 'arguments' => ['courseOffering.subject', 'courseOffering.academicLevel', 'courseOffering.academicPeriod']],
                ['name' => 'latest'],
            ]"
            :columns="[
                ['property' => 'name'],
                ['property' => 'name', 'name' => 'Subject', 'relation' => 'courseOffering.subject'],
                ['property' => 'label', 'name' => 'Academic level', 'relation' => 'courseOffering.academicLevel'],
                ['property' => 'label', 'name' => 'Academic period', 'relation' => 'courseOffering.academicPeriod'],
                ['type' => 'dropdown', 'name' => 'actions','links' => [
                    ['href' => 'syllabi.show', 'text' => 'View', 'icon' => 'eye', 'can' => 'read syllabus'],
                ]],
                ['type' => 'delete', 'name' => 'Delete', 'action' => 'syllabi.destroy', 'can' => 'delete syllabus']
            ]"
            :empty-state="['heading' => 'No syllabi yet', 'description' => 'Upload a syllabus to share course material for a specific offering.', 'action' => ['href' => route('syllabi.create'), 'ability' => 'create', 'arguments' => [\App\Models\Syllabus::class], 'label' => 'Add syllabus']]"
            />
        </div>
        <x-loading-spinner/>
    </div>
</div>
