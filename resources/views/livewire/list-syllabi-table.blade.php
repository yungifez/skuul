<div class="card">
    <div class="card-header">
        <div class="card-title">Syllabus List</div>
    </div>
    <div class="card-body">
        @if (!auth()->user()->hasRole('student'))
            <form action="">
                <x-adminlte-select id="my_class" label="Select a class to see syllabus"  fgroup-class="col-md-6" name="" wire:model="class">
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
        @isset($syllabi)
            @foreach ($syllabi as $syllabus)
                <x-adminlte-card title="{{$syllabus->name}}" theme="primary" icon=""  collapsible="collapsed" wire.loading.remove>
                    <p>Subject: {{$syllabus->subject->name}}</p>
                    @if (isset($syllabus->description))
                        <h4 class="text-center text-bold">Description:</h4>
                        <p>{{$syllabus->description}}</p>
                    @endif
                    <div class=" row">
                        <a class="btn btn-secondary  mx-2" href="{{asset('storage/'.$syllabus->file)}}" download>
                            <i class="fas fa-download"></i>
                            Download
                        </a> 
                        @can('delete syllabus')
                            @livewire('delete-modal', ['modal_id' => $syllabus->id ,"action" => route('syllabi.destroy', $syllabus->id), 'item_name' => $syllabus->name], key("delete-modal-".$syllabus->id))
                        @endcan
                    </div>
                </x-adminlte-card>
            @endforeach
        @else
            <p class="text-bold text-center" wire.loading.remove>No syllabus for this class at this time</p>
        @endisset
    </div>
</div>
