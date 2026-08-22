@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['text' => 'Requests', 'active'],
]])

@section('title', 'Requests')
@section('page_heading', 'Requests')

@section('content')
    <div class="space-y-6">
        @if ($errors->has('portal_request'))
            <april:alert variant="destructive">
                <slot:title>The request was not sent</slot:title>
                <slot:description>{{ $errors->first('portal_request') }}</slot:description>
            </april:alert>
        @endif

        <april:card>
            <slot:title>Ask the school for something</slot:title>
            <slot:description>
                A request is a message about {{ $studentRecord->user?->name ?? 'your child' }}. It changes no school
                record by itself: somebody at the school reads it and answers.
            </slot:description>
            <slot:content>
                <form method="POST" action="{{ route('portal.requests.store', $studentRecord) }}" class="grid gap-4 lg:grid-cols-4 lg:items-end">
                    @csrf

                    <div class="flex flex-col gap-2 lg:col-span-2">
                        <april:label for="subject">What you need</april:label>
                        <april:input id="subject" name="subject" value="{{ old('subject') }}" required
                            placeholder="A copy of last term's report" />
                        @error('subject') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <april:label for="type">Kind of request</april:label>
                        <april:native-select id="type" name="type" required>
                            @foreach ($types as $type)
                                <option value="{{ $type->value }}" @selected(old('type') === $type->value)>{{ $type->label() }}</option>
                            @endforeach
                        </april:native-select>
                        @error('type') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col gap-2 lg:col-span-3">
                        <april:label for="message">Anything else the school should know</april:label>
                        <textarea id="message" name="message" rows="3"
                            class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                            placeholder="Optional">{{ old('message') }}</textarea>
                        @error('message') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>

                    <april:button type="submit">
                        <x-lucide-send class="mr-2 size-4" />
                        Send the request
                    </april:button>
                </form>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>What you have asked for</slot:title>
            <slot:description>The school's answer appears here when it is ready.</slot:description>
            <slot:content>
                @if ($requests->isEmpty())
                    <x-empty-state icon="lucide-message-square" title="You have not asked for anything yet"
                        description="Send a request above and it appears here with the school's answer." />
                @else
                    <ol class="space-y-3">
                        @foreach ($requests as $request)
                            <li class="rounded-lg border p-4">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="font-medium">{{ $request->subject }}</p>
                                        <p class="text-sm text-muted-foreground">
                                            {{ $request->type->label() }} · sent {{ $request->created_at->format('j M Y') }}
                                        </p>
                                        @if (filled($request->message))
                                            <p class="mt-2 whitespace-pre-line text-sm">{{ $request->message }}</p>
                                        @endif
                                    </div>
                                    <span class="whitespace-nowrap rounded-full border px-2.5 py-0.5 text-xs font-semibold">
                                        {{ $request->status->label() }}
                                    </span>
                                </div>

                                @if (filled($request->response))
                                    <div class="mt-3 rounded-md bg-muted/40 p-3">
                                        <p class="text-xs text-muted-foreground">
                                            The school answered on {{ $request->answered_at?->format('j M Y') }}
                                        </p>
                                        <p class="mt-1 whitespace-pre-line text-sm">{{ $request->response }}</p>
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                @endif
            </slot:content>
        </april:card>
    </div>
@endsection
