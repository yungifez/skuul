<div class="flex flex-col gap-10">
<section aria-labelledby="profile-heading" class="flex flex-col gap-6">
    <div class="flex items-center gap-4">
        <img src="{{ $user->profile_photo_url }}" alt="" class="size-16 shrink-0 rounded-full border object-cover" />
        <div class="min-w-0">
            <h2 id="profile-heading" class="truncate text-xl font-semibold tracking-tight">{{ $user->name }}</h2>
            <p class="flex flex-wrap gap-x-2 text-sm text-muted-foreground">
                @foreach ($user->roles as $role)
                    <span>{{ str($role->name)->headline() }}</span>
                @endforeach
                @if ($user->can(\App\Enums\PlatformPermission::ManagePlatform))
                    <span>Platform administrator</span>
                @endif
            </p>
        </div>
    </div>

    <dl class="grid grid-cols-1 gap-x-6 gap-y-4 border-y py-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ([
            'Email address' => $user->email,
            'Phone number' => $user->phone,
            'Gender' => $user->gender,
            'Date of birth' => $user->birthday === null ? null : \Illuminate\Support\Carbon::parse($user->birthday)->format('j M Y'),
            'Nationality' => $user->nationality,
            'Address' => collect([$user->address, $user->address_line_2, $user->city, $user->state, $user->postal_code, $user->country])->filter()->implode(', '),
        ] as $label => $value)
            <div class="min-w-0">
                <dt class="text-sm text-muted-foreground">{{ $label }}</dt>
                <dd @class(['break-words', 'text-muted-foreground' => blank($value)])>{{ filled($value) ? $value : '—' }}</dd>
            </div>
        @endforeach
    </dl>
</section>

@can('manageAccountAccess', $user)
    <livewire:manage-account-password :user="$user" />
@endcan
</div>
