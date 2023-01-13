@livewire('show-user-profile', ['user' => $applicant])

<div class="card">
    <div class="container card-body">
        <div class="row my-2">
            <h4 class="text-center col-12">Application information</h4>
            <table class="table col-12 table-bordered text-capitalize">
                <tbody class="">
                    <tr>
                        <th>Applying as:</th>
                        <td>{{$applicant->accountApplication->role->name ?? 'Not found'}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
