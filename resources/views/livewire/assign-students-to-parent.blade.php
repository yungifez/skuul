<div class="card">
    <div class="card-header">
        <h4 class="card-title">Assign student to parent</h4>
    </div>
    <div class="card-body">
        <x-display-validation-errors/>
        {{-- form for selecting user --}}
        <form action="{{route('parents.assign-student', $parent->id)}}" method="POST" class=" md:grid grid-cols-3 gap-4">
            <x-select id="class" name="class" label="Class"  wire:model="class">
                @isset($classes)
                    @foreach ($classes as $item)
                        <option value="{{$item['id']}}">{{$item['name']}}</option>
                    @endforeach
                @endisset
            </x-select>
            <x-select id="section" name="section" label="Section" wire:model="section">
                @isset($sections)
                    @foreach ($sections as $item)
                        <option value="{{$item['id']}}">{{$item['name']}}</option>
                    @endforeach
                @endisset
            </x-select>
            <x-select id="student" name="student_id" label="Student" wire:model="student">
                @isset($students)
                    @foreach ($students as $item)
                        <option value="{{$item['id']}}">{{$item['name']}}</option>
                    @endforeach
                @endisset
            </x-select> 
            @csrf
            <x-button label="Add student" type="submit" class="w-full"/>
        </form>
        <x-loading-spinner/>

        <div class="my-3">
            <div class="table-responsive">
                <style>
                    #children-list tr td,  #children-list tr th {
                        vertical-align: middle;
                        text-align: center;
                    }
                </style>
                <table id="children-list" style="width:100%" class="table table-bordered table-striped">
                    <thead class="">
                        <tr class="bg-gray-900 text-white">
                            <th class="p-4 border">S/N</th>
                            <th class="p-4 border">Name</th>
                            <th class="p-4 border">Class</th>
                            <th class="p-4 border">section</th>
                            <th class="p-4 border">Email</th>
                            <th class="p-4 border">

                            </th>
                        </tr>
                    </thead>
                    @foreach($children as $student)
                        <tr>
                            <td class="p-4 border">{{$loop->iteration}}</td>
                            <td class="p-4 border">{{ $student->name}}</td>
                            <td class="p-4 border">@isset ($student->studentRecord->myClass)
                                {{$student->studentRecord->myClass->name}}
                            @endisset</td>
                            <td class="p-4 border">@isset($student->studentRecord->section)
                                {{$student->studentRecord->section->name}}
                            @endisset</td>
                            <td class="p-4 border">{{ $student->email}}</td>
                            <td class="p-4 border">
                                <form action="{{route('parents.assign-student', $parent->id)}}" method="POST">
                                    <input type="hidden" name="student_id" value="{{$student->id}}">
                                    <input type="hidden" name="assign" value="0">
                                    @csrf
                                    <x-button label="Remove student" theme="primary" type="submit" class="col-md-12"/>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
</div>