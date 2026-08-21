<div class="md:flex gap-2">
    <div class="flex w-full flex-col gap-2">
        <april:label for="nationality">Nationality</april:label>
        <april:select id="nationality" name="nationality" wire:model.live="nationality">
            <option value="">Not specified</option>
        @foreach ($nationalities as $item)
            <option value="{{$item}}">{{$item}}</option>
        @endforeach

        </april:select>
        @error('nationality')
            <p class="text-sm text-destructive">{{ $message }}</p>
        @enderror
    </div>
    <div class="flex w-full flex-col gap-2">
        <april:label for="state">State</april:label>
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
