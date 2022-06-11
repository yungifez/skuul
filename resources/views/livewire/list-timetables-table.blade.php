<div class="card">
    <div class="card-header">
        <h4 class="card-title">Timetables</h4>
    </div>
    <div class="card-body">
        {{--Select statement for switching classes--}}
        @if (!auth()->user()->hasRole('student'))
            <form action="">
                <x-adminlte-select id="my_class" label="Select a class to see timetable"  fgroup-class="col-md-6" name="" wire:model="class">
                    @foreach ($classes as $item)
                        <option value="{{$item['id']}}">{{$item['name']}}</option>
                    @endforeach
                </x-adminlte-select>
            </form>
        @endif
        <div class="d-flex justify-content-center">
            <div wire:loading class="spinner-border" role="status">
                <p class="sr-only">Loading.....</p>
            </div>
        </div>
        
        @isset($timetables)
            @foreach ($timetables as $timetable)
                <x-adminlte-card title="{{$timetable->name}}" theme="primary" icon=""  collapsible="collapsed" :wire:key="$loop->index" wire:loading.remove>
                    <div class="d-grid gap-2 ">
                        @can('read timetable')
                            <a class="btn btn-primary" href="{{route('timetables.show',$timetable->id)}}">
                                <i class="fas fa-eye"></i>
                                View
                            </a> 
                        @endcan
                        @can('update timetable')
                            <a class="btn btn-primary " href="{{route('timetables.edit',$timetable->id)}}">
                                <i class="fas fa-pen"></i>
                                Edit
                            </a> 
                            <a class="btn btn-primary" href="{{route('timetables.manage',$timetable->id)}}">
                                <i class="fas fa-cog"></i>
                                Manage
                            </a> 
                        @endcan
                        @can('delete timetable')
                            @livewire('delete-modal', ['modal_id' => $timetable->id ,"action" => route('timetables.destroy', $timetable->id), 'item_name' => $timetable->name,"button_class" => ' my-3'], key("delete-modal-".$timetable->id))
                        @endcan
                    </div>
                </x-adminlte-card>
            @endforeach
        @else
            <p class="text-center text-bold" wire:loading.remove>No Timetable for this class at this time</p>
        @endisset
    </div>
</div>
