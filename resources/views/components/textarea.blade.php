<div @class(["$groupClass flex flex-col gap-2 my-2"])>
    @isset($label)
        <april:label for="{{$id}}" class="{{$labelClass}}">{{$label}}</april:label>
    @endisset
    <textarea id="{{$id}}" name="{{$name}}" data-slot="textarea" @class(["$class flex min-h-[80px] rounded-md border bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 border-input", 'border-destructive' => $errors->has($name)]) {{$attributes}}>{{old($name) ?? ($slot != null ? $slot : '')}}</textarea>
    @error($name)
        <p class="text-sm text-destructive">{{$message}}</p>
    @enderror
</div>
