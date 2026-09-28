<div class="flex flex-col gap-8">
    @php
        $controlClasses = 'mt-1 h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
    @endphp

    <p class="text-sm text-muted-foreground">
        An address that names a campus opens on that campus, so nobody picks one from a list. Membership still decides what a person sees, and an address does nothing until it is proved.
    </p>

    <section class="flex flex-col gap-2" aria-labelledby="domains-heading">
        <h2 id="domains-heading" class="text-base font-semibold">Addresses</h2>
        @if ($domains->isEmpty())
            <p class="text-sm text-muted-foreground">No address yet. Everybody signs in at the shared address and picks a campus.</p>
        @else
            <ul class="divide-y border-y">
                @foreach ($domains as $domain)
                    <li wire:key="domain-{{ $domain->id }}" class="flex flex-col gap-3 py-3">
                        <div class="flex items-center gap-3">
                            <div class="min-w-0 flex-1">
                                <p class="break-all text-sm font-medium">{{ $domain->host }}{{ $domain->is_primary ? ' · main' : '' }}</p>
                                <p class="text-xs text-muted-foreground">
                                    Opens {{ $domain->school !== null && $domain->school->organization_id === $organization->id ? $domain->school->name : 'the organization, no campus' }} ·
                                    {{ $domain->isVerified() ? 'proved '.$domain->verified_at?->format('j M Y') : 'not proved yet' }}
                                </p>
                            </div>
                            @unless ($domain->isVerified())
                                <april:button type="button" class="h-11 select-none" wire:click="verify({{ $domain->id }})" wire:loading.attr="disabled" wire:target="verify({{ $domain->id }})">Prove it</april:button>
                            @endunless
                            <april:dropdown-menu>
                                <slot:trigger>
                                    <april:button type="button" variant="ghost" size="icon" class="size-11 select-none" aria-label="More for {{ $domain->host }}">
                                        <x-lucide-ellipsis class="size-4" />
                                    </april:button>
                                </slot:trigger>
                                <slot:content align="end">
                                    <april:dropdown-menu-item wire:click="giveUp({{ $domain->id }})" wire:confirm="Give up {{ $domain->host }}? Nobody reaches the school at this address afterwards."><x-lucide-trash-2 class="mr-2 size-4" />Give it up</april:dropdown-menu-item>
                                </slot:content>
                            </april:dropdown-menu>
                        </div>
                        @unless ($domain->isVerified())
                            <dl class="grid gap-1 text-sm sm:grid-cols-[4rem_1fr]">
                                <dt class="text-muted-foreground">Type</dt>
                                <dd>TXT</dd>
                                <dt class="text-muted-foreground">Name</dt>
                                <dd class="select-all break-all font-mono text-xs">{{ $domain->verificationRecord() }}</dd>
                                <dt class="text-muted-foreground">Value</dt>
                                <dd class="select-all break-all font-mono text-xs">{{ $domain->verification_token }}</dd>
                            </dl>
                        @endunless
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    <form wire:submit="claim" class="flex flex-col gap-3" aria-label="Claim an address">
        <h2 class="text-base font-semibold">Claim an address</h2>
        <div class="grid gap-3 sm:grid-cols-2">
            <div>
                <label for="domain-host" class="text-sm text-muted-foreground">Address</label>
                <input id="domain-host" wire:model="host" required maxlength="253" autocomplete="off" inputmode="url" placeholder="lagos.example.school" class="{{ $controlClasses }}" {{ field_error_bindings('host') }}>
                <x-field-error name="host" class="mt-1" />
            </div>
            <div>
                <label for="domain-school" class="text-sm text-muted-foreground">Opens</label>
                <select id="domain-school" wire:model="schoolId" class="{{ $controlClasses }}" {{ field_error_bindings('schoolId') }}>
                    <option value="">The organization, no campus</option>
                    @foreach ($campuses as $campus)
                        <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                    @endforeach
                </select>
                <x-field-error name="schoolId" class="mt-1" />
            </div>
        </div>
        <label class="flex min-h-11 select-none items-center gap-3 text-sm">
            <input type="checkbox" wire:model="isPrimary" class="size-4 rounded border-input">
            The main address
        </label>
        <div class="flex justify-end">
            <april:button type="submit" variant="outline" class="h-11 select-none" wire:loading.attr="disabled" wire:target="claim">Claim it</april:button>
        </div>
    </form>
</div>
