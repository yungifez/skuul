<x-adminlte-card title="{{$class->name}}" theme="primary" icon="fas fa-lg fa-moon">
    <div>
        <h4 class="text-center text-semibold">Contains {{$class->studentRecords()->count()}} {{Str::plural('student', $class->studentRecords()->count())}}</h4> 
        <ol>
            @foreach ($class->studentRecords as $student)
                <li><p>{{$student->user->name}}</p></li>
            @endforeach
        </ol>
    </div>
</x-adminlte-card>