<div class="card">
    <div class="card-header">
        <h4 class="card-title">Notices</h4>
    </div>
    <div class="card-body">
        <x-adminlte-datatable id="notice-list-table" :heads="['S/N', 'Title','From','Till' , '', '']" class='text-capitalize' >
            @foreach($notices as $notice)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>{{ $notice->title}}</td>
                    <td>{{ \Carbon\Carbon::parse($notice->start_date)->diffForHumans()}}</td>
                    <td>{{ \Carbon\Carbon::parse($notice->stop_date)->diffForHumans()}}</td>
                    <td>@livewire('dropdown-links', [
                        'links' => [
                        ['href' => route("notices.show", $notice->id), 'text' => 'View', 'icon' => 'fas fa-eye'],
                        ],
                    ],)</td>
                    <td>
                        @can('delete notice', $notice)
                            @livewire('delete-modal', ['modal_id' => $notice->id ,"action" => route('notices.destroy', $notice->id), 'item_name' => $notice->name])
                        @endcan
                    </td>
                </tr>
            @endforeach
        </x-adminlte-datatable>
    </div>
</div>