@props(['value' => date('Y')])

<x-select x-data="{ 'years': [...Array(400)].map((_, i) => i + 1900) }" {{$attributes}}>
    <template x-for="year in years">
        <option :selected="{{$value}} == year" :value="year" x-text="year"></option>
    </template>
</x-select>