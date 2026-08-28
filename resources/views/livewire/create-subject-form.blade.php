<div class="card">
    <div class="card-header">
        <div class="flex items-center gap-1">
            <h3 class="card-title">Create subject</h3>
            <x-help-tooltip label="Subject catalog help">A subject is the reusable name of what learners study, such as Mathematics, English, or Science. Later, add it to a class and reporting period for a specific school year.</x-help-tooltip>
        </div>
    </div>
    <div class="card-body">
        <form action="{{route('subjects.store')}}" method="POST" class="space-y-5 md:w-1/2">
            @if ($setup)
                <input type="hidden" name="setup" value="1">
                <input type="hidden" name="academic_year_id" value="{{ $academicYearId }}">
            @endif
            <x-display-validation-errors/>
            <april:input-group id="name" name="name" label="Subject name" placeholder="e.g. Mathematics" />
            <april:input-group id="short-name" name="short_name" label="Short name" placeholder="e.g. Maths" />
            @csrf
            <april:button type="submit" class="w-full sm:w-auto">
                <x-lucide-key class="mr-2 size-4" />
                Create subject
            </april:button>
        </form>
    </div>
</div>
