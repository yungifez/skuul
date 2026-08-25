<div class="grid gap-4 sm:grid-cols-2">
    <div class="flex w-full flex-col gap-2">
        <april:label for="country">Country</april:label>
        <april:select id="country" name="country" wire:model.live="country">
            <option value="">Not specified</option>
        @foreach ($countries as $item)
            <option value="{{$item}}">{{$item}}</option>
        @endforeach

        </april:select>
        @error('country')
            <p class="text-sm text-destructive">{{ $message }}</p>
        @enderror
    </div>
    <div class="flex w-full flex-col gap-2">
        <april:label for="state">State / Province</april:label>
        <april:select id="state" name="state" wire:init="loadInitialStates" wire:model.live="state">
        @if (isset($states))
            @foreach ($states as $item)
                <option value="{{$item['name']}}"  wire:key="{{ $loop->index }}">{{$item['name']}}</option>
            @endforeach
        @else
            <option value="">Not specified</option>
        @endif

        </april:select>
        @error('state')
            <p class="text-sm text-destructive">{{ $message }}</p>
        @enderror
    </div>
</div>
