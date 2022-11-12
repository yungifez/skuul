<div class="card">
    <div class="card-header">
        <h4 class="card-title">Assign student to parent</h4>
    </div>
    <div class="card-body">
        @livewire('display-validation-error')
        @livewire('loading-spinner')
        {{-- form for selecting user --}}
        <form action="{{route('parents.assign-students', $parent->id)}}" method="POST" class="col-12 d-md-flex px-0">
            <x-adminlte-select name="class" label="Class"  fgroup-class="col-md-3" enable-old-support wire:model="class">
                @isset($classes)
                    @foreach ($classes as $item)
                        <option value="{{$item['id']}}">{{$item['name']}}</option>
                    @endforeach
                @endisset
            </x-adminlte-select>
            <x-adminlte-select name="section" label="Section" fgroup-class="col-md-3" wire:model="section">
                @isset($sections)
                    @foreach ($sections as $item)
                        <option value="{{$item['id']}}">{{$item['name']}}</option>
                    @endforeach
                @endisset
            </x-adminlte-select>
            <x-adminlte-select name="student_id" label="Student" fgroup-class="col-md-3" wire:model="student">
                @isset($students)
                    @foreach ($students as $item)
                        <option value="{{$item['id']}}">{{$item['name']}}</option>
                    @endforeach
                @endisset
            </x-adminlte-select> 
            @csrf
            <div class='col-md-3 mt-auto mb-auto'>
                <x-adminlte-button label="Add student" theme="primary" type="submit" class="col-md-12"/>
            </div>
        </form>
        <div class="my-3">
            <div class="table-responsive">
                <style>
                    #children-list tr td,  #children-list tr th {
                        vertical-align: middle;
                        text-align: center;
                    }
                </style>
                <table id="children-list" style="width:100%" class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th style="width:%">
                                S/N
                            </th>
                            <th style="width:%">
                                Picture
                            </th>
                            <th style="width:%">
                                Name
                            </th>
                            <th style="width:%">
                                Class
                            </th>
                            <th style="width:%">
                                section
                            </th>
                            <th style="width:%">
                                Email
                            </th>
                            <th style="width:%">
                                
                            </th>
                        </tr>
                    </thead>
                    @foreach($children as $student)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td><img src="{{$student->profile_photo_url}}" alt="" class="rounded-circle" height="50px" width="50px"></td>
                            <td>{{ $student->name}}</td>
                            <td>@isset ($student->studentRecord->myClass)
                                {{$student->studentRecord->myClass->name}}
                            @endisset</td>
                            <td>@isset($student->studentRecord->section)
                                {{$student->studentRecord->section->name}}
                            @endisset</td>
                            <td>{{ $student->email}}</td>
                            <td>
                                <form action="{{route('parents.assign-students', $parent->id)}}" method="POST">
                                    <input type="hidden" name="student_id" value="{{$student->id}}">
                                    <input type="hidden" name="assign" value="0">
                                    @csrf
                                    <x-adminlte-button label="Remove student" theme="primary" type="submit" class="col-md-12"/>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
</div>