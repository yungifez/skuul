<div class="card">
    <div class="card-header">
        <h4 class="card-title">Result tabulation</h4>
    </div>
    <div class="card-body">
        <x-display-validation-errors/>
        {{-- loading spinner --}}
        <x-loading-spinner/>
        {{-- form for selecting class and section to display --}}
        <form wire:submit="tabulate('{{$class}}','{{$section}}')" class="d-md-flex my-3">
            <div class="md:grid grid-cols-2 gap-4">
                <x-select id="class" name="class" label="Class"    wire:model.live="class">
                    @foreach ($classes as $item)
                        <option value="{{$item['id']}}">{{$item['name']}}</option>
                    @endforeach
                </x-select>
                <x-select id="section" name="section" label="Section"  wire:model.live="section">
                    <option value="null">Entire Class</option>
                    @isset($sections)
                        @foreach ($sections as $item)
                            <option value="{{$item['id']}}">{{$item['name']}}</option>
                        @endforeach
                    @endisset
                </x-select>
            </div>
            <x-button label="View records" theme="primary" type="submit" class="w-full md:w-1/4"/>
        </form>
        {{-- table to display tabulation --}}
        @if ($createdTabulation === true)
            @livewire('mark-tabulation', ['tabulatedRecords' => $tabulatedRecords, 'totalMarksAttainableInEachSubject' => $totalMarksAttainableInEachSubject, 'subjects' => $subjects, 'title' => $title ?? ''],key(str()->random()))
                <x-button label="Print" theme="primary" icon="fas fa-download" wire:click="$dispatch('print')" class="w-full md:w-3/12" />
        @elseif($createdTabulation === false)
            <p class="text-center my-3 text-red-500 dark:text-red-300">Something went wrong. Make sure there are subjects in this class</p>
        @endisset
    </div>
</div>
