<div class="card">
    <div class="card-header">
        <h4 class="card-title">Applicants</h4>
    </div>
    <div class="card-body">
        <x-adminlte-datatable id="applicants-list-table" :heads="['S/N', 'Photo', 'Name','email' , 'Type', '', '']" class='text-capitalize' bordered striped head-theme="dark" beautify>
            @foreach($applicants as $applicant)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td><img src="{{$applicant->profile_photo_url}}" alt="" class="rounded-circle" height="50px" width="50px"></td>
                    <td>{{$applicant->name}}</td>
                    <td style="text-transform: none">{{$applicant->email}}</td>
                    <td>{{$applicant->accountApplication->role->name ?? 'Not found'}}</td>
                    <td>@livewire('dropdown-links', [
                        'links' => [
                        ['href' => route("account-applications.edit", $applicant->id), 'text' => 'Edit profile', 'icon' => 'fas fa-cog'],
                        ['href' => route("account-applications.show", $applicant->id), 'text' => 'View profile', 'icon' => 'fas fa-eye'],
                        ['href' => route("account-applications.change-status", $applicant->id), 'text' => 'Change Status ( make decision )', 'icon' => 'fas fa-balance-scale'],
                        ],
                    ],)</td>
                    <td>
                        @livewire('delete-modal', ['modal_id' => $applicant->id ,"action" => route('account-applications.destroy', $applicant->id), 'item_name' => $applicant->name])
                    </td>
                </tr>
            @endforeach
        </x-adminlte-datatable>
    </div>
</div>

