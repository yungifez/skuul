<div class="card">
    <div class="card-header">
        <h4 class="card-title">Exam tabulation</h4>
    </div>
    <div class="card-body">
        <x-display-validation-errors/>
        {{-- loading spinner --}}
        <x-loading-spinner/>
        {{-- form for selecting class and section to display --}}
        <form wire:submit="tabulate('{{$exam}}','{{$class}}' ,'{{$section}}')" class="md:grid grid-cols-3 gap-4">
                <div class="flex w-full flex-col gap-2">
                    <april:label for="exam">Select exam</april:label>
                    <april:select id="exam" name="exam_id" wire:model.live="exam">
                    @foreach ($exams as $item)
                        <option value="{{$item['id']}}">{{$item['name']}}</option>
                    @endforeach

                    </april:select>
                    @error('exam_id')
                        <p class="text-sm text-destructive">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex w-full flex-col gap-2">
                    <april:label for="class">Select class</april:label>
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
                    @isset($sections)
                        <option value="null">Entire Class</option>
                        @foreach ($sections as $item)
                            <option value="{{$item['id']}}">{{$item['name']}}</option>
                        @endforeach
                    @endisset

                    </april:select>
                    @error('section')
                        <p class="text-sm text-destructive">{{ $message }}</p>
                    @enderror
                </div>

            <april:button type="submit" class="w-full ">
                View records
            </april:button>
        </form>
        {{-- table to display tabulation --}}
        @if($tabulatedRecords && $createdTabulation == true)
            @livewire('mark-tabulation', ['tabulatedRecords' => $tabulatedRecords, 'totalMarksAttainableInEachSubject' => $totalMarksAttainableInEachSubject, 'subjects' => $subjects, 'title' => $title] ,key(str()->random()))
            <div class='col-12 my-2'>
                <april:button wire:click="$dispatch('print')" class="w-full md:w-3/12">
                    <x-lucide-download class="mr-2 size-4" />
                    Print
                </april:button>
            </div>
        @elseif (isset($error))
            <p class="text-center text-red-700 dark:text-red-300 my-3">Something went wrong, {{$error}}</p>
        @endisset
    </div>
</div>
