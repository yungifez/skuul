@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('portal.overview'), 'text' => 'My school'],
    ['text' => 'Results', 'active'],
]])

@section('title', 'Results')
@section('page_heading', 'Results')

@section('content')
    <div class="mx-auto flex w-full max-w-4xl flex-col gap-5">
        <p class="text-sm text-muted-foreground">
            The approved result of each course of {{ $studentRecord->user?->name }} · {{ $studentRecord->school?->name }}.
            A mark the school is still checking is not shown.
        </p>

        <april:card>
            <slot:content>
                @if ($results->isEmpty())
                    <x-empty-state icon="lucide-graduation-cap" title="No approved results yet"
                        description="Results appear here when the school approves them." />
                @else
                    <div class="overflow-x-auto beautify-scrollbar">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b text-left text-muted-foreground">
                                    <th scope="col" class="py-2 pr-3 font-medium">Subject</th>
                                    <th scope="col" class="py-2 pr-3 font-medium">Term</th>
                                    <th scope="col" class="py-2 pr-3 text-right font-medium">Score</th>
                                    <th scope="col" class="py-2 font-medium">Approved</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach ($results as $result)
                                    <tr>
                                        <td class="py-2 pr-3 font-medium">{{ $result->courseOffering?->subject?->name ?? '—' }}</td>
                                        <td class="py-2 pr-3">{{ $result->courseOffering?->academicPeriod?->name ?? '—' }}</td>
                                        <td class="py-2 pr-3 text-right tabular-nums">{{ $result->percentage === null ? '—' : rtrim(rtrim(number_format($result->percentage, 2), '0'), '.').'%' }}</td>
                                        <td class="py-2 whitespace-nowrap">{{ $result->approved_at?->format('j M Y') ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </slot:content>
        </april:card>
    </div>
@endsection
