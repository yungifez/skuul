<x-adminlte-card title="Promotion details" theme="primary" icon="">
    <div>
       <p>Old class: {{$promotion->oldClass->name}}</p>
       <p>Old section: {{$promotion->oldSection->name}}</p>
       <p>New class: {{$promotion->newClass->name}}</p>
       <p>New Section: {{$promotion->newSection->name}}</p>
    </div>
    <h4 class="text-bold text-center">Students promoted</h4>
    <ul>
        @foreach ($students as $student)
            <li>{{$student->name}}</li>
        @endforeach
    </ul>
</x-adminlte-card>