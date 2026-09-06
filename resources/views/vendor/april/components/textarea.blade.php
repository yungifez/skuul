{{-- Overrides april-ui so a refused field says so. See app/helpers.php. --}}
<textarea {{$attributes->merge(april_field_error_attributes($attributes))->merge(["data-slot" => "textarea"])->twMerge(["flex min-h-[80px] rounded-md border bg-background px-3 py-2
    text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2
    focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed
    disabled:opacity-50 border-input"])}}></textarea>
