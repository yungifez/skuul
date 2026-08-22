<div class="card">
<div class="card-body">
    <div>
        <h1 class="text-xl lg:text-2xl text-center font-bold my-3">Promotion details</h1>
        <x-show-table :body="[
            ['From level', $promotion->sourceAcademicCycleSection?->academicLevel?->name],
            ['From section', $promotion->sourceAcademicCycleSection?->name],
            ['To level', $promotion->destinationAcademicCycleSection?->academicLevel?->name],
            ['To section', $promotion->destinationAcademicCycleSection?->name],
        ]"/>
    </div>
    <h4 class="font-bold text-center text-xl lg:text-2xl my-3">Students promoted</h4>
    <ul class="">
        @foreach ($students as $student)
            <li>{{$student->name}}</li>
        @endforeach
</div>
</div>
