<div @class(["$groupClass flex flex-col"])>
    <label for="{{$id}}" @class(["$labelClass my-3"])>{{$label}}</label>
    <input type="{{$type}}" name="{{$name}}" @class(["$class border-2 border-gray-400 p-2 rounded"]) {{$attributes}}>
</div>