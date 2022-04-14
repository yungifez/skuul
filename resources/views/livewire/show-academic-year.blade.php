<x-adminlte-card title="{{$academicYear->name()}}" theme="primary" icon="fas fa-lg fa-star">
    <div>
        <h4 class="text-center text-semibold">Contains {{$academicYear->semesters()->count()}} {{Str::plural('semester', $academicYear->semesters()->count())}}</h4> 
        <ol>
            @foreach ($academicYear->semesters as $semester)
                <li><p>{{$semester->name}}</p></li>
            @endforeach
        </ol>
    </div>
</x-adminlte-card>