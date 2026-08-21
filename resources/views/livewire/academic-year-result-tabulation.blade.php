<div class="card">
    <div class="card-header">
        <h4 class="card-title">Academic Year Result Tabulation</h4>
    </div>
    <div class="card-body">
        <x-display-validation-errors/>
        {{-- loading spinner --}}
        <x-loading-spinner/>
        {{-- form for selecting class and section to display --}}
        <form wire:submit="tabulate('{{$class}}','{{$section}}')" class="d-md-flex my-3">
            <div class="md:grid grid-cols-2 gap-4">
                <div class="flex w-full flex-col gap-2">
                    <april:label for="class">Class</april:label>
                    <april:select id="class" name="class" wire:model.live="class">
                    @foreach ($classes as $item)
                        <option value="{{$item['id']}}">{{$item['name']}}</option>
                    @endforeach

                    </april:select>
                    @error('class')
                        <p class="text-sm text-destructive">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex w-full flex-col gap-2">
                    <april:label for="section">Section</april:label>
                    <april:select id="section" name="section" wire:model.live="section">
                    <option value="null">Entire Class</option>
                    @isset($sections)
                        @foreach ($sections as $item)
                            <option value="{{$item['id']}}">{{$item['name']}}</option>
                        @endforeach
                    @endisset

                    </april:select>
                    @error('section')
                        <p class="text-sm text-destructive">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <april:button type="submit" class="w-full md:w-1/4">
                View records
            </april:button>
        </form>
        {{-- table to display tabulation --}}
        @if ($createdTabulation === true)
            @livewire('mark-tabulation', ['tabulatedRecords' => $tabulatedRecords, 'totalMarksAttainableInEachSubject' => $totalMarksAttainableInEachSubject, 'subjects' => $subjects, 'title' => $title ?? ''],key(str()->random()))
                <april:button wire:click="$dispatch('print')" class="w-full md:w-3/12" >
                    <x-lucide-download class="mr-2 size-4" />
                    Print
                </april:button>
        @elseif($createdTabulation === false)
            <p class="text-center my-3 text-red-500 dark:text-red-300 ">Something went wrong. Make sure there are subjects in this class</p>
        @endisset
    </div>
</div>
