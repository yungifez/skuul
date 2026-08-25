<div class="card">
<div class="card-body">
    <div>
        <h1 class="text-xl lg:text-2xl text-center font-bold my-3">Promotion details</h1>
        <x-show-table :body="[
            ['From '.school_term('class_level', 'class'), $promotion->sourceAcademicCycleSection?->academicLevel?->name],
            ['From '.school_term('section', 'section'), $promotion->sourceAcademicCycleSection?->name],
            ['To '.school_term('class_level', 'class'), $promotion->destinationAcademicCycleSection?->academicLevel?->name],
            ['To '.school_term('section', 'section'), $promotion->destinationAcademicCycleSection?->name],
        ]"/>
    </div>
    <h4 class="font-bold text-center text-xl lg:text-2xl my-3">Students promoted</h4>
    <ul class="">
        @foreach ($students as $student)
            <li>{{$student->name}}</li>
        @endforeach
</div>
</div>
