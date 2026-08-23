@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('organizations.index'), 'text' => 'Organizations'],
    ['href' => route('organizations.show', $organization), 'text' => $organization->name],
    ['href' => route('organizations.domains.index', $organization), 'text' => 'Web addresses', 'active'],
]])

@section('title', __('Web addresses'))

@section('page_heading', __('Web addresses'))

@section('content')
<div class="mx-auto flex w-full max-w-4xl flex-col gap-6">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-foreground md:text-3xl">Where this organization answers</h2>
        <p class="mt-1 text-sm text-muted-foreground">
            An address that names a campus opens on that campus, so staff and families do not choose one from a list.
            The address only says which campus was meant. Who may see the records is still decided by membership, and an
            address is ignored until the organization has proved it owns it.
        </p>
    </div>

    <x-display-validation-errors />

    <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
        <div class="flex flex-col gap-1.5 border-b p-6">
            <h3 class="text-lg font-semibold leading-none tracking-tight">Claim an address</h3>
            <p class="text-sm text-muted-foreground">Add the address here first, then prove it with a record only its owner can write.</p>
        </div>

        <form action="{{ route('organizations.domains.store', $organization) }}" method="POST" class="flex flex-col gap-4 p-6">
            @csrf

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="flex flex-col gap-2">
                    <label for="domain-host" class="text-sm font-medium leading-none">Address</label>
                    <input id="domain-host" name="host" required maxlength="253" value="{{ old('host') }}" autocomplete="off"
                        placeholder="lagos.example.school"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                </div>

                <div class="flex flex-col gap-2">
                    <label for="domain-school" class="text-sm font-medium leading-none">Opens</label>
                    <select id="domain-school" name="school_id"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                        <option value="">The organization, no campus</option>
                        @foreach ($campuses as $campus)
                            <option value="{{ $campus->id }}" @selected(old('school_id') == $campus->id)>{{ $campus->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <label class="flex items-start gap-3 text-sm">
                <input type="checkbox" name="is_primary" value="1" class="mt-0.5 size-4" @checked(old('is_primary'))>
                <span>
                    <span class="font-medium">This is the main address</span>
                    <span class="block text-xs text-muted-foreground">Use it in links the application sends out.</span>
                </span>
            </label>

            <div>
                <april:button type="submit">
                    <x-lucide-globe class="mr-2 size-4" />
                    Claim it
                </april:button>
            </div>
        </form>
    </div>

    <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
        <div class="flex flex-col gap-1.5 border-b p-6">
            <h3 class="text-lg font-semibold leading-none tracking-tight">Claimed addresses</h3>
        </div>

        @if ($domains->isEmpty())
            <div class="flex flex-col items-center gap-3 p-10 text-center">
                <span class="flex size-12 items-center justify-center rounded-full bg-muted text-muted-foreground">
                    <x-lucide-globe class="size-6" />
                </span>
                <p class="text-sm font-medium">No address is claimed yet.</p>
                <p class="max-w-sm text-sm text-muted-foreground">
                    Everybody signs in at the shared address and picks a campus.
                </p>
            </div>
        @else
            <div class="flex flex-col divide-y">
                @foreach ($domains as $domain)
                    <div class="flex flex-col gap-3 p-6">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="font-medium">
                                    {{ $domain->host }}
                                    @if ($domain->is_primary)
                                        <april:badge variant="secondary" class="ml-2">Main</april:badge>
                                    @endif
                                </p>
                                <p class="text-sm text-muted-foreground">
                                    Opens {{ $domain->school?->name ?? 'the organization, with no campus chosen' }}.
                                </p>
                            </div>

                            <div class="flex items-center gap-2">
                                @if ($domain->isVerified())
                                    <april:badge>Proved {{ $domain->verified_at?->format('j M Y') }}</april:badge>
                                @else
                                    <form action="{{ route('organizations.domains.verify', [$organization, $domain]) }}" method="POST">
                                        @csrf
                                        <april:button type="submit" variant="outline" size="sm">Prove it now</april:button>
                                    </form>
                                @endif

                                <form action="{{ route('organizations.domains.destroy', [$organization, $domain]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <april:button type="submit" variant="ghost" size="sm">Give it up</april:button>
                                </form>
                            </div>
                        </div>

                        @unless ($domain->isVerified())
                            <div class="rounded-lg bg-muted/40 p-4 text-sm">
                                <p class="font-medium">Add this record where the address is managed:</p>
                                <dl class="mt-2 grid gap-1 sm:grid-cols-[6rem_1fr]">
                                    <dt class="text-muted-foreground">Type</dt>
                                    <dd>TXT</dd>
                                    <dt class="text-muted-foreground">Name</dt>
                                    <dd class="break-all font-mono text-xs">{{ $domain->verificationRecord() }}</dd>
                                    <dt class="text-muted-foreground">Value</dt>
                                    <dd class="break-all font-mono text-xs">{{ $domain->verification_token }}</dd>
                                </dl>
                                <p class="mt-2 text-xs text-muted-foreground">
                                    Records take a few minutes to travel. Until this one is found, the address does nothing.
                                </p>
                            </div>
                        @endunless
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
