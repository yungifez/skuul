<div>
    @hasanyrole('admin|super-admin')
    <div class="card">
        <div class="card-body">
            @can('read school')
                <h2 class="font-bold text-center text-2xl my-2 ">Multi schools</h2>
                <div>
                    <x-info-box :title="$schools" text="Schools" icon="fas fa-school text-dark" colour="bg-red-600" text-colour="text-white" :url="route('schools.index')" url-text="View schools"/>
                </div>
            @endcan

            @can('manage school settings')
                <h4 class="font-bold text-center text-2xl my-4">School data</h4>
            @endcan

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                @can('read class group')
                    <x-info-box title="{{$classGroups}}" text="Class groups" colour="bg-orange-600"  text-colour="text-white" url="{{route('class-groups.index')}}" url-text="View class groups"/>
                @endcan
                @can('read class')
                    <x-info-box title="{{$classes}}" text="Classes" url="{{route('classes.index')}}" url-text="View classes" colour="bg-green-500"  text-colour="text-white"/>
                @endcan
                @can('read section')
                    <x-info-box title="{{$sections}}" text="Sections" url="{{route('sections.index')}}" url-text="View sections" colour="bg-lime-600"  text-colour="text-white" />
                @endcan
                @can('read student')
                    <x-info-box title="{{$students}}" text="Students (active)" icon=" text-dark" theme="yellow" url="{{route('students.index')}}" url-text="View students" colour="bg-blue-700"  text-colour="text-white"/>
                @endcan
                @can('read teacher')
                    <x-info-box title="{{$teachers}}" text="Teachers" icon=" text-dark" theme="orange" url="{{route('teachers.index')}}" url-text="View teachers" colour="bg-indigo-700"  text-colour="text-white"/>
                @endcan
                @can('read subject')
                    <x-info-box title="{{$parents}}" text="Parents" icon=" text-dark" theme="purple" url="{{route('parents.index')}}" url-text="View Parents"  colour="bg-violet-700"  text-colour="text-white"/>
                @endcan
            </div>
        </div>
    </div>
    @endhasanyrole
</div>
