@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('organizations.index'), 'text' => 'Organizations'],
    ['href' => route('organizations.show', $organization), 'text' => $organization->name],
    ['href' => route('organizations.billing-groups.index', $organization), 'text' => 'Billing groups', 'active'],
]])

@section('title', __('Billing groups'))

@section('page_heading', __('Billing groups'))

@section('content')
<div class="mx-auto flex w-full max-w-4xl flex-col gap-6">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-foreground md:text-3xl">Which campuses keep one purse</h2>
        <p class="mt-1 text-sm text-muted-foreground">
            Campuses in one group bill a family as one school: when a learner moves between them, what they owe moves
            with them, and each campus's books still balance because the two campuses settle with each other. Campuses
            outside a group keep their own books, and a debt stays with the campus that is owed it.
        </p>
    </div>

    <x-display-validation-errors />

    <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
        <div class="flex flex-col gap-1.5 border-b p-6">
            <h3 class="text-lg font-semibold leading-none tracking-tight">Start a group</h3>
        </div>

        <form action="{{ route('organizations.billing-groups.store', $organization) }}" method="POST" class="flex flex-wrap items-end gap-3 p-6">
            @csrf
            <div class="flex min-w-64 flex-1 flex-col gap-2">
                <label for="group-name" class="text-sm font-medium leading-none">Name</label>
                <input id="group-name" name="name" required maxlength="100" value="{{ old('name') }}"
                    placeholder="City campuses"
                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
            </div>
            <april:button type="submit">Add group</april:button>
        </form>
    </div>

    <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
        <div class="flex flex-col gap-1.5 border-b p-6">
            <h3 class="text-lg font-semibold leading-none tracking-tight">Campuses</h3>
            <p class="text-sm text-muted-foreground">Changing a group moves nothing already in the books.</p>
        </div>

        @if ($campuses->isEmpty())
            <div class="p-10 text-center text-sm text-muted-foreground">This organization has no campuses yet.</div>
        @else
            <div class="flex flex-col divide-y">
                @foreach ($campuses as $campus)
                    <form action="{{ route('organizations.billing-groups.update', [$organization, $campus]) }}" method="POST"
                        class="flex flex-wrap items-end justify-between gap-3 p-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <p class="font-medium">{{ $campus->name }}</p>
                            <p class="text-sm text-muted-foreground">
                                {{ $campus->billingGroup?->name ?? 'Bills on its own' }}
                            </p>
                        </div>

                        <div class="flex items-end gap-2">
                            <div class="flex flex-col gap-2">
                                <label for="group-{{ $campus->id }}" class="text-sm font-medium leading-none">Purse</label>
                                <select id="group-{{ $campus->id }}" name="billing_group_id"
                                    class="flex h-10 w-56 rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                                    <option value="">Bills on its own</option>
                                    @foreach ($groups as $group)
                                        <option value="{{ $group->id }}" @selected($campus->billing_group_id === $group->id)>{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <april:button type="submit" variant="outline">Save</april:button>
                        </div>
                    </form>
                @endforeach
            </div>
        @endif
    </div>

    @if ($groups->isNotEmpty())
        <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col gap-1.5 border-b p-6">
                <h3 class="text-lg font-semibold leading-none tracking-tight">Who bills with whom</h3>
            </div>
            <div class="flex flex-col divide-y">
                @foreach ($groups as $group)
                    <div class="p-6">
                        <p class="font-medium">{{ $group->name }}</p>
                        <p class="mt-1 text-sm text-muted-foreground">
                            {{ $group->schools->isEmpty() ? 'No campus yet.' : $group->schools->pluck('name')->join(', ', ' and ') }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
