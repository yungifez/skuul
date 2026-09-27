<form wire:submit="save" class="grid w-full gap-8 xl:grid-cols-[minmax(0,1fr)_22rem]" aria-label="Write a notice">
    @php
        $controlClasses = 'mt-1 h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
    @endphp

    <div class="flex min-w-0 flex-col gap-4">
        <div>
            <label for="title" class="text-sm text-muted-foreground">Title</label>
            <input id="title" wire:model="title" required maxlength="255" placeholder="Parent meeting next Thursday" class="{{ $controlClasses }}" {{ field_error_bindings('title') }}>
            <x-field-error name="title" class="mt-1" />
        </div>

        <div>
            <label for="content" class="text-sm text-muted-foreground">Message</label>
            <div wire:ignore class="mt-1">
                <april:editor
                    id="content"
                    wire:model="content"
                    placeholder="Say first what people need to know."
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
            <x-field-error name="content" class="mt-1" />
        </div>

        <div>
            <label for="attachment" class="text-sm text-muted-foreground">Attachment (optional)</label>
            <input id="attachment" type="file" wire:model="attachment" accept=".gif,.jpg,.jpeg,.png,.doc,.docx,.pdf"
                class="{{ $controlClasses }} py-2 file:mr-3 file:border-0 file:bg-transparent file:text-sm file:font-medium" {{ field_error_bindings('attachment') }}>
            <p class="mt-1 text-xs text-muted-foreground">PDF, Word, GIF, JPG or PNG, up to 10 MB.</p>
            <x-field-error name="attachment" class="mt-1" />
        </div>
    </div>

    <aside class="flex min-w-0 flex-col gap-6">
        <fieldset class="grid grid-cols-2 gap-3">
            <legend class="mb-1 text-sm font-medium">When it shows</legend>
            <div>
                <label for="startDate" class="text-sm text-muted-foreground">From</label>
                <input id="startDate" type="date" wire:model="startDate" required class="{{ $controlClasses }}" {{ field_error_bindings('startDate') }}>
                <x-field-error name="startDate" class="mt-1" />
            </div>
            <div>
                <label for="stopDate" class="text-sm text-muted-foreground">Until</label>
                <input id="stopDate" type="date" wire:model="stopDate" required class="{{ $controlClasses }}" {{ field_error_bindings('stopDate') }}>
                <x-field-error name="stopDate" class="mt-1" />
            </div>
        </fieldset>

        <fieldset class="flex flex-col gap-2">
            <legend class="mb-1 text-sm font-medium">Who reads it</legend>
            @foreach ($audienceScopes as $scope)
                <label class="flex min-h-11 cursor-pointer select-none items-start gap-3 rounded-md border p-3 text-sm has-[:checked]:border-primary">
                    <input type="radio" wire:model.live="audienceScope" value="{{ $scope->value }}" class="mt-0.5 size-5 shrink-0 accent-primary">
                    <span>
                        <span class="block font-medium">{{ $scope->label() }}</span>
                        <span class="mt-1 block text-xs text-muted-foreground">
                            @switch($scope->value)
                                @case('school') Everyone in this school: staff and learners who attend. @break
                                @case('class') Every current section of the classes or groups you choose. @break
                                @case('section') Only the sections you choose. @break
                            @endswitch
                        </span>
                    </span>
                </label>
            @endforeach
            <x-field-error name="audienceScope" />

            @if ($audienceScope === 'class')
                <div class="mt-2">
                    @if ($academicLevels->isNotEmpty())
                        <div class="max-h-64 divide-y overflow-y-auto border-y" role="group" aria-label="Classes or levels">
                            @foreach ($academicLevels as $academicLevel)
                                <label wire:key="level-{{ $academicLevel->id }}" class="flex min-h-11 cursor-pointer select-none items-center gap-3 text-sm">
                                    <input type="checkbox" wire:model="academicLevelIds" value="{{ $academicLevel->id }}" class="size-5 rounded border-input">
                                    {{ $academicLevel->name }}
                                    @if ($academicLevel->is_group)
                                        <span class="text-xs text-muted-foreground">group, with every class under it</span>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-muted-foreground">No active classes yet.</p>
                    @endif
                    <x-field-error name="academicLevelIds" class="mt-1" />
                </div>
            @elseif ($audienceScope === 'section')
                <div class="mt-2">
                    @if ($sections->isNotEmpty())
                        <div class="max-h-64 divide-y overflow-y-auto border-y" role="group" aria-label="Sections">
                            @foreach ($sections as $section)
                                <label wire:key="section-{{ $section->id }}" class="flex min-h-11 cursor-pointer select-none items-center gap-3 text-sm">
                                    <input type="checkbox" wire:model="sectionIds" value="{{ $section->id }}" class="size-5 rounded border-input">
                                    {{ $section->academicLevel?->name ?? '—' }} · {{ $section->label ?? $section->name }}
                                </label>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-muted-foreground">No active sections yet.</p>
                    @endif
                    <x-field-error name="sectionIds" class="mt-1" />
                </div>
            @endif

            <label class="mt-2 flex min-h-11 cursor-pointer select-none items-start gap-3 text-sm">
                <input type="checkbox" wire:model="includeGuardians" class="mt-0.5 size-5 rounded border-input">
                <span>
                    <span class="block font-medium">Guardians too</span>
                    <span class="mt-1 block text-xs text-muted-foreground">The guardians of the learners it reaches get it as well.</span>
                </span>
            </label>
        </fieldset>
    </aside>

    <div class="flex flex-col-reverse gap-3 border-t pt-6 sm:flex-row sm:items-center sm:justify-between xl:col-span-2">
        <p class="text-sm text-muted-foreground">It is saved as a draft. Nobody sees it until you publish it.</p>
        <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save,attachment">Save the draft</april:button>
    </div>
</form>

@pushOnce('scripts')
    @aprilEditorScripts
@endPushOnce
