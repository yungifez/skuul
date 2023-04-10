<div class="card">
    <div class="card-header">
        <h2 class="card-title">{{$school->name}} details</h2>
    </div>
    <div class="card-body">
        <x-show-table :body="[
            [
                'Address', $school->address
            ],
            [
                'Initials', $school->initials
            ],
            [
                'Email', $school->email
            ],
            [
                'Phone', $school->phone
            ],
            [
                'Current Academic Year', $school?->academicYear?->name 
            ],
            [
                'Current Semester', $school?->semester?->name
            ],
            [
                'School code', $school->code
            ],
        ]"/>
    </div>
</div>
