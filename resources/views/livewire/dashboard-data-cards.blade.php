@hasanyrole('admin|super-admin')
<div class="card">
    <div class="my-4 card-body">
        @can('read school')
            <h4 class="text-bold text-center">Multi schools</h4>
            <div>
                <x-adminlte-small-box title="{{$schools}}" text="Schools" icon="fas fa-school text-dark" theme="teal" url="{{route('schools.index')}}" url-text="View schools"/>
            </div>
        @endcan
        
        @can('manage school settings')
            <h4 class="text-bold text-center">School data</h4>
        @endcan
        <div class="row">
            <div class="col-lg-4">
                @can('read class group')
                    <x-adminlte-small-box title="{{$classGroups}}" text="Class groups" icon=" text-dark" theme="red" url="{{route('class-groups.index')}}" url-text="View class groups"/>
                @endcan 
            </div>
            <div class="col-lg-4">
                @can('read class')
                    <x-adminlte-small-box title="{{$classes}}" text="Classes" icon=" text-dark" theme="blue" url="{{route('classes.index')}}" url-text="View classes" />
                @endcan
            </div>
            <div class="col-lg-4">
                @can('read section')
                    <x-adminlte-small-box title="{{$sections}}" text="Sections" icon=" text-dark" theme="green" url="{{route('sections.index')}}" url-text="View sections" />
                @endcan
            </div>
            <div class="col-lg-4">
                @can('read student')
                    <x-adminlte-small-box title="{{$students}}" text="Students (active)" icon=" text-dark" theme="yellow" url="{{route('students.index')}}" url-text="View students" />
                @endcan
            </div>
            <div class="col-lg-4">
                @can('read teacher')
                    <x-adminlte-small-box title="{{$students}}" text="Teachers" icon=" text-dark" theme="orange" url="{{route('teachers.index')}}" url-text="View teachers" />
                @endcan
            </div>
    
            <div class="col-lg-4">
                @can('read subject')
                    <x-adminlte-small-box title="{{$subjects}}" text="Subjects" icon=" text-dark" theme="purple" url="{{route('subjects.index')}}" url-text="View subjects" />
                @endcan
            </div>
        </div>
    
    </div>
</div>
@endhasanyrole