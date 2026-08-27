<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create syllabus</h3>
    </div>
    <div class="card-body">
        <form action="{{route('syllabi.store')}}" method="POST" enctype="multipart/form-data" class="md:w-1/2">
            @csrf
            <x-display-validation-errors/>
            <p class="text-secondary">
                {{__('All fields marked * are required')}}
            </p>
            <div class="flex w-full flex-col gap-2">
                <april:label for="course_offering_id">Course offering *</april:label>
                <april:select id="course_offering_id" name="course_offering_id">
                    <option value="">Select the subject, {{ strtolower(school_term('class_level', 'class')) }}, and {{ strtolower(school_term('period', 'period')) }}</option>
                    @foreach ($courseOfferings as $courseOffering)
                        <option value="{{ $courseOffering->id }}">
                            {{ $courseOffering->subject->name }} — {{ $courseOffering->academicLevel->name }} — {{ $courseOffering->academicPeriod->label ?? $courseOffering->academicPeriod->name }}
                        </option>
                    @endforeach
                </april:select>
                @error('course_offering_id')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>
            @if ($courseOfferings->isEmpty())
                <p class="rounded-md border border-warning/30 bg-warning/10 p-3 text-sm text-warning-foreground">
                    Create a course offering before uploading a syllabus. The offering defines who receives the work and when it applies.
                </p>
            @endif
            <april:input-group id="name" name="name" id="name" label="Name *" placeholder="Name (Eg: Physics second academic period syllabus) " wire:ignore />
            <div class="flex w-full flex-col gap-2 md:col-span-6">
                <april:label for="description">Description</april:label>
                <april:textarea id="description" name="description" placeholder="Insert description (optional)..." rows="5" />
            </div>
            <april:input-group id="file" type="file" name="file" accept="application/pdf" label="Upload file *" placeholder="Choose a PDF file..." fgroup-class="col-md-6" />
            <april:button type="submit" class="w-full md:w-6/12">
                <x-lucide-key class="mr-2 size-4" />
                Create
            </april:button>
        </form>
    </div>
</div>
