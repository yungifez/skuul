@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('imports.index'), 'text' => 'Imports', 'active'],
]])

@section('title', 'Imports')
@section('page_heading', 'Imports')

@section('content')
    <div class="space-y-6">
        @can('create', App\Models\ImportBatch::class)
            <april:card>
                <slot:title>Import a file</slot:title>
                <slot:description>The file is checked first and nothing is written. You read what each row will do, then you choose to write it.</slot:description>
                <slot:content>
                    <form method="POST" action="{{ route('imports.store') }}" enctype="multipart/form-data"
                        class="grid gap-4 lg:grid-cols-4 lg:items-end">
                        @csrf

                        <div class="flex flex-col gap-2">
                            <april:label for="import-type">What the file holds</april:label>
                            <april:native-select id="import-type" name="type" required>
                                <option value="">Choose an import</option>
                                @foreach ($imports as $import)
                                    <option value="{{ $import['key'] }}" @selected(old('type') === $import['key'])>
                                        {{ $import['title'] }}
                                    </option>
                                @endforeach
                            </april:native-select>
                            <x-field-error name="type" />
                        </div>

                        <div class="flex flex-col gap-2 lg:col-span-2">
                            <april:label for="import-file">CSV file</april:label>
                            <input type="file" id="import-file" name="file" accept=".csv,text/csv" required
                                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm file:mr-3 file:border-0 file:bg-transparent file:text-sm file:font-medium"  {{ field_error_bindings('file') }}/>
                            <p class="text-xs text-muted-foreground">Up to 5 MB. The first line must name the columns.</p>
                            <x-field-error name="file" />
                        </div>

                        <april:button type="submit">
                            <x-lucide-upload class="mr-2 size-4" />
                            Check the file
                        </april:button>
                    </form>
                </slot:content>
            </april:card>
        @endcan

        <april:card>
            <slot:title>What each file must hold</slot:title>
            <slot:description>Name the columns on the first line. The order does not matter, and the names are read without regard to case.</slot:description>
            <slot:content>
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach ($imports as $import)
                        <div class="rounded-lg border p-4">
                            <p class="font-medium">{{ $import['title'] }}</p>
                            <dl class="mt-3 space-y-3 text-sm">
                                <div>
                                    <dt class="text-muted-foreground">Required columns</dt>
                                    <dd class="mt-1 flex flex-wrap gap-1">
                                        @foreach ($import['required'] as $column)
                                            <code class="rounded bg-muted px-1.5 py-0.5 text-xs">{{ $column }}</code>
                                        @endforeach
                                    </dd>
                                </div>
                                @if ($import['optional'] !== [])
                                    <div>
                                        <dt class="text-muted-foreground">Optional columns</dt>
                                        <dd class="mt-1 flex flex-wrap gap-1">
                                            @foreach ($import['optional'] as $column)
                                                <code class="rounded bg-muted px-1.5 py-0.5 text-xs">{{ $column }}</code>
                                            @endforeach
                                        </dd>
                                    </div>
                                @endif
                            </dl>
                            <p class="mt-3 text-xs text-muted-foreground">
                                Give a row a <code class="rounded bg-muted px-1 py-0.5">source_id</code> to import the same
                                file twice without making a second record.
                            </p>
                        </div>
                    @endforeach
                </div>
            </slot:content>
        </april:card>

        @livewire('import-batch-directory')
    </div>
@endsection
