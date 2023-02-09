<div class="my-3">
    <h3 class="text-center font-bold md:text-xl capitalize">{{$title ?? ''}}</h3>
    <div class="md:flex justify-evenly my-4 text-xs md:text-base">
        <p>Total obtainable marks in each subject: {{$totalMarksAttainableInEachSubject}}</p>
        <p>Total Marks attainable across all subjects: {{$totalMarksAttainableInEachSubject * $subjects->count()}}</p>
    </div>
    @php
        $heads = $subjects->pluck('name');
    @endphp
    {{--foreach displaus records in order of class positions--}}
    <div class="overflow-scroll beautify-scrollbar text-center">
        <table class="w-full" id="mark-tabulation">
            <thead class="">
                <th class="border p-4">Class Position</th>
                <th class="border p-4">Name</th>
                <th class="border p-4">Admission Number</th>
                @foreach ($heads as $head)
                    <th class="hide-on-print border p-4">{{$head}}</th>   
                @endforeach
                <th class="border p-4">Total Marks</th>
                <th class="border p-4">Percentage (%)</th>
                <th class="border p-4">Grade</th>
            </thead>
             @foreach ($tabulatedRecords->sortByDesc('total') as $tabulatedRecord)
            <tr>
                <td class="border p-4">{{$loop->iteration}}</td>
                <td class="border p-4">{{$tabulatedRecord['student_name']}}</td>
                <td class="border p-4">{{$tabulatedRecord['admission_number']}}</td>
                @foreach ($tabulatedRecord['student_marks'] as $item)
                    <td class="hide-on-print border p-4">{{$item}}</td>
                @endforeach
                <td class="border p-4">{{$tabulatedRecord['total']}}</td>
                <td class="border p-4">{{$tabulatedRecord['percent']}}</td>
                <td class="border p-4">{{$tabulatedRecord['grade']}}</td>
            </tr>
            @endforeach
        </table>
    </div>
</div>