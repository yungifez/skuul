@livewire('show-user-profile', ['user' => $applicant])

<div class="card">
    <div class="card-body">
        <div class="w-full md:w-8/12 m-auto">
            <h4 class="text text-xl m-2">Application information</h4>
                <x-show-table :body="[
                    ['Role',ucfirst($applicant->accountApplication->role->name)],
                    ['status', $applicant->accountApplication->status]
                ]"/>
        </div>
    </div>
</div>
