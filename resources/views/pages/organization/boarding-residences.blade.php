@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('organizations.index'), 'text' => 'Organizations'],
    ['href' => route('organizations.show', $organization), 'text' => $organization->name],
    ['text' => 'Shared residences', 'active'],
]])

@section('title', 'Shared residences')

@section('page_heading', 'Shared residences')

@section('content')
<div class="mx-auto flex w-full max-w-6xl flex-col gap-6">
    <div>
        <p class="text-xs font-medium uppercase text-muted-foreground">{{ $organization->name }}</p>
        <h2 class="mt-1 text-2xl font-bold tracking-tight text-foreground md:text-3xl">Shared residences</h2>
        <p class="mt-1 max-w-3xl text-sm text-muted-foreground">
            Group the school-owned houses that sit in one physical residence. Student records, room rosters, beds, and staff permissions stay with each campus.
        </p>
    </div>

    <x-display-validation-errors />

    <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
        <div class="border-b p-6">
            <h3 class="text-lg font-semibold">Add a shared residence</h3>
            <p class="mt-1 text-sm text-muted-foreground">Use this for one physical boarding site used by more than one campus.</p>
        </div>
        <form action="{{ route('organizations.boarding-residences.store', $organization) }}" method="POST" class="grid gap-4 p-6 md:grid-cols-[1fr_1fr_auto] md:items-end">
            @csrf
            <div class="flex flex-col gap-2">
                <label for="residence-name" class="text-sm font-medium">Residence name</label>
                <input id="residence-name" name="name" required maxlength="100" value="{{ old('name') }}" placeholder="North campus residence"
                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
            </div>
            <div class="flex flex-col gap-2">
                <label for="residence-notes" class="text-sm font-medium">Notes <span class="font-normal text-muted-foreground">(optional)</span></label>
                <input id="residence-notes" name="notes" maxlength="1000" value="{{ old('notes') }}" placeholder="Shared by two campuses"
                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
            </div>
            <april:button type="submit">Create residence</april:button>
        </form>
    </div>

    @forelse ($residences as $residence)
        @php($linkedCampusIds = $residence->schools->pluck('id'))
        <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4 border-b p-6">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h3 class="text-lg font-semibold">{{ $residence->name }}</h3>
                        @if (!$residence->is_active)
                            <april:badge variant="outline">Inactive</april:badge>
                        @endif
                    </div>
                    @if ($residence->notes)
                        <p class="mt-1 text-sm text-muted-foreground">{{ $residence->notes }}</p>
                    @endif
                </div>
                <p class="text-sm text-muted-foreground">{{ $residence->schools->count() }} campuses · {{ $residence->dormitories->count() }} houses</p>
            </div>

            <div class="grid gap-6 p-6 lg:grid-cols-2">
                <section>
                    <h4 class="font-medium">Campuses using this residence</h4>
                    <div class="mt-3 overflow-x-auto rounded-lg border">
                        <table class="w-full min-w-[420px] text-left text-sm">
                            <thead class="border-b bg-muted/40 text-xs uppercase text-muted-foreground">
                                <tr>
                                    <th scope="col" class="px-4 py-3 font-medium">Campus</th>
                                    <th scope="col" class="px-4 py-3 text-right font-medium">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @forelse ($residence->schools as $campus)
                                    <tr class="align-middle">
                                        <td class="px-4 py-3 font-medium">{{ $campus->name }}</td>
                                        <td class="px-4 py-3 text-right">
                                            @if ($residence->dormitories->where('school_id', $campus->id)->isEmpty())
                                                <form action="{{ route('organizations.boarding-residences.schools.destroy', [$organization, $residence, $campus]) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <april:button type="submit" variant="ghost" size="sm">Remove</april:button>
                                                </form>
                                            @else
                                                <span class="text-xs text-muted-foreground">Move houses first</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="px-4 py-6 text-center text-muted-foreground">No campuses are linked yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($campuses->whereNotIn('id', $linkedCampusIds)->isNotEmpty())
                        <form action="{{ route('organizations.boarding-residences.schools.store', [$organization, $residence]) }}" method="POST" class="mt-3 flex flex-wrap items-end gap-2">
                            @csrf
                            <div class="flex min-w-56 flex-1 flex-col gap-1">
                                <label for="campus-{{ $residence->id }}" class="text-xs font-medium">Add campus</label>
                                <select id="campus-{{ $residence->id }}" name="school_id" required
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                                    <option value="">Choose a campus</option>
                                    @foreach ($campuses->whereNotIn('id', $linkedCampusIds) as $campus)
                                        <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <april:button type="submit" size="sm">Link campus</april:button>
                        </form>
                    @endif
                </section>

                <section>
                    <h4 class="font-medium">Houses in this residence</h4>
                    <div class="mt-3 overflow-x-auto rounded-lg border">
                        <table class="w-full min-w-[420px] text-left text-sm">
                            <thead class="border-b bg-muted/40 text-xs uppercase text-muted-foreground">
                                <tr>
                                    <th scope="col" class="px-4 py-3 font-medium">House</th>
                                    <th scope="col" class="px-4 py-3 font-medium">Campus</th>
                                    <th scope="col" class="px-4 py-3 text-right font-medium">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @forelse ($residence->dormitories as $dormitory)
                                    <tr class="align-middle">
                                        <td class="px-4 py-3 font-medium">{{ $dormitory->name }}</td>
                                        <td class="px-4 py-3 text-muted-foreground">{{ $dormitory->school?->name }}</td>
                                        <td class="px-4 py-3 text-right">
                                            <form action="{{ route('organizations.boarding-residences.houses.destroy', [$organization, $residence, $dormitory]) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <april:button type="submit" variant="ghost" size="sm">Remove</april:button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="px-4 py-6 text-center text-muted-foreground">No houses are attached yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @php($availableForResidence = $availableHouses->whereIn('school_id', $linkedCampusIds))
                    @if ($availableForResidence->isNotEmpty())
                        <form action="{{ route('organizations.boarding-residences.houses.store', [$organization, $residence]) }}" method="POST" class="mt-3 flex flex-wrap items-end gap-2">
                            @csrf
                            <div class="flex min-w-56 flex-1 flex-col gap-1">
                                <label for="house-{{ $residence->id }}" class="text-xs font-medium">Add house</label>
                                <select id="house-{{ $residence->id }}" name="dormitory_id" required
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                                    <option value="">Choose a house</option>
                                    @foreach ($availableForResidence as $dormitory)
                                        <option value="{{ $dormitory->id }}">{{ $dormitory->name }} · {{ $dormitory->school?->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <april:button type="submit" size="sm">Add house</april:button>
                        </form>
                    @endif
                </section>
            </div>
        </div>
    @empty
        <div class="rounded-xl border border-dashed p-10 text-center text-sm text-muted-foreground">
            No shared residences have been configured. Campuses can continue using their own boarding houses.
        </div>
    @endforelse
</div>
@endsection
