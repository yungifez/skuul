<div class="card">
    <div class="card-header">
        <h4 class="card-title">Create Notice</h4>
    </div>
    <div class="card-body">
        <form action="{{route('notices.store')}}" method="post" enctype="multipart/form-data" class="md:w-1/2">
            <x-display-validation-errors/>
            <april:input-group id="title" name="title" label="Notice title" placeholder="Enter Notice title" />
            <div class="flex w-full flex-col gap-2">
                <april:label for="content">Notice content/body</april:label>
                <april:editor
                    name="content"
                    :value="old('content', '')"
                    placeholder="Write the announcement..."
                    bold
                    italic
                    heading
                    bullet-list
                    ordered-list
                    blockquote
                    link
                    undo
                    redo
                />
            </div>
            <april:input-group type="date" id="start_date" name="start_date" label="Start date" required />
            <april:input-group type="date" id="stop_Date" name="stop_date" label="Stop date" />
            <fieldset class="flex flex-col gap-3 rounded border border-slate-200 p-4 dark:border-slate-700">
                <legend class="px-1 text-sm font-medium text-slate-900 dark:text-slate-100">Who should receive this?</legend>
                <p class="text-sm text-slate-600 dark:text-slate-300">Leave {{ strtolower(school_terms('section', 'sections')) }} empty for the whole school community. Select {{ strtolower(school_terms('section', 'sections')) }} to send to those learners only.</p>
                <label for="academic_cycle_section_ids" class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ school_terms('section', 'Sections') }}</label>
                <select id="academic_cycle_section_ids" name="audience[academic_cycle_section_ids][]" multiple class="min-h-28 rounded border border-slate-300 bg-white px-3 py-2 text-slate-900 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
                    @foreach($sections as $section)
                        <option value="{{ $section->id }}">{{ $section->academicLevel?->name ?? 'Unassigned '.strtolower(school_term('class_level', 'class')) }} — {{ $section->label ?? $section->name }}</option>
                    @endforeach
                </select>
                <label class="flex items-start gap-2 text-sm text-slate-800 dark:text-slate-100">
                    <input type="hidden" name="audience[include_guardians]" value="0">
                    <input type="checkbox" name="audience[include_guardians]" value="1" class="mt-0.5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    <span>Also send this to the selected learners’ guardians.</span>
                </label>
            </fieldset>
            @csrf
            <april:input-group id="file" type="file" name="attachment" accept=".gif,.jpg,.jpeg,.png,.doc,.docx,.pdf" label="Upload file" placeholder="Choose a file...(optional)" />
            <div class='col-12 my-2'>
                <april:button type="submit" class="w-full md:w-1/2">
                    <x-lucide-key class="mr-2 size-4" />
                    Create
                </april:button>
            </div>
        </form>
    </div>
</div>

@pushOnce('scripts')
    @aprilEditorScripts
@endPushOnce
