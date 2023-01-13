<div class="card">
    <div class="card-header">
        <h1 class="card-title">Application Status</h1>
    </div>
    @if (!is_null($applicant->accountApplication))  
        <div class="card-body">
            
            <h3 class="m-2 text-center text-bold">Account Application History</h3>
            @if ($statusHistory->isNotEmpty())
                @foreach ($applicant->accountApplication->statuses as $item)
                    @if (!$loop->first)
                        <div class="m-auto col-md-9">
                            <i class="fas fa-arrow-circle-up fa-2x"></i>  
                        </div>
                    @endif
                    <x-adminlte-card title="{{ucwords($item->name)}}" theme="lightblue" theme-mode="outline" class="col-md-9 mx-auto my-3">
                        <p class="text-left">{{$item->reason}}</p>
                    </x-adminlte-card>
                @endforeach
            @else
                <p class="text-center">No status History</p>
            @endif
            <div class="d-flex justify-content-center">
                <div wire:loading class="spinner-border" role="status">
                    <p class="sr-only">Loading.....</p>
                </div>
            </div>
            @can('change account application status')
                <form action="{{route('account-applications.change-status', $applicant->id)}}" method="POST" class="col-md-9 m-auto">
                    <x-adminlte-select id="name" name="status" label="Status"
                    wire:model="status" enable-old-support>
                        @foreach ($statuses as $status)
                            <option value="{{$status}}">{{ucwords($status)}}</option>
                        @endforeach
                    </x-adminlte-select>
                    <x-adminlte-input name="reason" label="Optional Reason/Message" placeholder="Application status reason or message eg user action required, no profile picture." enable-old-support/>
                    @if ($studentRecordFields == true)
                        @livewire('create-student-record-fields')
                    @endif
                    @csrf
                    <div class='col-12 my-2'>
                        <x-adminlte-button label="Change status" theme="primary" icon="fas fa-key" type="submit" class="col-md-3"/>
                    </div>
                </form>
            @endcan

        </div>
    @else
        <div class="card-body">
            <p>Application approved or user does not have an application record, <a href="{{route('account-applications.index')}}">Go back</a></p>
        </div>
    @endif
</div>
