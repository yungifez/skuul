<div class="card">
    <div class="card-header">
        <h2 class="card-title">{{$classGroup->name}}</h2>
    </div>
    <div class="card-body">
        <h4 class="text-center font-semibold text-2xl my-3">Contains {{$classGroup->classes->count()}} {{Str::plural('class', $classGroup->classes->count())}}</h4> 
        <ol>
            @foreach ($classGroup->classes as $class)
                <li class="my-2 text-lg"><a href="{{route('classes.show', $class->id)}}">{{$class->name}}</a></li>
            @endforeach
        </ol>

        <h4 class="text-center font-semibold text-2xl my-3">Grading system</h4> 
        <ol>
            <livewire:datatable :model="App\Models\ClassGroup::class" uniqueId="schoolTablepage" :filters="[['name' => 'find' , 'arguments' => [ $classGroup->id ]], ['name' => 'gradeSystem']]" :columns="
            [
            ['property' => 'name'] , 
            ['property' => 'remark'] , 
            ['property' => 'grade_from'] , 
            ['property' => 'grade_till'] , 
            ['type' => 'dropdown', 'name' => 'actions','links' => [
                ['href' => 'grade-systems.edit', 'text' => 'Settings', 'icon' => 'fas fa-cog'],
                ['href' => 'grade-systems.show', 'text' => 'View', 'icon' => 'fas fa-eye'],
            ]],
            ['type' => 'delete', 'name' => 'Delete', 'action' => 'grade-systems.destroy']
         ]
        "/>
        </ol>
    </div>
</div>
