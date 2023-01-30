<x-adminlte-card title="Promotion details" theme="primary" icon="">
    <div>
        <h1 class="text-xl lg:text-2xl text-center font-bold my-3">Promotion details</h1>
        <x-show-table :body="[
            ['Old class', $promotion->oldClass->name],
            ['Old section', $promotion->oldSection->name],
            ['New class', $promotion->newClass->name],
            ['New Section', $promotion->newSection->name],
        ]"/>
    </div>
    <h4 class="font-bold text-center text-xl lg:text-2xl my-3">Students promoted</h4>
    <ul class="text-xl">
        @foreach ($students as $student)
            <li>{{$student->name}}</li>
        @endforeach
    </ul>
</x-adminlte-card>
