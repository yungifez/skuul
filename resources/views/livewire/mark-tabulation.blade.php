<div>
    <p>Total obtainable marks in each subject: {{$totalMarksAttainableInEachSubject}}</p>
    <p>Total Marks a cross all subjects: {{$totalMarksAttainableInEachSubject * $subjects->count()}}</p>
    @php
        $heads = $subjects->sortBy('name')->pluck('name');
        $heads = $heads->prepend('Admission number')->prepend('Student name')->prepend('Class Position');
        $heads = $heads->push('Total')->push('Percentage (%)')->push('Grade');
    @endphp
    {{--foreach displaus records in order of class positions--}}
    <div class="table-responsive">
        <table class="table table-bordered">
            @foreach ($heads as $head)
                <th>{{$head}}</th>   
            @endforeach
             @foreach ($tabulatedRecords->sortByDesc('total') as $tabulatedRecord)
            <tr>
                <td>{{$loop->iteration}}</td>
                <td>{{$tabulatedRecord['student_name']}}</td>
                <td>{{$tabulatedRecord['admission_number']}}</td>
                @foreach ($tabulatedRecord['student_marks'] as $item)
                    <td>{{$item}}</td>
                @endforeach
                <td>{{$tabulatedRecord['total']}}</td>
                <td>{{$tabulatedRecord['percent']}}</td>
                <td>{{$tabulatedRecord['grade']}}</td>
            </tr>
            @endforeach
        </table>
    </div>
</div>