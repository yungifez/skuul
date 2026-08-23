@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('dormitories.index'), 'text' => 'Boarding', 'active'],
]])

@section('title', __('Boarding'))

@section('page_heading', __('Boarding'))

@section('page_actions')
    <x-resource-create-action :href="route('dormitories.create')" ability="create" :arguments="[\App\Models\Dormitory::class]">Open a house</x-resource-create-action>
@endsection

@section('content')
<div class="mx-auto flex w-full max-w-5xl flex-col gap-6">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-foreground md:text-3xl">Houses</h2>
        <p class="mt-1 text-sm text-muted-foreground">
            A house holds as many boarders as it has beds, so nobody has to keep a capacity number up to date.
        </p>
    </div>

    @if ($dormitories->isEmpty())
        <div class="flex flex-col items-center gap-3 rounded-xl border border-dashed border-sidebar-border/70 p-10 text-center">
            <span class="flex size-12 items-center justify-center rounded-full bg-muted text-muted-foreground">
                <x-lucide-bed-double class="size-6" />
            </span>
            <p class="text-sm font-medium">No houses yet.</p>
            <p class="max-w-sm text-sm text-muted-foreground">
                Open one and say how many rooms it has. The beds are made for you.
            </p>
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-2">
            @foreach ($dormitories as $dormitory)
                @php ($counts = $occupancy[$dormitory->id])
                <a href="{{ route('dormitories.show', $dormitory->id) }}"
                    class="group flex flex-col gap-3 rounded-xl border border-sidebar-border/70 bg-card p-6 text-card-foreground shadow-sm transition-colors hover:border-primary/40">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-muted-foreground">{{ $dormitory->label }}</p>
                            <h3 class="mt-1 text-lg font-semibold leading-none tracking-tight">{{ $dormitory->name }}</h3>
                        </div>
                        <x-lucide-arrow-right class="size-4 shrink-0 text-muted-foreground transition-transform group-hover:translate-x-0.5" />
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <april:badge variant="outline">{{ $counts['taken'] }} of {{ $counts['beds'] }} beds</april:badge>
                        <april:badge variant="outline">{{ $counts['free'] }} free</april:badge>
                        @if ($counts['away'] > 0)
                            <april:badge variant="secondary">{{ $counts['away'] }} out tonight</april:badge>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
