<div class="card">
    <div class="card-header">
        <div class="flex items-center gap-1">
            <h3 class="card-title">Create subject</h3>
            <x-help-tooltip label="Subject catalog help">Subjects belong to the school catalog. Choose where and when they are taught, then assign teachers from a course offering.</x-help-tooltip>
        </div>
    </div>
    <div class="card-body">
        <form action="{{route('subjects.store')}}" method="POST" class="md:w-1/2">
            @if ($setup)
                <input type="hidden" name="setup" value="1">
                <input type="hidden" name="academic_year_id" value="{{ $academicYearId }}">
            @endif
            <x-display-validation-errors/>
            <april:input-group id="name" name="name" label="Subject Name" placeholder="Enter subject name" />
            <april:input-group id="short-name" name="short_name" label="Subject short Name" placeholder="Enter subject short name" />
            @csrf
            <april:button type="submit" class="w-full md:w-1/2">
                <x-lucide-key class="mr-2 size-4" />
                Create
            </april:button>
        </form>
    </div>
</div>
