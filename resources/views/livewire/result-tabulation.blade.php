<div class="card">
    <div class="card-header">
        <h4 class="card-title">Exam tabilation</h4>
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
                <x-adminlte-select name="class" label="Select class"  fgroup-class="col-md-6" enable-old-support wire:model="class">
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
        @isset($subjects)
            @livewire('mark-tabulation', ['tabulatedRecords' => $tabulatedRecords, 'totalMarksAttainableInEachSubject' => $totalMarksAttainableInEachSubject, 'subjects' => $subjects])
        @endisset
    </div>
</div>
