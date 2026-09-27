<section class="flex flex-col gap-4" aria-labelledby="assessments-heading">
    @php
        $controlClasses = 'h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
        $formatNumber = fn (?float $value): string => $value === null ? '—' : rtrim(rtrim(number_format($value, 3, '.', ''), '0'), '.');
        $isEditing = $editingItemId !== null;
    @endphp

    <div class="flex items-center justify-between gap-3">
        <h2 id="assessments-heading" class="text-base font-semibold">Assessments</h2>
        @if ($items->isNotEmpty() || $categories->isNotEmpty())
            <april:dropdown-menu>
                <slot:trigger>
                    <april:button type="button" variant="ghost" size="icon" class="size-11 select-none" aria-label="More for the assessments">
                        <x-lucide-ellipsis class="size-4" />
                    </april:button>
                </slot:trigger>
                <slot:content align="end">
                    <april:dropdown-menu-item wire:click="$set('isAddingCategory', true)"><x-lucide-folder-plus class="mr-2 size-4" />Add a category</april:dropdown-menu-item>
                    <april:dropdown-menu-item wire:click="$set('isSavingTemplate', true)"><x-lucide-copy class="mr-2 size-4" />Save as a template</april:dropdown-menu-item>
                </slot:content>
            </april:dropdown-menu>
        @endif
    </div>

    @if ($templates->isNotEmpty())
        <form wire:submit="applyTemplate" class="flex flex-col gap-2 border-y py-4 sm:flex-row sm:items-start" aria-label="Start from a template">
            <div class="flex-1">
                <label for="setup-template" class="sr-only">Template</label>
                <select id="setup-template" wire:model="templateId" class="{{ $controlClasses }}" {{ field_error_bindings('templateId') }}>
                    <option value="">Start from a school template</option>
                    @foreach ($templates as $template)
                        <option value="{{ $template->id }}">{{ $template->name }} · {{ $template->categories_count }} {{ Str::plural('category', $template->categories_count) }}, {{ $template->items_count }} {{ Str::plural('assessment', $template->items_count) }}</option>
                    @endforeach
                </select>
                <x-field-error name="templateId" class="mt-1" />
            </div>
            <april:button type="submit" variant="outline" class="h-11 select-none" wire:loading.attr="disabled" wire:target="applyTemplate">Use template</april:button>
        </form>
    @endif

    @if ($categories->isNotEmpty())
        <ul class="flex flex-wrap gap-2" aria-label="Categories">
            @foreach ($categories as $category)
                <li wire:key="category-{{ $category->id }}" class="rounded-md border px-3 py-2 text-sm">
                    <span class="font-medium">{{ $category->name }}</span>
                    <span class="text-muted-foreground">· {{ $category->aggregation->label() }} · {{ $formatNumber($category->weight) }}×</span>
                </li>
            @endforeach
        </ul>
    @endif

    @if ($isAddingCategory)
        <form wire:submit="addCategory" class="grid gap-3 border-y py-4 sm:grid-cols-[minmax(0,2fr)_minmax(0,1.5fr)_6rem]" aria-label="Add a category">
            <div>
                <label for="category-name" class="sr-only">Category name</label>
                <input id="category-name" wire:model="categoryName" maxlength="100" required placeholder="Category, e.g. classwork" class="{{ $controlClasses }}" {{ field_error_bindings('categoryName') }}>
                <x-field-error name="categoryName" class="mt-1" />
            </div>
            <div>
                <label for="category-aggregation" class="sr-only">Calculation</label>
                <select id="category-aggregation" wire:model="categoryAggregation" class="{{ $controlClasses }}" {{ field_error_bindings('categoryAggregation') }}>
                    @foreach ($aggregations as $aggregation)
                        <option value="{{ $aggregation->value }}">{{ $aggregation->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="category-weight" class="sr-only">Weight</label>
                <input id="category-weight" type="number" wire:model="categoryWeight" min="0.001" step="0.001" required inputmode="decimal" aria-describedby="category-weight-hint" class="{{ $controlClasses }} text-right tabular-nums" {{ field_error_bindings('categoryWeight') }}>
                <x-field-error name="categoryWeight" class="mt-1" />
            </div>
            <p id="category-weight-hint" class="text-xs text-muted-foreground sm:col-span-3">The weight says how much this category counts beside the others.</p>
            <div class="flex justify-end gap-2 sm:col-span-3">
                <april:button type="button" variant="ghost" class="h-11 select-none" wire:click="$set('isAddingCategory', false)">Cancel</april:button>
                <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="addCategory">Add category</april:button>
            </div>
        </form>
    @endif

    @if ($isSavingTemplate)
        <form wire:submit="saveTemplate" class="flex flex-col gap-3 border-y py-4" aria-label="Save as a template">
            <p class="text-sm text-muted-foreground">Copies the categories and assessments only. No marks, due dates or results.</p>
            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label for="template-name" class="sr-only">Template name</label>
                    <input id="template-name" wire:model="templateName" maxlength="150" required placeholder="Template name" class="{{ $controlClasses }}" {{ field_error_bindings('templateName') }}>
                    <x-field-error name="templateName" class="mt-1" />
                </div>
                <div>
                    <label for="template-description" class="sr-only">When to use it (optional)</label>
                    <input id="template-description" wire:model="templateDescription" maxlength="5000" placeholder="When to use it (optional)" class="{{ $controlClasses }}" {{ field_error_bindings('templateDescription') }}>
                    <x-field-error name="templateDescription" class="mt-1" />
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <april:button type="button" variant="ghost" class="h-11 select-none" wire:click="$set('isSavingTemplate', false)">Cancel</april:button>
                <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="saveTemplate">Save template</april:button>
            </div>
        </form>
    @endif

    @if ($items->isNotEmpty())
        <ul class="divide-y border-y">
            @foreach ($items as $gradeItem)
                <li wire:key="setup-item-{{ $gradeItem->id }}" class="flex items-center gap-3 py-2">
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium">{{ $gradeItem->name }}</p>
                        <p class="text-xs text-muted-foreground">
                            {{ $gradeItem->gradingScale?->name ?? ($gradeItem->max_points === null ? $gradeItem->type->label() : 'Out of '.$formatNumber($gradeItem->max_points)) }}
                            · {{ $formatNumber($gradeItem->weight) }}×
                            · {{ $gradeItem->category?->name ?? 'No category' }}
                            @if ($gradeItem->due_on !== null)
                                · due {{ $gradeItem->due_on->format('j M') }}
                            @endif
                        </p>
                    </div>
                    <april:dropdown-menu>
                        <slot:trigger>
                            <april:button type="button" variant="ghost" size="icon" class="size-11 select-none" aria-label="More for {{ $gradeItem->name }}">
                                <x-lucide-ellipsis class="size-4" />
                            </april:button>
                        </slot:trigger>
                        <slot:content align="end">
                            <april:dropdown-menu-item wire:click="editItem({{ $gradeItem->id }})"><x-lucide-pencil class="mr-2 size-4" />Change</april:dropdown-menu-item>
                            @if ($gradeItem->entries_count === 0)
                                <april:dropdown-menu-item class="text-destructive" wire:click="removeItem({{ $gradeItem->id }})" wire:confirm="Remove {{ $gradeItem->name }}?">
                                    <x-lucide-trash-2 class="mr-2 size-4" />Remove
                                </april:dropdown-menu-item>
                            @endif
                        </slot:content>
                    </april:dropdown-menu>
                </li>
            @endforeach
        </ul>
    @endif

    @if ($isAddingItem || $isEditing)
        @php($chosenType = \App\Enums\GradeItemType::tryFrom($itemType))
        <form wire:submit="saveItem" class="flex flex-col gap-3" aria-label="{{ $isEditing ? 'Change '.$itemName : 'Add an assessment' }}">
            <div>
                <label for="item-name" class="sr-only">Assessment name</label>
                <input id="item-name" wire:model="itemName" maxlength="150" required placeholder="Assessment, e.g. term project" class="{{ $controlClasses }}" {{ field_error_bindings('itemName') }}>
                <x-field-error name="itemName" class="mt-1" />
            </div>
            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label for="item-type" class="text-sm text-muted-foreground">Marked as</label>
                    <select id="item-type" wire:model.live="itemType" @disabled($isEditing) class="{{ $controlClasses }} mt-1 disabled:opacity-60" {{ field_error_bindings('itemType') }}>
                        @foreach ($types as $type)
                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                        @endforeach
                    </select>
                </div>
                @if ($chosenType === \App\Enums\GradeItemType::Numeric)
                    <div>
                        <label for="item-max" class="text-sm text-muted-foreground">Out of</label>
                        <input id="item-max" type="number" wire:model="itemMaxPoints" min="0.01" step="0.01" required inputmode="decimal" placeholder="e.g. 20" class="{{ $controlClasses }} mt-1 text-right tabular-nums" {{ field_error_bindings('itemMaxPoints') }}>
                    </div>
                @elseif ($chosenType === \App\Enums\GradeItemType::Scale)
                    <div>
                        <label for="item-scale" class="text-sm text-muted-foreground">Grading scale</label>
                        <select id="item-scale" wire:model="itemScaleId" @disabled($isEditing) class="{{ $controlClasses }} mt-1 disabled:opacity-60" {{ field_error_bindings('itemScaleId') }}>
                            <option value="">Choose a scale</option>
                            @foreach ($scales as $scale)
                                <option value="{{ $scale->id }}">{{ $scale->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div>
                    <label for="item-weight" class="text-sm text-muted-foreground">Weight</label>
                    <input id="item-weight" type="number" wire:model="itemWeight" min="0.001" step="0.001" required inputmode="decimal" class="{{ $controlClasses }} mt-1 text-right tabular-nums" {{ field_error_bindings('itemWeight') }}>
                </div>
                <div>
                    <label for="item-category" class="text-sm text-muted-foreground">Category</label>
                    <select id="item-category" wire:model="itemCategoryId" class="{{ $controlClasses }} mt-1" {{ field_error_bindings('itemCategoryId') }}>
                        <option value="">No category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="item-due" class="text-sm text-muted-foreground">Due (optional)</label>
                    <input id="item-due" type="date" wire:model="itemDueOn" class="{{ $controlClasses }} mt-1" {{ field_error_bindings('itemDueOn') }}>
                </div>
            </div>
            <x-field-error name="itemType" />
            <x-field-error name="itemMaxPoints" />
            <x-field-error name="itemScaleId" />
            <x-field-error name="itemWeight" />
            <x-field-error name="itemCategoryId" />
            <x-field-error name="itemDueOn" />
            <div class="flex justify-end gap-2">
                @if ($items->isNotEmpty() || $isEditing)
                    <april:button type="button" variant="ghost" class="h-11 select-none" wire:click="cancelItem">Cancel</april:button>
                @endif
                <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="saveItem">{{ $isEditing ? 'Save changes' : 'Add assessment' }}</april:button>
            </div>
        </form>
    @else
        <div>
            <april:button type="button" variant="outline" class="h-11 select-none" wire:click="startAddingItem">
                <x-lucide-plus class="mr-2 size-4" aria-hidden="true" />Add an assessment
            </april:button>
        </div>
    @endif
</section>
