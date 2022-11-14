<div>
    <p>Total obtainable marks in each subject: {{$totalMarksAttainableInEachSubject}}</p>
    <p>Total Marks across all subjects: {{$totalMarksAttainableInEachSubject * $subjects->count()}}</p>
    @php
        $heads = $subjects->pluck('name');
    @endphp
    {{--foreach displaus records in order of class positions--}}
    <div class="table-responsive">
        <style>
            #mark-tabulation tr td,  #mark-tabulation tr th {
                vertical-align: middle;
                text-align: center;
            }
        </style>
        <table class="table table-bordered table-striped" id="mark-tabulation">
            <thead class="thead-dark">
                <th>Class Position</th>
                <th>Name</th>
                <th>Admission Number</th>
                @foreach ($heads as $head)
                    <th class="hide-on-print">{{$head}}</th>   
                @endforeach
                <th>Total Marks</th>
                <th>Percentage (%)</th>
                <th>Grade</th>
            </thead>
             @foreach ($tabulatedRecords->sortByDesc('total') as $tabulatedRecord)
            <tr>
                <td>{{$loop->iteration}}</td>
                <td>{{$tabulatedRecord['student_name']}}</td>
                <td>{{$tabulatedRecord['admission_number']}}</td>
                @foreach ($tabulatedRecord['student_marks'] as $item)
                    <td class="hide-on-print">{{$item}}</td>
                @endforeach
                <td>{{$tabulatedRecord['total']}}</td>
                <td>{{$tabulatedRecord['percent']}}</td>
                <td>{{$tabulatedRecord['grade']}}</td>
            </tr>
            @endforeach
        </table>
    </div>
</div>