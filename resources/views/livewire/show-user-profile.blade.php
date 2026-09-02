<april:card>
    <slot:content>
        <div class="grid gap-6 lg:grid-cols-[auto_1fr] lg:items-start">
            <div class="flex flex-col items-center gap-3 lg:items-start">
                <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="size-28 rounded-full border object-cover shadow-sm" />
                <div class="text-center lg:text-left">
                    <h2 class="text-2xl font-semibold tracking-tight">{{ $user->name }}</h2>
                    <div class="mt-2 flex flex-wrap justify-center gap-2 lg:justify-start">
                        @foreach ($user->roles as $role)
                            <april:badge variant="secondary">{{ str($role->name)->headline() }}</april:badge>
                        @endforeach
                        @if ($user->can(\App\Enums\PlatformPermission::ManagePlatform))
                            <april:badge>Platform administrator</april:badge>
                        @endif
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <h3 class="font-semibold">Personal information</h3>
                    <p class="text-sm text-muted-foreground">Contact and identity details for this person.</p>
                </div>

                <dl class="grid gap-x-6 gap-y-4 sm:grid-cols-2">
                    @foreach ([
                        'Email address' => $user->email,
                        'Phone number' => $user->phone,
                        'Gender' => $user->gender,
                        'Date of birth' => $user->birthday,
                        'Nationality' => $user->nationality,
                        'Country' => $user->country,
                        'State / Province' => $user->state,
                        'City' => $user->city,
                        'Address line 1' => $user->address,
                        'Address line 2' => $user->address_line_2,
                        'Postal / ZIP code' => $user->postal_code,
                    ] as $label => $value)
                        <div class="border-b pb-3">
                            <dt class="text-xs font-medium uppercase text-muted-foreground">{{ $label }}</dt>
                            <dd class="mt-1 text-sm">{{ $value ?: 'Not recorded' }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>
    </slot:content>
</april:card>
