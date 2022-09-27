<div class="card">
    <div class="card-header">
        <h4 class="card-title">{{$school->name}} details</h4>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
              <tr>
                <th scope="col">Key</th>
                <th scope="col">Value</th>
              </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Address</td>
                    <td>{{$school->address}}</td>
                </tr>
                <tr>
                    <td>Initials</td>
                    <td>{{$school->initials}}</td>
                </tr>
                @if ($school->academicYear)
                    <tr>
                        <td>Current academic year</td>
                        <td>{{$school->academicYear->name()}}</td>
                    </tr>
                @endif
                @if ($school->semester)
                    <tr>
                        <td>Current academic year</td>
                        <td>{{$school->semester->name}}</td>
                    </tr>
                @endif
                <tr>
                    <td>School code</td>
                    <td>{{$school->code}}</td>
                </tr>
            </tbody>
        </table>      
    </div>
</div>
