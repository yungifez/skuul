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

Write it this way instead:

- Put `wire:key` on a plain element that wraps the component:
  `<span wire:key="row-{{ $id }}"><april:badge>...</april:badge></span>`
- For a form control that needs `@checked`, `@selected`, or `@disabled`, use a
  native `<input>` with Tailwind classes rather than the April component.
