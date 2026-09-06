{{-- Overrides april-ui so a refused field says so. See app/helpers.php. --}}
<select data-slot="native-select" {{$attributes->merge(april_field_error_attributes($attributes))->twMerge(["flex h-10 rounded-md border border-input bg-background px-3
    py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none
    focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed
    disabled:opacity-50"])}}
    >
    {{$slot}}
</select>
