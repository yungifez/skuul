@livewire('show-user-profile', ['user' => $student])
<div class="container">
    <div class="row my-2">
        <h4>Student information</h4>
        <table class="table col-12 table-bordered">
            <tbody class="">
                <tr>
                    <th scope="row">Class:</th>
                    <td>{{$student->studentRecord->myClass->name}}</td>
                </tr>
                <tr>
                    <th scope="row">Section:</th>
                    <td>{{$student->studentRecord->section->name}}</td>
                </tr>
                <tr>
                    <th scope="row">Admission no:</th>
                    <td>{{$student->studentRecord->admission_number}}</td>
                </tr>
                <tr>
                    <th scope="row">Admission Date:</th>
                    <td>{{$student->studentRecord->admission_date}}</td>
                </tr>
                <tr>
                    <th scope="row">Graduated:</th>
                    @if ($student->studentRecord->is_graduated == 0)
                        <td>False</td>
                    @else
                        <td>True</td>
                    @endif
                </tr>
            </tbody>
        </table>
    </div>
</div>
