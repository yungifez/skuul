<x-adminlte-card title="{{$section->name}}" theme="primary" icon="fas fa-lg fa-star">
    <div>
        <h4 class="text-center text-semibold">Student list</h4> 
        <ol>
            @foreach ($students->sortBy('name') as $student)
                <li>
                    <a href="{{route('students.show', $student)}}">{{$student->name}}</a>
                </li>
            @endforeach
        </ol>
        <p>Contains {{$students->count()}} {{Str::plural('student', $students->count())}}</p>
    </div>
</x-adminlte-card>