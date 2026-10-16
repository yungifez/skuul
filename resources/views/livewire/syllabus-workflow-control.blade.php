<div class="flex flex-col gap-3">
    <div class="flex flex-wrap items-center gap-2">
        @if ($canSubmit && !$canPublish)
            <april:button type="button" wire:click="submit" wire:loading.attr="disabled">Send for review</april:button>
        @endif
        @if ($canPublish)
            <april:button type="button" wire:click="publish" wire:confirm="Publish {{ $syllabus->name }}? Students will see it{{ $syllabus->revision_of_id ? ' in place of revision '.($syllabus->revision - 1) : '' }}." wire:loading.attr="disabled">
                {{ $syllabus->status === \App\Enums\SyllabusStatus::Submitted ? 'Approve and publish' : 'Publish' }}
            </april:button>
        @endif
        @if ($canWithdraw)
            <april:button type="button" variant="outline" wire:click="withdraw" wire:loading.attr="disabled">Take back to edit</april:button>
        @endif
    </div>

    @if ($canSendBack)
        <form wire:submit="sendBack" class="flex flex-col gap-2 md:w-1/2">
            <label for="review-note" class="text-sm font-medium">Send back for changes</label>
            <textarea id="review-note" wire:model="reviewNote" rows="2" placeholder="What needs to change" {{ field_error_bindings('reviewNote') }}
                class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"></textarea>
            <x-field-error name="reviewNote" />
            <div><april:button type="submit" variant="outline">Send back</april:button></div>
        </form>
    @endif

    @if ($canRevise)
        <form wire:submit="revise" class="flex flex-col gap-2 border-t pt-4 md:w-1/2">
            <label for="change-note" class="text-sm font-medium">Revise this syllabus</label>
            <p class="text-sm text-muted-foreground">Students keep seeing this revision until the new one is published.</p>
            <textarea id="change-note" wire:model="changeNote" rows="2" placeholder="What will change and why" {{ field_error_bindings('changeNote') }}
                class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"></textarea>
            <x-field-error name="changeNote" />
            <div><april:button type="submit" variant="outline">Create revised draft</april:button></div>
        </form>
    @endif
</div>
