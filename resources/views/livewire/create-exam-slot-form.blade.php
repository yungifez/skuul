<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create exam slot in {{$exam->name}}</h3>
    </div>
    <div class="card-body">
        <form action="{{route('exam-slots.store', $exam->id)}}" method="post" class="md:w-1/2">
            <x-display-validation-errors/>
            <x-input id="name" name="name" label="Exam slot name" placeholder="Enter Exam slot name"  />
            <x-textarea id="description" name="description" label="Description" placeholder="Enter description"  />
            <x-input id="total-marks" name="total_marks" label="Maximum marks obtainable" placeholder="Enter max mark"  type="number" />
            @csrf
            <x-button label="Create" theme="primary" icon="fas fa-key" type="submit" class="w-full md:w-1/2"/>
        </form>
    </div>
</div>