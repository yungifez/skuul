@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('course-offerings.index'), 'text' => school_terms('course', 'Course').' being taught', 'active'],
]])

@section('title', school_terms('course', 'Course').' being taught')
@section('page_heading', school_terms('course', 'Course').' being taught')

@section('page_actions')
    <x-resource-create-action :href="route('course-offerings.create')" ability="create" :arguments="[\App\Models\CourseOffering::class]">Add {{ school_term('course', 'course') }}</x-resource-create-action>
@endsection

@section('content')
    <april:card>
        <slot:title>{{ school_terms('course', 'Course') }} for an exact {{ school_term('period', 'period') }}</slot:title>
        <slot:description>Create a dated offering from an existing subject. Home sections are its default groups; learner placement remains separate.</slot:description>
        <slot:content>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-sm">
                    <thead class="border-b text-left text-muted-foreground">
                        <tr><th class="px-3 py-2">Subject</th><th class="px-3 py-2">Academic level</th><th class="px-3 py-2">Period</th><th class="px-3 py-2">Roster</th><th class="px-3 py-2">Status</th><th class="px-3 py-2"></th></tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($courseOfferings as $courseOffering)
                            <tr>
                                <td class="px-3 py-3 font-medium">{{ $courseOffering->subject->name }}<span class="ml-1 text-muted-foreground">{{ $courseOffering->subject->short_name }}</span></td>
                                <td class="px-3 py-3">{{ $courseOffering->academicLevel->name }}</td>
                                <td class="px-3 py-3">{{ $courseOffering->academicYear->name }} · {{ $courseOffering->academicPeriod->display_name }}</td>
                                <td class="px-3 py-3">
                                    <span class="block">{{ $courseOffering->roster_mode->label() }}</span>
                                    <span class="text-muted-foreground">{{ $courseOffering->roster_mode->usesHomeSections() ? $courseOffering->cycleSections->map(fn ($section) => $section->label ?? $section->name)->join(', ') : ($courseOffering->roster_mode === \App\Enums\RosterMode::AcademicLevel ? 'All learners in this level' : $courseOffering->studentRecords->map(fn ($record) => $record->user?->name ?? $record->admission_number)->join(', ')) }}</span>
                                </td>
                                <td class="px-3 py-3"><april:badge>{{ $courseOffering->status->label() }}</april:badge></td>
                                <td class="px-3 py-3 text-right">
                                    <p class="mb-2 text-xs text-muted-foreground">{{ $courseOffering->teachingAssignments->isEmpty() ? 'No teachers assigned' : $courseOffering->teachingAssignments->map(fn ($assignment) => $assignment->teacher->name.' · '.$assignment->role->label())->join(', ') }}</p>
                                    @can('update', $courseOffering)
                                        @if ($courseOffering->status === \App\Enums\CourseOfferingStatus::Draft)
                                            <form method="POST" action="{{ route('course-offerings.activate', $courseOffering) }}">
                                                @csrf
                                                <april:button size="sm" type="submit">Activate</april:button>
                                            </form>
                                        @endif
                                        <details class="mt-2 text-left">
                                            <summary class="cursor-pointer text-sm text-primary">Assign teacher</summary>
                                            <form method="POST" action="{{ route('course-offerings.teachers.store', $courseOffering) }}" class="mt-2 grid gap-2">
                                                @csrf
                                                <select name="teacher_id" class="rounded-md border border-input bg-background px-2 py-1.5 text-sm" required>
                                                    <option value="">Select a teacher</option>
                                                    @foreach ($teachers as $teacher)
                                                        <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                                    @endforeach
                                                </select>
                                                <select name="role" class="rounded-md border border-input bg-background px-2 py-1.5 text-sm" required>
                                                    @foreach (\App\Enums\TeachingRole::cases() as $role)
                                                        <option value="{{ $role->value }}">{{ $role->label() }}</option>
                                                    @endforeach
                                                </select>
                                                <april:button type="submit">Assign</april:button>
                                            </form>
                                        </details>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-3 py-10 text-center text-muted-foreground">No {{ school_terms('course', 'course') }} exist yet. Add the {{ school_term('course', 'course') }} that will run in a {{ school_term('period', 'period') }}.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $courseOfferings->links() }}</div>
        </slot:content>
    </april:card>
@endsection
