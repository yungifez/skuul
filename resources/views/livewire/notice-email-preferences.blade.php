<div class="flex flex-col gap-8">
    @php
        $switchClasses = 'relative h-6 w-10 shrink-0 cursor-pointer appearance-none rounded-full transition-colors before:absolute before:left-0.5 before:top-0.5 before:size-5 before:rounded-full before:bg-background before:shadow before:transition-transform focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2';
    @endphp

    <section class="flex flex-col gap-2" aria-labelledby="notice-email-heading">
        <h2 id="notice-email-heading" class="text-base font-semibold">Email me a copy of notices</h2>
        <ul class="divide-y border-y">
            @foreach ($schools as $school)
                @php($isOn = $emailEnabled[$school->id] ?? true)
                <li wire:key="notice-email-{{ $school->id }}">
                    <label for="notice-email-{{ $school->id }}" class="flex min-h-11 cursor-pointer select-none items-center justify-between gap-4 py-3">
                        <span class="min-w-0">
                            <span @class(['block font-medium', 'text-muted-foreground' => !$isOn])>{{ $school->name }}</span>
                            <span class="block text-sm text-muted-foreground">{{ $isOn ? 'Emails arrive, and notices stay here too' : 'Notices only here' }}</span>
                        </span>
                        <input type="checkbox" role="switch" id="notice-email-{{ $school->id }}" wire:model.live="emailEnabled.{{ $school->id }}" @class([$switchClasses, 'bg-foreground before:translate-x-4' => $isOn, 'bg-input' => !$isOn])>
                    </label>
                </li>
            @endforeach
        </ul>
    </section>

    <p class="text-sm text-muted-foreground">
        Account and safety messages, such as a password reset, are always sent. You cannot turn those off here.
    </p>
</div>
