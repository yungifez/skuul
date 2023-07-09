<div @class(["$groupClass flex flex-col my-2"])>
    <label for="{{$id}}"  @class(["$labelClass font-semibold my-3"])>{{$label}}</label>
    <textarea id="{{$id}}" name="{{$name}}" @class(["$class border border-gray-500 p-2 rounded dark:bg-transparent", 'border-red-500' => $errors->has($name)]) {{$attributes}}>{{old($name) ?? ($slot != null ? $slot : '')}}</textarea>
    @error($name)
        <p class="text-red-700 dark:text-red-500 my-2">{{$message}}</p>
    @enderror
</div>