<div class="card">
    <div class="card-header">
        <h4 class="card-title">Exam tabulation</h4>
    </div>
    <div class="card-body">
        <x-display-validation-errors/>
        {{-- loading spinner --}}
        <x-loading-spinner/>
        {{-- form for selecting class and section to display --}}
        <form wire:submit.prevent="tabulate('{{$exam}}','{{$class}}' ,'{{$section}}')" class="md:grid grid-cols-3 gap-4">
                <x-select id="exam" name="exam_id" label="Select exam" wire:model="exam">
                    @foreach ($exams as $item)
                        <option value="{{$item['id']}}">{{$item['name']}}</option>
                    @endforeach
                </x-select>
                <x-select id="class" name="class" label="Select class" wire:model="class">
                    @foreach ($classes as $item)
                        <option value="{{$item['id']}}">{{$item['name']}}</option>
                    @endforeach
                </x-select>
                <x-select id="section" name="section" label="Section" wire:model="section">
                    @isset($sections)
                        <option value="null">Entire Class</option>
                        @foreach ($sections as $item)
                            <option value="{{$item['id']}}">{{$item['name']}}</option>
                        @endforeach
                    @endisset
                </x-select>

            <x-button label="View records" theme="primary" type="submit" class="w-full md:w-5/12"/>
        </form>
        {{-- table to display tabulation --}}
        @if($tabulatedRecords && $createdTabulation == true)
            @livewire('mark-tabulation', ['tabulatedRecords' => $tabulatedRecords, 'totalMarksAttainableInEachSubject' => $totalMarksAttainableInEachSubject, 'subjects' => $subjects, 'title' => $title] ,key(str()->random()))
            <div class='col-12 my-2'>
                <x-button label="Print" theme="primary" icon="fas fa-download" wire:click="$emit('print')" class="w-full md:w-3/12"/>
            </div>
        @elseif (isset($error))
            Something went wrong, {{$error}}.
        @endisset
    </div>
</div>
