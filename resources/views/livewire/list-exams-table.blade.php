<div class="card">
    <div class="card-header">
        <h4 class="card-title">Exam list for semester {{ auth()->user()->school->semester->name}} </h4>
    </div>
    <div class="card-body">
        @livewire('display-validation-error')
        <x-adminlte-datatable id="school-list-table" :heads="['S/N','name', 'start date', 'stop date', '', '', '', '']" class='text-capitalize' >
            @foreach($exams as $exam)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>{{$exam->name}}</td>
                    <td>{{$exam->start_date}}</td>
                    <td>{{$exam->stop_date}}</td>
                    <td>
                        <form action="{{route('exams.set-status', $exam->id)}}" method="POST">
                            {{--set exam status to active or inactive--}}
                            <div class="custom-control custom-switch">
                                @csrf
                                <input name="status" type="checkbox" class="custom-control-input" id="customSwitch{{$exam->id}}" onChange="this.form.submit()"
                                    @if($exam->active == 'true') checked @endif >
                                <label class="custom-control-label" for="customSwitch{{$exam->id}}">
                                    @if($exam->active == 'true')
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </label>
                            </div>
                        </form>
                    </td>
                    <td>
                        <form action="{{route('exams.set-publish-result-status', $exam->id)}}" method="POST">
                            {{--set exam status to active or inactive--}}
                            <div class="custom-control custom-switch">
                                @csrf
                                <input name="status" type="checkbox" class="custom-control-input" id="result{{$exam->id}}" onChange="this.form.submit()"
                                    @if($exam->publish_result == 'true') checked @endif >
                                <label class="custom-control-label" for="result{{$exam->id}}">
                                    @if($exam->publish_result == 'true')
                                        <span class="badge badge-success">Result published</span>
                                    @else
                                        <span class="badge badge-danger">Result not published</span>
                                    @endif
                                </label>
                            </div>
                        </form>
                    </td>
                    <td>
                        @livewire('dropdown-links', [
                            'links' => [
                                ['href' => route("exams.edit", $exam->id), 'text' => 'edit', 'icon' => 'fas fa-cog'],
                                ['href' => route("exams.show", $exam->id), 'text' => 'View', 'icon' => 'fas fa-eye'],
                                ['href' => route("exam-slots.index", $exam->id), 'text' => 'Manage exam slots', 'icon' => 'fas fa-cog'],
                                ['href' => route("exam-slots.create", $exam->id), 'text' => 'Create exam slots', 'Create exam slot', 'icon' => 'fas fa-key'],
                            ],
                        ],)
                    </td>
                    <td>
                        @livewire('delete-modal', ['modal_id' => $exam->id ,"action" => route('exams.destroy', $exam->id), 'item_name' => $exam->name])
                    </td>
                </tr>
            @endforeach
        </x-adminlte-datatable>
    </div>
</div>