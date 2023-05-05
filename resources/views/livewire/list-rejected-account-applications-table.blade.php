<div class="card">
    <div class="card-header">
        <h4 class="card-title">Applicants</h4>
    </div>
    <div class="card-body">
        <livewire:datatable :model="App\Models\User::class"
        :filters="[
            ['name' => 'inSchool'],
            ['name' => 'rejectedApplicants'],
        ]"
        :columns="[
            ['property' => 'name'],
            ['property' => 'locked', 'name' => 'Locked' , 'type' => 'boolean-switch', 'action' => 'user.lock-account', 'field' => 'lock', 'true-statement' => 'Locked', 'false-statement' => 'Unlocked',  'can' => 'lock user'],
            ['type' => 'dropdown', 'name' => 'actions','links' => [
                ['href' => 'account-applications.edit', 'text' => 'Edit profile', 'icon' => 'fas fa-cog'],
                ['href' => 'account-applications.show', 'text' => 'View profile', 'icon' => 'fas fa-eye'],
                ['href' => 'account-applications.change-status', 'text' => 'Change Status ( make decision )', 'icon' => 'fas fa-balance-scale'],
                ]],
                ['type' => 'delete', 'name' => 'Delete', 'action' => 'account-applications.destroy']
        ]"/>
    </div>
</div>

