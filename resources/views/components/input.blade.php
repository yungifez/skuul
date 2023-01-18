<div @class(["$groupClass flex flex-col my-2"])>
    <label for="{{$id}}" @class(["$labelClass font-bold my-3"])>{{$label}}</label>
    <input id={{$id}} @class(["$class border border-gray-500 p-2 rounded dark:bg-transparent", 'border-red-500' => $errors->has($name)]) {{$attributes}}>
    @error($name)
        <p class="text-red-700 my-2">{{$message}}</p>
    @enderror
</div>