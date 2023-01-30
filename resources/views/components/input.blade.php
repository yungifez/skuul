<div @class(["$groupClass flex flex-col my-2"])>
    <label for="{{$id}}" @class(["$labelClass font-bold my-3"])>{{$label}}</label>
    <input id={{$id}} name="{{$name}}" @class(["$class border border-gray-500 p-2 rounded dark:bg-transparent w-full", 'border-red-500 ' => $errors->has($name)]) {{$attributes}} value="{{old($name) ?? ($value != null ? $value : '')}}">
    @error($name)
        <p class="text-red-700 dark:text-red-500 my-2">{{$message}}</p>
    @enderror
</div>