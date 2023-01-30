<div class="card">
    <div class="card-header">
        <h1 class="card-title">Application Status</h1>
    </div>
    @if (!is_null($applicant->accountApplication))  
        <div class="card-body">
            
            <h3 class="m-2 text-center text-bold text-2xl font-bold">Account Application History</h3>
            <livewire:application-history :applicant="$applicant"/>
            <x-loading-spinner/>
            
            @can('change account application status')
                <form action="{{route('account-applications.change-status', $applicant->id)}}" method="POST" class="col-md-9 m-auto">
                    <x-select id="name" name="status" label="Status"
                    wire:model="status" >
                        @foreach ($statuses as $status)
                            <option value="{{$status}}">{{ucwords($status)}}</option>
                        @endforeach
                    </x-select>
                    <x-input id="reason" name="reason" label="Optional Reason/Message" placeholder="Application status reason or message eg user action required, no profile picture." />
                    @if ($studentRecordFields == true)
                        <livewire:create-student-record-fields/>
                    @endif
                    @csrf
                    <x-button label="Change status" icon="fas fa-key" type="submit" class="w-full md:w-1/4"/>
                </form>
            @endcan

        </div>
    @else
        <div class="card-body">
            <p>Application approved or user does not have an application record, <a href="{{route('account-applications.index')}}"" class="text-blue-500">Go back</a></p>
        </div>
    @endif
</div>
