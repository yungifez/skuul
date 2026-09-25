@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('report-cards.index'), 'text' => 'Report cards', 'active'],
]])

@section('title', 'Report cards')
@section('page_heading', 'Report cards')

@section('content')
    <div class="space-y-6">
        @if ($errors->has('report_card'))
            <april:alert variant="destructive">
                <slot:title>The report card was not published</slot:title>
                <slot:description>{{ $errors->first('report_card') }}</slot:description>
            </april:alert>
        @endif

        @can('create', App\Models\ReportCardSnapshot::class)
            <april:card>
                <slot:title>Publish a report card</slot:title>
                <slot:description>Official record built from published results. Once issued, it stays unchanged; a correction creates a new revision.</slot:description>
                <slot:content>
                    <form method="POST" action="{{ route('report-cards.store') }}" class="grid gap-4 lg:grid-cols-4 lg:items-end">
                        @csrf

                        <div class="flex flex-col gap-2">
                            <april:label for="report-card-student">Learner</april:label>
                            <april:native-select id="report-card-student" name="student_record_id" required class="w-full min-w-0">
                                <option value="">Choose a learner</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}" @selected(old('student_record_id') == $student->id)>
                                        {{ $student->user?->name ?? 'Unnamed' }} · {{ $student->admission_number }}
                                    </option>
                                @endforeach
                            </april:native-select>
                            <x-field-error name="student_record_id" />
                        </div>

                        <div class="flex flex-col gap-2">
                            <april:label for="report-card-period">{{ school_term('period', 'Academic period') }}</april:label>
                            <april:native-select id="report-card-period" name="academic_period_id" required class="w-full min-w-0">
                                <option value="">Choose a {{ school_term('period', 'period') }}</option>
                                @foreach ($periods as $period)
                                    <option value="{{ $period->id }}" @selected(old('academic_period_id') == $period->id)>
                                        {{ $period->academicYear?->name }} · {{ $period->displayName }}
                                    </option>
                                @endforeach
                            </april:native-select>
                            <x-field-error name="academic_period_id" />
                        </div>

                        <div class="flex flex-col gap-2">
                            <april:label for="report-card-reason">Reason for a revision</april:label>
                            <april:input id="report-card-reason" name="reason" value="{{ old('reason') }}"
                                placeholder="Only needed when reissuing" />
                        </div>

                        <april:button type="submit">
                            <x-lucide-file-check class="mr-2 size-4" />
                            Publish
                        </april:button>
                    </form>
                </slot:content>
            </april:card>
        @endcan

        @livewire('report-card-directory')
    </div>
@endsection
