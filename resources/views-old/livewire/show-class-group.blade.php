
<x-adminlte-card title="{{$classGroup->name}}" theme="primary" icon="fas fa-lg fa-moon">
    <h4 class="text-center text-semibold">Contains {{$classGroup->classes()->count()}} {{Str::plural('class', $classGroup->classes()->count())}}</h4> 
    <ol>
        @foreach ($classGroup->classes as $class)
            <li><p>{{$class->name}}</p></li>
        @endforeach
    </ol>
</x-adminlte-card>