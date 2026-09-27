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
                <x-field-error name="course_offering_id" />
            </div>
            @if ($courseOfferings->isEmpty())
                <p class="rounded-md border border-warning/30 bg-warning/10 p-3 text-sm text-warning-foreground">
                    No course offering is open to you. A syllabus belongs to an offering that is still in use, and teachers add syllabi only for the offerings they are assigned to teach.
                </p>
            @endif
            <april:input-group id="name" name="name" id="name" label="Name *" placeholder="Name (Eg: Physics second academic period syllabus) " wire:ignore />
            <div class="flex w-full flex-col gap-2 md:col-span-6">
                <april:label for="description">Overview</april:label>
                <april:textarea id="description" name="description" placeholder="Insert description (optional)..." rows="5" />
            </div>
            <april:input-group id="file" type="file" name="file" accept="application/pdf" label="Attach a PDF (optional)" placeholder="Choose a PDF file..." fgroup-class="col-md-6" />
            <p class="text-sm text-muted-foreground">The syllabus starts as a draft. Next, you plan its weekly topics, then publish it to students.</p>
            <april:button type="submit" class="w-full md:w-6/12">
                Create draft
            </april:button>
        </form>
    </div>
</div>
