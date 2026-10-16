{{-- Overrides april-ui so a refused field says so. See app/helpers.php. --}}
@props(['type' => 'text', 'isChoiceInput'])

@php
$isChoiceInput = $isChoiceInput ?? in_array($type, ['checkbox', 'radio']);
@endphp
<input type="{{$type}}" {{$attributes->merge(april_field_error_attributes($attributes))->merge(["data-slot" => "input"])->twMerge([
"flex ring-offset-background disabled:cursor-not-allowed
disabled:opacity-50 border",

"text-sm bg-background h-10 w-full px-3 py-2 file:border-0 file:bg-transparent file:text-sm file:text-foreground
file:font-medium
placeholder:text-muted-foreground
rounded-md
focus-visible:outline-none border-input focus-visible:ring-2
focus-visible:ring-ring focus-visible:ring-offset-2 accent-primary" => !$isChoiceInput,

"appearance-none justify-center h-4 w-4 before:h-4 before:w-4
before:bg-foreground before:content-[''] before:scale-0 border-primary cursor-pointer
focus-visible:outline-background focus:visible:outline focus:visible:outline-2 focus:visible:outline-offset-2
checked:before:scale-100"
=> $isChoiceInput,

"rounded-sm checkbox-clip-path cursor-pointer" => $type == 'checkbox',
"rounded-full radio-clip-path cursor-pointer" => $type == 'radio',
])}}>
