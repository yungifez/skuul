<div class="flex flex-col">
    @isset($label) 
        <label for="{{$id}}" class="my-3 font-bold ">{{$label}}</label>
    @endisset
    <select name="{{$name}}" id="{{$id}}" @class(["$class p-2 border border-gray-400 focus:border-blue-500 dark:bg-gray-800", 'border-red-500' => $errors->has($name)]) {{$attributes}}>
        {{$slot}}
    </select>
    @error($name)
        <p class="text-red-700 my-2">{{$message}}</p>
    @enderror
</div>