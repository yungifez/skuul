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
    </div>
</div>
