@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('graduation-plans.index'), 'text' => 'Graduation plans', 'active'],
]])

@section('title', 'Graduation plans')
@section('page_heading', 'Graduation plans')

@section('page_actions')
    @can('create', App\Models\GraduationPlan::class)
        <april:button-link href="{{ route('graduation-plans.create') }}">
            <x-lucide-plus class="mr-2 size-4" />
            Write a plan
        </april:button-link>
    @endcan
@endsection

@section('content')
    <april:card>
        <slot:title>What a learner must finish</slot:title>
        <slot:description>
            A plan lists what the school will accept before a learner graduates. Only a published result counts
            towards it, so work still in the gradebook never moves a learner along.
        </slot:description>
        <slot:content>
            @if ($plans->isEmpty())
                <x-empty-state icon="lucide-graduation-cap" title="No graduation plans yet"
                    description="Write one to say which subjects a learner must pass, and how many credits they need.">
                    @can('create', App\Models\GraduationPlan::class)
                        <april:button-link href="{{ route('graduation-plans.create') }}">Write the first plan</april:button-link>
                    @endcan
                </x-empty-state>
            @else
                <april:data-table>
                    <slot:header>
                        <april:data-table-row>
                            <april:data-table-head>Plan</april:data-table-head>
                            <april:data-table-head>Credits</april:data-table-head>
                            <april:data-table-head>Requirements</april:data-table-head>
                            <april:data-table-head>For</april:data-table-head>
                            <april:data-table-head class="text-right">Actions</april:data-table-head>
                        </april:data-table-row>
                    </slot:header>
                    <slot:body>
                        @foreach ($plans as $plan)
                            <april:data-table-row>
                                <april:data-table-cell class="font-medium">
                                    {{ $plan->name }}
                                    @if (filled($plan->description))
                                        <span class="block text-xs text-muted-foreground">{{ $plan->description }}</span>
                                    @endif
                                </april:data-table-cell>
                                <april:data-table-cell class="text-muted-foreground">
                                    {{ $plan->uses_credits ? $plan->required_credits.' needed' : 'Not counted' }}
                                </april:data-table-cell>
                                <april:data-table-cell>{{ $plan->requirements_count }}</april:data-table-cell>
                                <april:data-table-cell class="text-muted-foreground">
                                    {{ $plan->cohort?->name ?? 'Every learner' }}
                                </april:data-table-cell>
                                <april:data-table-cell class="text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @unless ($plan->is_active)
                                            <span class="whitespace-nowrap rounded-full border border-dashed px-2.5 py-0.5 text-xs text-muted-foreground">Closed</span>
                                        @endunless
                                        <april:button-link href="{{ route('graduation-plans.show', $plan) }}" variant="outline" size="sm">
                                            <x-lucide-eye class="mr-1 size-4" />
                                            Open
                                        </april:button-link>
                                    </div>
                                </april:data-table-cell>
                            </april:data-table-row>
                        @endforeach
                    </slot:body>
                </april:data-table>

                <div class="pt-4">
                    {{ $plans->links('components.pagination-links-view') }}
                </div>
            @endif
        </slot:content>
    </april:card>
@endsection
