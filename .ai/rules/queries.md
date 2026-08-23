---
paths:
  - 'app/Models/*.php'
  - 'app/Services/**'
  - 'app/Actions/**'
  - 'app/Http/Controllers/**'
---

# Eloquent queries

## An `or` inside a whereHas closure escapes the relation's join

`whereHas('audiences', fn ($q) => $q->where('user_id', $id)->orWhere('section_id', $s))`
builds this:

```sql
exists (select * from audiences
        where events.id = audiences.event_id and user_id = ? or section_id = ?)
```

`and` binds tighter than `or`, so the second condition stands alone. The
`exists` is then true whenever any row of the whole table matches, for any
parent record and any school. The filter silently passes everything.

Group the alternatives in their own closure:

```php
$query->whereHas('audiences', function (Builder $audience) use ($id, $s): void {
    $audience->where(function (Builder $named) use ($id, $s): void {
        $named->orWhere('user_id', $id)->orWhere('section_id', $s);
    });
});
```

`App\Services\Calendar\SchoolCalendar::limitToAudience()` carries the fixed
shape. The bug shipped unnoticed because the method had no caller yet, so
write the leak test first: one record that names the reader, one that names
somebody else, and assert the second stays out.
