---
paths:
  - 'resources/views/**/*.blade.php'
---

# Blade views

## Keep compiled PHP out of april component tags
A Blade directive that compiles to a PHP block inside an `<april:*>` opening tag
breaks April's tag precompiler. It stops matching the opening tag but still
rewrites the closing one, so the view compiles to unbalanced PHP.

Two failure shapes seen so far:

- `wire:key` on a component inside a loop throws
  "syntax error, unexpected token endif", pointing at a line that looks
  unrelated. Livewire injects a `<?php ... ?>` block into the tag.
- `@checked(...)` drops the whole component without an error, so the control
  never renders at all.

`{{ }}` interpolation inside an attribute value is safe: `id="row-{{ $id }}"`.
Directives are not.

A bare `{{ }}` that stands where an attribute goes breaks it the same way:
`<april:input name="foo" {{ field_error_bindings('foo') }} />` throws
"unexpected token endif". Only interpolation inside a quoted value is safe.
An april component that needs conditional attributes has to work them out
itself. Override it under `resources/views/vendor/april/components/`.

Write it this way instead:

- Put `wire:key` on a plain element that wraps the component:
  `<span wire:key="row-{{ $id }}"><april:badge>...</april:badge></span>`
- For a form control that needs `@checked`, `@selected`, or `@disabled`, use a
  native `<input>` with Tailwind classes rather than the April component.

## april:button is a button element, so href does nothing

`<april:button href="...">` compiles to `<button href="...">`, which never
navigates. Use `<april:button-link href="...">` for anything that goes to
another page, and keep `<april:button>` for form submits.
`x-resource-create-action` carried this bug on every index page until it was
fixed, so the create button looked right and did nothing.

## Two pagination views: pick the one that matches the page

`components.datatable-pagination-links-view` moves pages with `wire:click`. It
works only inside a Livewire component. On a page a controller renders, its
buttons render but do nothing, so page 2 is unreachable.

- Livewire list: `{{ $items->links('components.datatable-pagination-links-view') }}`
- Controller-rendered Blade page: `{{ $items->links('components.pagination-links-view') }}`

The second one uses `<april:button-link href>` with the paginator URLs. Call
`->withQueryString()` on the paginator so a filter survives the page change.

## A filter menu lists every option, so do not assert on names

A screen that filters a list still renders every learner in the select. A test
that asserts `assertDontSee('Ben Hidden')` fails on the menu, not the rows.
Assert on something only a row carries: the row's show-route URL, or a value
the record holds.

## Say on the control that a field was refused

A message on its own reaches nobody who cannot see where it sits. Every form
control that has a message must carry `aria-invalid` and point at that message
with `aria-describedby`.

- Render the message with `<x-field-error name="admission_date" />`. It gives
  the message the id the control points at.
- On a native `<input>`, `<select>` or `<textarea>`, add
  `{{ field_error_bindings('admission_date') }}` inside the tag.
- `<april:input>`, `<april:native-select>` and `<april:textarea>` read their own
  `name` or `wire:model` and wire themselves. Add nothing.

`app/helpers.php` holds all three helpers, so both sides work the id out the
same way. `tests/Feature/FieldErrorWiringTest.php` covers it.
