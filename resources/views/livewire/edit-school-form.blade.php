<div class="mx-auto max-w-6xl space-y-6">
    <div class="flex flex-col gap-4 rounded-xl border bg-muted/30 p-5 sm:flex-row sm:items-center sm:justify-between md:p-6">
        <div class="min-w-0">
            <p class="text-sm font-medium text-primary-foreground">School profile</p>
            <h2 class="mt-1 truncate text-2xl font-semibold tracking-tight">{{ $school->name }}</h2>
            <p class="mt-1 max-w-2xl text-sm text-muted-foreground">Keep the details families, staff, and printed records use to identify and contact this school.</p>
        </div>
        <div class="flex size-16 shrink-0 items-center justify-center overflow-hidden rounded-xl border bg-background text-xl font-semibold text-primary-foreground shadow-sm">
            @if ($school->logo_path)
                <img src="{{ $school->logo_url }}" alt="" class="size-full object-cover" />
            @else
                {{ str($school->name)->substr(0, 2)->upper() }}
            @endif
        </div>
    </div>

    <x-display-validation-errors />

    <form action="{{ route('schools.update', $school->id) }}" method="POST" enctype="multipart/form-data" class="grid gap-6 lg:grid-cols-[minmax(0,1.25fr)_minmax(18rem,0.75fr)]">
        @csrf
        @method('PUT')
        @if ($setup)
            <input type="hidden" name="setup" value="1">
        @endif

        <div class="space-y-6">
            <april:card>
                <slot:title>Basic details</slot:title>
                <slot:description>Use the name and contact details your school wants people to see.</slot:description>
                <slot:content class="grid gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <april:input-group id="name" name="name" placeholder="Enter name of school" label="School name *" value="{{ $school->name }}" autocomplete="organization" required />
                    </div>
                    <april:input-group id="initials" name="initials" placeholder="Enter school initials" label="Short name" value="{{ $school->initials }}" />
                    <april:input-group id="phone" name="phone" type="tel" placeholder="Enter school phone number" label="Phone number" value="{{ $school->phone }}" autocomplete="tel" />
                    <div class="sm:col-span-2">
                        <april:input-group id="email" name="email" type="email" placeholder="Enter school email" label="School email" value="{{ $school->email }}" autocomplete="email" />
                    </div>
                </slot:content>
            </april:card>

            <april:card>
                <slot:title>School address</slot:title>
                <slot:description>Use the address that should appear on school records and correspondence.</slot:description>
                <slot:content>
                    <x-school-address-fields
                        :countries="$countries"
                        :address="$school->address"
                        :city="$school->city"
                        :country="$school->country"
                        :state="$school->state"
                        :postal-code="$school->postal_code"
                    />
                </slot:content>
            </april:card>
        </div>

        <div class="space-y-6">
            <april:card>
                <slot:title>School logo</slot:title>
                <slot:description>Use a square image for the clearest result in the sidebar and printed records.</slot:description>
                <slot:content class="space-y-5">
                    <div class="flex items-center gap-4 rounded-lg border bg-muted/30 p-4">
                        <div class="flex size-16 shrink-0 items-center justify-center overflow-hidden rounded-lg border bg-background text-xl font-semibold text-primary-foreground">
                            @if ($school->logo_path)
                                <img src="{{ $school->logo_url }}" alt="Current school logo" class="size-full object-cover" />
                            @else
                                {{ str($school->name)->substr(0, 2)->upper() }}
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="font-medium">Current logo</p>
                            <p class="mt-1 text-sm text-muted-foreground">{{ $school->logo_path ? 'Your uploaded logo is in use.' : 'The school initials are shown until a logo is uploaded.' }}</p>
                        </div>
                    </div>
                    <april:input-group name="logo" id="logo" type="file" label="Replace logo" accept="image/*" />
                    <p class="text-xs text-muted-foreground">PNG, JPG, or another image up to 5 MB.</p>
                </slot:content>
            </april:card>

            <april:card>
                <slot:title>Required information</slot:title>
                <slot:content>
                    <p class="text-sm text-muted-foreground">Fields marked with an asterisk are required. Saving changes updates the school everywhere it appears in Skuul.</p>
                </slot:content>
            </april:card>
        </div>

        <div class="flex flex-col-reverse gap-3 border-t pt-6 sm:flex-row sm:items-center sm:justify-between lg:col-span-2">
            <april:button-link href="{{ route('schools.show', $school) }}" variant="ghost">Cancel</april:button-link>
            <april:button type="submit" class="sm:min-w-36">
                <x-lucide-save class="mr-2 size-4" />
                Save changes
            </april:button>
        </div>
    </form>
</div>
