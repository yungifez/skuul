<x-adminlte-card title="{{$section->name}}" theme="primary" icon="fas fa-lg fa-star">
    <div>
        <h4 class="text-center text-semibold">Contains {{$section->studentRecords()->count()}} {{Str::plural('student', $section->studentRecords()->count())}}</h4> 
        <ol>
            @foreach ($section->studentRecords as $student)
                <li><p>{{$student->user->name}}</p></li>
            @endforeach
        </ol>
    </div>
</x-adminlte-card>