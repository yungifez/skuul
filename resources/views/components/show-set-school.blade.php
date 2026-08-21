@if (auth()->user()->can(\App\Enums\PlatformPermission::AccessAllSchools) || auth()->user()->schoolMemberships()->active()->count() > 1)
@php($school = current_school())
<div class="flex min-w-0 items-center gap-2">
    <span class="hidden shrink-0 select-none text-sm text-muted-foreground/60 sm:inline" aria-hidden="true">|</span>
    @if ($school !== null)
        <span class="flex min-w-0 items-center gap-1.5 text-sm text-muted-foreground"
            title="{{ $school->name }} - {{ $school->address }}">
            <x-lucide-map-pin class="size-3.5 shrink-0" />
            <span class="sr-only">You are currently on</span>
            <span class="truncate font-medium text-foreground">{{ $school->name }}</span>
            <span class="hidden shrink-0 select-none text-muted-foreground/60 lg:inline" aria-hidden="true">|</span>
            <span class="hidden truncate lg:inline">{{ $school->address }}</span>
        </span>
    @else
        {{-- Permissions are school-scoped, so a person with no working school
             cannot pass a `can()` check. Anyone who sees this chip already holds
             access to more than one school, so always offer the way to choose. --}}
        <a href="{{ route('schools.index') }}"
            class="flex shrink-0 items-center gap-1.5 rounded-md bg-amber-500/10 px-2 py-1 text-sm font-medium text-amber-600 transition-colors hover:bg-amber-500/20 dark:text-amber-400">
            <x-lucide-map-pin class="size-3.5 shrink-0" />
            Set a school
        </a>
    @endif
</div>
@endif
