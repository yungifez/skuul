<div>
    <p class='text-bold'>Syllabus list 
    </p>
    @if (!auth()->user()->hasRole('student'))
        <form action="">
            <x-adminlte-select id="my_class" label="Select a class to see syllabus"  fgroup-class="col-md-6" name="" wire:model="class">
                @foreach ($classes as $item)
                    <option value="{{$item['id']}}">{{$item['name']}}</option>
                @endforeach
            </x-adminlte-select>
        </form>
    @endif
    @isset($syllabi)
        @foreach ($syllabi as $syllabus)
            <x-adminlte-card title="{{$syllabus->name}}" theme="primary" icon=""  collapsible="collapsed">
                <p>Subject: {{$syllabus->subject->name}}</p>
                @if (isset($syllabus->description))
                    <h4 class="text-center text-bold">Description:</h4>
                    <p>{{$syllabus->description}}</p>
                @endif
                <div class="my-2 row">
                    <a class="btn btn-secondary col-2 mx-2" href="{{asset('storage/'.$syllabus->file)}}" download>
                        <i class="fas fa-download"></i>
                        Download
                    </a> 
                    @can('update syllabus')
                        <a class="btn btn-primary col-2 mx-2" href="{{route('syllabi.edit',$syllabus->id)}}">
                            <i class="fas fa-pen"></i>
                            Edit
                        </a> 
                    @endcan
                    @can('delete syllabus')
                        @livewire('delete-modal', ['modal_id' => $syllabus->id ,"action" => route('syllabi.destroy', $syllabus->id), 'item_name' => $syllabus->name], key($loop->index))
                    @endcan
                </div>
            </x-adminlte-card>
        @endforeach
    @endisset
   
</div>
