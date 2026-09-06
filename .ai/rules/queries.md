---
paths:
  - 'app/Models/*.php'
  - 'app/Services/**'
  - 'app/Actions/**'
  - 'app/Http/Controllers/**'
---

# Eloquent queries

## A `whereHas` closure groups its own conditions

An earlier version of this rule said an `or` inside a `whereHas` closure
escapes the relation's join. That is wrong for the installed Laravel version.
`Builder::has()` runs the closure through `callScope()`, which puts every
condition the closure adds inside one group.

```php
AccountInvitation::query()->whereHas('user', fn ($user) => $user
    ->where('name', 'LIKE', $term)
    ->orWhere('email', 'LIKE', $term));
```

builds this:

```sql
select * from `account_invitations` where exists (
    select * from `users`
    where `account_invitations`.`user_id` = `users`.`id`
      and (`name` LIKE ? or `email` LIKE ?))
```

The `or` stays inside the group. Do not rewrite working code to add a second
closure. `App\Services\Calendar\SchoolCalendar::limitToAudience()` uses the
grouped shape and its comment repeats the old claim; the code is correct.

Confirm the SQL before you call a query a leak:

```
vendor/bin/sail artisan tinker --execute 'echo Model::query()->whereHas(...)->toSql();'
```

## An `or` outside a closure does escape

`callScope()` only wraps what a closure adds. An `or` chained onto the outer
query still sits beside the other conditions, and `and` binds tighter than
`or`. Group these yourself:

```php
$query->where(function (Builder $named) use ($id, $sectionId): void {
    $named->where('user_id', $id)->orWhere('section_id', $sectionId);
});
```

Write the leak test either way: one record that names the reader, one that
names somebody else, then assert the second stays out.

## A cascade can delete the record of where money went

`payment_allocations.fee_invoice_record_id` is `cascadeOnDelete`. Deleting a
fee invoice line therefore removes the allocations that say what a payment
settled, while the `student_payments` row keeps its full amount. The invoice
then reports less paid than the school holds.

Before deleting any row, check what cascades with it. A row that money points
at must refuse to go: `FeeInvoiceRecordService::deleteFeeInvoiceRecord()`
throws when `paid` is positive and tells the office to raise the waiver
instead. Hide the button on such a row as well, so no screen offers an action
the service will refuse.

## A soft delete leaves relations reading null

`Subject` soft deletes. The row stays, so no foreign key cascade fires, but
every `belongsTo` back to it returns null once it is gone. Screens read
`$courseOffering->subject->name` without a guard, so deleting a taught subject
took the course offerings index and every gradebook to a 500.

Before soft deleting a record, check what still points at it. There are two
right answers, and which one applies depends on what the record is.

**A catalog record: refuse the delete.** A subject, a fee or a fee category
exists to be picked from. Nothing is lost by keeping it, and a record still in
use has to stay readable. `SubjectService::deleteSubject()`,
`FeeService::deleteFee()` and `FeeCategoryService::deleteFeeCategory()` all
throw and say what to clear first. Hide the row action as well, so no screen
offers an action the server will refuse.

**A person: keep the name.** A school may legitimately remove a student, and
the invoices, report cards and receipts raised for them are real history that
has to keep naming them. Do not refuse the delete. Make the relation resolve
the removed person instead: `belongsTo(User::class)->withTrashed()`, which
`FeeInvoice::user()` and `StudentRecord::user()` both do. A `withTrashed()`
`belongsTo` adds no rows to any list, so it is safe on a live screen.

Do not reach for `?->` here. PHPStan reads the relation's declared type as
non-null and rejects a nullsafe read on the left of `??`. Fix the relation, not
the screens that read it.
