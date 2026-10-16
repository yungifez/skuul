---
paths:
  - 'resources/views/vendor/april/**'
---

# April

## A published april view freezes the component
A file under resources/views/vendor/april/components wins over the package for
good, so it keeps every fix april-ui makes after the copy was taken. It says
nothing while it does so: the screen renders, it just renders the old
component. steps.blade.php sat here with no change of its own and blocked the
component's vertical orientation until it was deleted.

Publish a view only for the reason in .ai/rules/views.md: an april tag cannot
carry a blade directive, so input, native-select and textarea work out their own
refusal wiring. Open the file with
`{{-- Overrides april-ui so a refused field says so. See app/helpers.php. --}}`
and change nothing else.

tests/Unit/PublishedAprilViewTest.php strips that one change and compares what
is left with the packaged view. After `composer update yungifez/april-ui`, a
failure there means: copy the packaged view again and add the wiring back.
