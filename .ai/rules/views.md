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

## Headings step down one level at a time

The page heading in `layouts/app.blade.php` is the only `h1`. Everything under
it must step down one level at a time, so a screen reader reader can trust the
outline.

- `april:card`, `april:alert`, `april:dialog-header` and `april:sheet-header`
  render their title as an `h2`. Pass `level="3"` when the component sits
  inside a section that already holds a heading.
- Do not use a heading to label a dialog or a toast. `aria-labelledby` takes
  any element, and `role="alert"` reads the whole message out.

`tests/Feature/HeadingOrderTest.php` walks real screens and fails on a skip.

## A leading colon is Blade, not Alpine

Blade reads `:attribute="..."` on a component tag as a PHP expression. Alpine
never sees it. `<april:input-group :max="amount">` compiled to a constant named
`amount` and threw "Undefined constant". Write Alpine's long form instead:
`x-bind:max="amount"`. Keep `:id="$record->id.'-amount'"` for a real PHP
binding, which is what the colon is for.

## Name a control that sits in a table row

A column heading names the column, not the control inside it. Give every form
control in a table cell an `aria-label` that names its row and its column:
`aria-label="Status for {{ $boarder }}"`. `tests/Feature/ControlNameTest.php`
walks three screens and fails on a control that nothing names.

## Key a row a Livewire action can remove

Livewire matches rows by position unless `wire:key` says otherwise. A value the
reader typed lives on the DOM node, not in the markup, so removing a row from
the middle carries typed values onto the wrong record. Key by record id:
`<tr wire:key="added-fee-{{ $fee['id'] }}">`. `wire:key` on an `<april:*>` tag
breaks the precompiler, so put it on a plain element.

## Say what a destructive button undoes

`resources/js/app.js` asks the reader to confirm any form carrying the DELETE
method. Without a message it reads "Delete this item? This action cannot be
undone.", which names nothing and is wrong for a DELETE route that only
reverses a setting. Give every such form a `data-confirm` that names the
record: `data-confirm="Remove {{ $house->name }} from {{ $residence->name }}?"`.
Use `data-confirm="false"` only when the screen already asks in its own dialog.
`tests/Unit/DestructiveFormConfirmationTest.php` covers it.

## Hide a row action the server will refuse

`x-table-actions` takes a `when` key on an item, holding an Alpine expression
over `row`: `'when' => 'row.course_offerings_count === 0'`. The control is
hidden on rows the expression rejects. Add the count to the Livewire builder
with `withCount(...)` so the row carries it.

Whenever a service refuses an action on some records, hide the control on
those records as well. A button that always fails teaches the reader to
distrust every button beside it.

## Write money with money_text

A column cast to `Money` formats itself: `$invoice->balance->formatToLocale(app()->getLocale())`.
A figure that arrives as a plain number has nothing to format it, and
`number_format($amount, 2)` prints no currency at all.

Use `money_text($amount)` for those. It reads identically to the cast, so a
summary card and the table under it agree. Pass a **major** amount: a sum of
minor units has to be divided by 100 first.

`number_format` still belongs on a count and on a percentage.
