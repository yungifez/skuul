<div class="align-self-end d-flex flex-column">
    <x-adminlte-button theme="info" icon="fas fa-question" class="w-10 align-self-end ml-5"   data-toggle="collapse" data-target="#{{$target_id}}" aria-expanded="false" aria-controls="{{$target_id}}"/>
    <div class="collapse" id="{{$target_id}}">
        <div class="bg-white p-3">
            <p>{{$text}}</p>
        </div>
    </div>
</div>