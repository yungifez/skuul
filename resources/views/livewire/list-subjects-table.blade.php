<div class="card">
    <div class="card-header">
        <h4 class="card-title">Subject catalog</h4>
    </div>
    <div class="card-body">
        <p class="mb-5 text-sm text-muted-foreground">Keep each subject once in the school catalog. Course offerings decide the academic level, home group, period, and teachers who teach it.</p>
        <livewire:datatable
            unique-id="subject-catalog-table"
            :model="App\Models\Subject::class"
            :filters="[['name' => 'inSchool'], ['name' => 'with', 'arguments' => ['courseOfferings']], ['name' => 'orderBy', 'arguments' => ['name']]]"
            :empty-state="['heading' => 'No subjects yet', 'description' => 'Add a subject to the school catalog, then create an offering when it will be taught.', 'action' => ['href' => route('subjects.create'), 'ability' => 'create', 'arguments' => [\App\Models\Subject::class], 'label' => 'Add subject']]"
            :columns="[
                ['property' => 'name'],
                ['property' => 'short_name', 'name' => 'Short name'],
                ['name' => 'Course offerings', 'method' => 'count', 'relation' => 'courseOfferings'],
                ['type' => 'dropdown', 'name' => 'Actions', 'links' => [
                    ['href' => 'subjects.edit', 'text' => 'Edit', 'icon' => 'settings'],
                ]],
                ['type' => 'delete', 'name' => 'Delete', 'action' => 'subjects.destroy'],
            ]"
        />
    </div>
</div>
