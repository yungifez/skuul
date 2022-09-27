<div class="card">
    <div class="card-header">
        <h4 class="card-title">Result tabulation</h4>
    </div>
    <div class="card-body">
        @livewire('display-validation-error')
        {{-- loading spinner --}}
        <div class="d-flex justify-content-center">
            <div wire:loading class="spinner-border" role="status">
                <p class="sr-only">Loading.....</p>
            </div>
        </div>
        {{-- form for selecting class and section to display --}}
        <form wire:submit.prevent="tabulate('{{$section}}')" class="d-md-flex my-3">
            <div class="d-md-flex col-md-10 px-0">
                <x-adminlte-select name="class" label="Class"  fgroup-class="col-md-6" enable-old-support wire:model="class">
                    @foreach ($classes as $item)
                        <option value="{{$item['id']}}">{{$item['name']}}</option>
                    @endforeach
                </x-adminlte-select>
                <x-adminlte-select name="section" label="Section" fgroup-class="col-md-6" wire:model="section">
                    @isset($sections)
                        @foreach ($sections as $item)
                            <option value="{{$item['id']}}">{{$item['name']}}</option>
                        @endforeach
                    @endisset
                </x-adminlte-select>
            </div>
            <div class='col-md-2 h-inherit mt-auto mb-auto'>
                <x-adminlte-button label="View records" theme="primary" type="submit" class="col-md-12"/>
            </div>
        </form>
        {{-- table to display tabulation --}}
        @if ($createdTabulation === true)
            @livewire('mark-tabulation', ['tabulatedRecords' => $tabulatedRecords, 'totalMarksAttainableInEachSubject' => $totalMarksAttainableInEachSubject, 'subjects' => $subjects],key(str()->random()))
            <div class='col-12 my-2'>
                <x-adminlte-button label="Print" theme="primary" icon="fas fa-download" wire:click="$emit('print')" class="col-md-3" />
            </div>
        @elseif($createdTabulation === false)
            <p>Something went wrong. Make sure there are students in this class</p>
        @endisset
    </div>
</div>
