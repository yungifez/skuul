<div>
    <p class='text-bold'>Syllabus list</p>
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
                @if (isset($syllabus->description))
                    <h4 class="text-center text-bold">Description:</h4>
                    <p>{{$syllabus->description}}</p>
                @endif
                <a class="btn btn-secondary" href="{{asset($syllabus->file)}}">
                    Download
                </a>
            </x-adminlte-card>
        @endforeach
    @endisset
   
</div>
