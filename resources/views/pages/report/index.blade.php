@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('reports.index'), 'text' => 'Reports', 'active'],
]])

@section('title', __('Reports'))

@section('page_heading', __('Reports'))

@section('content')
<div class="mx-auto flex w-full max-w-5xl flex-col gap-6">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-foreground md:text-3xl">Reports and exports</h2>
        <p class="mt-1 text-sm text-muted-foreground">
            A report is built by a worker, so a whole-school report never holds up the screen. Ask for one here and
            collect the file when it says it is ready. Every request and every download is written to the audit log.
        </p>
    </div>

    <x-display-validation-errors />

    @if ($canRequest)
        <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col gap-1.5 border-b p-6">
                <h3 class="text-lg font-semibold leading-none tracking-tight">Ask for a report</h3>
                <p class="text-sm text-muted-foreground">Pick what to read and the shape you want it in.</p>
            </div>

            <form action="{{ route('reports.store') }}" method="POST" class="flex flex-col gap-4 p-6">
                @csrf

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="flex flex-col gap-2">
                        <label for="report-type" class="text-sm font-medium leading-none">Report</label>
                        <select id="report-type" name="type" required
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                            @foreach ($reports as $key => $title)
                                <option value="{{ $key }}" @selected(old('type') === $key)>{{ $title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="report-format" class="text-sm font-medium leading-none">Shape</label>
                        <select id="report-format" name="format"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                            @foreach ($formats as $key => $label)
                                <option value="{{ $key }}" @selected(old('format', 'csv') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-muted-foreground">
                            A comma-separated file opens anywhere. A spreadsheet keeps numbers as numbers. A document is
                            for filing and handing over.
                        </p>
                    </div>
                </div>

                <div>
                    <april:button type="submit">
                        <x-lucide-play class="mr-2 size-4" />
                        Build it
                    </april:button>
                </div>
            </form>
        </div>
    @endif

    <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
        <div class="flex flex-col gap-1.5 border-b p-6">
            <h3 class="text-lg font-semibold leading-none tracking-tight">What has been asked for</h3>
        </div>

        @if ($runs->isEmpty())
            <div class="flex flex-col items-center gap-3 p-10 text-center">
                <span class="flex size-12 items-center justify-center rounded-full bg-muted text-muted-foreground">
                    <x-lucide-file-text class="size-6" />
                </span>
                <p class="text-sm font-medium">Nobody has asked for a report yet.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left text-xs uppercase tracking-wider text-muted-foreground">
                            <th class="p-4 font-medium">Number</th>
                            <th class="p-4 font-medium">Report</th>
                            <th class="p-4 font-medium">Shape</th>
                            <th class="p-4 font-medium">Asked by</th>
                            <th class="p-4 font-medium">State</th>
                            <th class="p-4 font-medium">Rows</th>
                            <th class="p-4 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($runs as $run)
                            <tr class="border-b last:border-0">
                                <td class="p-4 font-medium">{{ $run->id }}</td>
                                <td class="p-4">
                                    {{ $reports[$run->type] ?? $run->type }}
                                    <span class="block text-xs text-muted-foreground">{{ $run->created_at?->diffForHumans() }}</span>
                                </td>
                                <td class="p-4 uppercase text-muted-foreground">{{ $run->format }}</td>
                                <td class="p-4 text-muted-foreground">{{ $run->requestedBy?->name ?? '—' }}</td>
                                <td class="p-4">
                                    <april:badge variant="{{ $run->status === \App\Enums\ReportStatus::Failed ? 'destructive' : ($run->status === \App\Enums\ReportStatus::Ready ? 'default' : 'secondary') }}">
                                        {{ $run->status->label() }}
                                    </april:badge>
                                    @if ($run->error !== null)
                                        <span class="block max-w-xs text-xs text-muted-foreground">{{ $run->error }}</span>
                                    @endif
                                </td>
                                <td class="p-4 text-muted-foreground">{{ $run->row_count ?? '—' }}</td>
                                <td class="p-4 text-right">
                                    @if ($run->isReady())
                                        <april:button-link href="{{ route('reports.download', $run->id) }}" variant="outline" size="sm">
                                            Download
                                        </april:button-link>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t p-4">
                {{ $runs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
