<div class="row">
<x-adminlte-select name="nationality" label="Nationality" fgroup-class="col-md-6"  wire:model="nationality" enable-old-support>
    @foreach ($nationalities as $item)
        <option value="{{$item}}">{{$item}}</option>
    @endforeach
</x-adminlte-select>
<x-adminlte-select name="state" label="State" fgroup-class="col-md-6" enable-old-support wire:init="loadInitialStates" wire:model="state">
    @if (isset($states))
        @foreach ($states as $item)
            <option value="{{$item['name']}}"  wire:key="{{ $loop->index }}">{{$item['name']}}</option>
        @endforeach
    @else 
        <option value="" disabled>Select a country first</option>
    @endif
</x-adminlte-select>
</div>
