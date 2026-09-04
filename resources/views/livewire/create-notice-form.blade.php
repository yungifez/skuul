@php
    $selectedAcademicLevelIds = collect(old('audience.academic_level_ids', []))
        ->map(fn ($levelId): string => (string) $levelId)
        ->all();
    $selectedSectionIds = collect(old('audience.academic_cycle_section_ids', []))
        ->map(fn ($sectionId): string => (string) $sectionId)
        ->all();
    $audienceScope = old('audience.scope')
        ?? ($selectedAcademicLevelIds !== [] ? 'class' : ($selectedSectionIds !== [] ? 'section' : 'school'));
    $includeGuardians = filter_var(old('audience.include_guardians', false), FILTER_VALIDATE_BOOLEAN);
@endphp

<div class="w-full">
    <form action="{{ route('notices.store') }}" method="POST" enctype="multipart/form-data" class="grid w-full gap-6 xl:grid-cols-[minmax(0,1fr)_22rem]">
        @csrf

        <div class="flex min-w-0 flex-col gap-6">
            <april:card>
                <slot:title class="flex items-center gap-2">
                    <span>Write your notice</span>
                    <x-help-tooltip label="Notice writing help">Keep the title short and make the first sentence tell people what they need to know.</x-help-tooltip>
                </slot:title>
                <slot:description>Share an update with your school community.</slot:description>
                <slot:content class="space-y-6">
                    <x-display-validation-errors />

                    <div class="flex flex-col gap-2">
                        <april:label for="title">Notice title</april:label>
                        <april:input id="title" name="title" value="{{ old('title') }}" required maxlength="255" placeholder="e.g. Parent meeting next Thursday" />
                        @error('title')
                            <p class="text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-2">
                            <april:label for="content">Message</april:label>
                            <span class="text-xs text-muted-foreground">Use the toolbar to format your message.</span>
                        </div>
                        <april:editor
                            id="content"
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
                        @error('content')
                            <p class="text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>
                </slot:content>
            </april:card>

            <april:card>
                <slot:title>Attachment</slot:title>
                <slot:description>Add a document or image when the message needs supporting information.</slot:description>
                <slot:content>
                    <div class="flex flex-col gap-2">
                        <april:label for="attachment">File <span class="font-normal text-muted-foreground">(optional)</span></april:label>
                        <input id="attachment" type="file" name="attachment" accept=".gif,.jpg,.jpeg,.png,.doc,.docx,.pdf"
                            class="flex min-h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm text-foreground file:mr-4 file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                        <p class="text-xs text-muted-foreground">PDF, Word, GIF, JPG, or PNG up to 10 MB.</p>
                        @error('attachment')
                            <p class="text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>
                </slot:content>
            </april:card>
        </div>

        <aside class="flex min-w-0 flex-col gap-6">
            <april:card>
                <slot:title>Publishing dates</slot:title>
                <slot:description>Choose when people can see this notice.</slot:description>
                <slot:content class="space-y-5">
                    <div class="flex flex-col gap-2">
                        <april:label for="start_date">Starts on</april:label>
                        <april:input id="start_date" name="start_date" type="date" value="{{ old('start_date', now()->toDateString()) }}" required />
                        @error('start_date')
                            <p class="text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <april:label for="stop_date">Ends on</april:label>
                        <april:input id="stop_date" name="stop_date" type="date" value="{{ old('stop_date') }}" />
                        <p class="text-xs text-muted-foreground">The notice stops showing after this date.</p>
                        @error('stop_date')
                            <p class="text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>
                </slot:content>
            </april:card>

            <april:card>
                <slot:title>Audience</slot:title>
                <slot:description>Choose the broadest audience that fits. You can narrow it to classes or exact sections.</slot:description>
                <slot:content class="space-y-5">
                    <div x-data="{ audienceScope: @js($audienceScope) }" class="space-y-5">
                        <div class="grid gap-2" role="radiogroup" aria-label="Audience scope">
                            @foreach ($audienceScopes as $scope)
                                <label
                                    class="flex cursor-pointer items-start gap-3 rounded-md border p-3 transition-colors hover:bg-muted/40"
                                    :class="{ 'border-primary bg-primary/5': audienceScope === @js($scope->value) }"
                                >
                                    <input
                                        type="radio"
                                        name="audience[scope]"
                                        value="{{ $scope->value }}"
                                        x-model="audienceScope"
                                        @checked($audienceScope === $scope->value)
                                        class="mt-0.5 size-4 shrink-0 accent-primary"
                                    >
                                    <span>
                                        <span class="block font-medium">{{ $scope->label() }}</span>
                                        <span class="mt-1 block text-xs text-muted-foreground">
                                            @switch($scope->value)
                                                @case('school') Everyone in this school, including staff and active learners. @break
                                                @case('class') One or more classes or level groups, including all their current sections. @break
                                                @case('section') One or more exact sections for the current academic cycle. @break
                                            @endswitch
                                        </span>
                                    </span>
                                </label>
                            @endforeach
                        </div>

                        <div x-cloak x-show="audienceScope === 'school'" class="rounded-md border border-primary/30 bg-primary/5 p-3 text-sm">
                            This notice will be sent to everyone in the school. No class or section selection is needed.
                        </div>

                        <div x-cloak x-show="audienceScope === 'class'" class="flex flex-col gap-2">
                            <april:label for="academic-level-ids">Classes or levels</april:label>
                            @if ($academicLevels->isNotEmpty())
                                <april:select id="academic-level-ids" name="audience[academic_level_ids][]" multiple placeholder="Choose classes or levels">
                                    @foreach ($academicLevels as $academicLevel)
                                        <option value="{{ $academicLevel->id }}" @selected(in_array((string) $academicLevel->id, $selectedAcademicLevelIds, true))>
                                            {{ $academicLevel->is_group ? 'Group' : 'Class' }} · {{ $academicLevel->name }}
                                        </option>
                                    @endforeach
                                </april:select>
                                <p class="text-xs text-muted-foreground">Groups include all classes nested under them.</p>
                            @else
                                <div class="rounded-md border border-dashed p-3 text-sm text-muted-foreground">
                                    No active classes or levels are available.
                                </div>
                            @endif
                            @error('audience.academic_level_ids')
                                <p class="text-sm text-destructive">{{ $message }}</p>
                            @enderror
                            @error('audience.academic_level_ids.*')
                                <p class="text-sm text-destructive">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-cloak x-show="audienceScope === 'section'" class="flex flex-col gap-2">
                            <april:label for="academic-cycle-section-ids">Sections</april:label>
                            @if ($sections->isNotEmpty())
                                <april:select id="academic-cycle-section-ids" name="audience[academic_cycle_section_ids][]" multiple placeholder="Choose sections">
                                    @foreach ($sections as $section)
                                        <option value="{{ $section->id }}" @selected(in_array((string) $section->id, $selectedSectionIds, true))>
                                            {{ $section->academicLevel?->name ?? 'Unassigned '.strtolower(school_term('class_level', 'class')) }} · {{ $section->label ?? $section->name }}
                                        </option>
                                    @endforeach
                                </april:select>
                                <p class="text-xs text-muted-foreground">Choose one or more exact sections.</p>
                            @else
                                <div class="rounded-md border border-dashed p-3 text-sm text-muted-foreground">
                                    No active sections are available.
                                </div>
                            @endif
                            @error('audience.academic_cycle_section_ids')
                                <p class="text-sm text-destructive">{{ $message }}</p>
                            @enderror
                            @error('audience.academic_cycle_section_ids.*')
                                <p class="text-sm text-destructive">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <label class="flex items-start gap-3 rounded-md border p-3 text-sm transition-colors hover:bg-muted/40">
                        <input type="hidden" name="audience[include_guardians]" value="0">
                        <input type="checkbox" name="audience[include_guardians]" value="1" @checked($includeGuardians)
                            class="mt-0.5 size-4 rounded border-input text-primary-foreground accent-primary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                        <span>
                            <span class="font-medium">Include guardians</span>
                            <span class="mt-1 block text-xs text-muted-foreground">Send this notice to the guardians of the targeted learners too.</span>
                        </span>
                    </label>
                </slot:content>
            </april:card>
        </aside>

        <div class="flex flex-col-reverse gap-3 border-t pt-6 sm:flex-row sm:items-center sm:justify-between xl:col-span-2">
            <p class="text-sm text-muted-foreground">You can review and publish the notice after it is created.</p>
            <div class="flex flex-col-reverse gap-3 sm:flex-row">
                <april:button-link href="{{ route('notices.index') }}" variant="outline">Cancel</april:button-link>
                <april:button type="submit">
                    <x-lucide-send class="mr-2 size-4" />
                    Create notice
                </april:button>
            </div>
        </div>
    </form>
</div>

@pushOnce('scripts')
    @aprilEditorScripts
@endPushOnce
