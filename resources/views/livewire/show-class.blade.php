<x-adminlte-card :title="$class->name.' overview'" theme="primary" icon="">
    <div>
        <p>Class group: {{$class->classGroup->name}} </p>
        <h4 class="text-center text-semibold">Section List</h4> 
        <ol>
            @foreach ($class->sections as $section)
                <li><a href="{{route('sections.show', $section->id)}}">{{$section->name}}</a></li>
            @endforeach
        </ol>
        <h4 class="text-center text-semibold">Subject List</h4>
        <div class="table-responsive">
            <table class="table col-md-8 table-bordered m-auto">
                <thead>
                    <th>Subject</th>
                    <th>Offered By</th>
                </thead>
                @foreach ($class->subjects as $subject)
                <tr>
                    <td>{{$subject->name}}</td>
                    <td> 
                        @foreach ($subject->teachers as $teacher)
                            {{$loop->first  ? '' : ','}}
                            <a href="{{route('teachers.show', $teacher)}}">{{$teacher->name}}</a>
                            {{$loop->last ? '.' : ''}}
                        @endforeach
                    </td>
                </tr>
            @endforeach
            </table>
        </div>
        <h4 class="text-center text-semibold mt-2">Student List (All sections)</h4> 
        <ol>
            @foreach ($students->sortBy('name') as $student)
                <li>
                    <a href="{{route('students.show', $student)}}">{{$student->name}}</a>
                </li>
            @endforeach
        </ol>
        <p>Contains {{$class->studentRecords->count()}} {{Str::plural('student', $class->studentRecords()->count())}}</p>
    </div>
</x-adminlte-card>