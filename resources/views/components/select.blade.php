<div class="{{$groupClass}} my-2 flex flex-col gap-2">
    @isset($label)
        <april:label for="{{$id}}">{{$label}}</april:label>
    @endisset

    <april:select name="{{$name}}" id="{{$id}}" {{$attributes->merge(['class' => $class])}}>
        {{$slot}}
    </april:select>

    @error($name)
        <p class="text-sm text-destructive">{{$message}}</p>
    @enderror
</div>
