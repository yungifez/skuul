@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('portal.overview'), 'text' => 'My school'],
    ['text' => 'Graduation progress', 'active'],
]])

@section('title', 'Graduation progress')
@section('page_heading', 'Graduation progress')

@section('content')
    <div class="mx-auto flex w-full max-w-4xl flex-col gap-5">
        <p class="text-sm text-muted-foreground">
            {{ $studentRecord->user?->name }} · {{ $studentRecord->school?->name }}
        </p>

        @forelse ($plans as $entry)
            @php($plan = $entry['plan'])
            @php($progress = $entry['progress'])

            <april:card>
                <slot:title>{{ $plan->name }}</slot:title>
                <slot:description>{{ $plan->description ?: 'Requirements the school checks before graduation.' }}</slot:description>
                <slot:content>
                    <div class="flex flex-wrap items-center gap-2">
                        @if ($progress['is_complete'])
                            <april:badge variant="secondary">Requirements met</april:badge>
                        @else
                            <april:badge variant="outline">In progress</april:badge>
                        @endif
                        @if ($plan->cohort)
                            <span class="text-sm text-muted-foreground">{{ $plan->cohort->name }}</span>
                        @endif
                        @if ($progress['credits_required'] !== null)
                            <span class="text-sm text-muted-foreground">{{ $progress['credits_earned'] }} of {{ $progress['credits_required'] }} credits</span>
                        @endif
                    </div>

                    @if ($progress['stages'] !== [])
                        <section class="mt-5 space-y-3" aria-label="Stage progress">
                            <h3 class="text-sm font-semibold">Stages</h3>
                            @include('pages.portal.partials.graduation-stage-tree', ['stages' => $progress['stages']])
                        </section>
                    @endif

                    @if ($progress['requirements'] !== [])
                        <section class="mt-5 space-y-3" aria-label="Graduation requirements">
                            <h3 class="text-sm font-semibold">Requirements</h3>
                            <ul class="divide-y rounded-md border">
                                @foreach ($progress['requirements'] as $requirement)
                                    <li class="flex flex-wrap items-start justify-between gap-2 px-3 py-3">
                                        <span class="min-w-0">
                                            <span class="block font-medium">{{ $requirement['description'] }}</span>
                                            <span class="text-sm text-muted-foreground">
                                                {{ $requirement['is_negated'] ? 'Must not be met' : 'Must be met' }}
                                                @if ($requirement['percentage'] !== null)
                                                    · {{ number_format($requirement['percentage'], 2) }}%
                                                @endif
                                            </span>
                                        </span>
                                        <span class="text-sm">{{ match ($requirement['state']) {
                                            'met' => 'Met',
                                            'exempt' => 'Excused',
                                            'not_met' => 'Below pass mark',
                                            'no_result' => 'No published result',
                                            'not_judged' => 'Checked outside school results',
                                            default => 'Not complete',
                                        } }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </section>
                    @endif
                </slot:content>
            </april:card>
        @empty
            <x-empty-state
                icon="lucide-graduation-cap"
                title="No graduation plan applies yet"
                description="The school has not assigned an active plan to this learner or their group." />
        @endforelse

        <p class="text-sm text-muted-foreground">Only published results count. Work still in the gradebook is not shown here.</p>
    </div>
@endsection
