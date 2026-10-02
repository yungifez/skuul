# Known Bugs

## Remove exam slots, which the gradebook replaced
- Status: Fixed
- Area: Exams, gradebook
- Observed: Exam slots outlived the exam records they were built for. Nothing linked to them, but their pages still opened by URL. While working in one school, a user could open the slot list of another school's exam and see its name and an "Add exam slot" button. The exam's own page was that slot list. The gradebook had an exam slot column that no code ever filled.
- Impact: Another school's exam names could be read across campuses. Staff could find a half-working screen that did nothing in the gradebook. A closed term still showed the "Add" button.
- Reproduction: Work in school B. Open /dashboard/exams/{id}/manage/exam-slots for an exam of school A. The page opens.
- Resolution: Exam slots are removed: model, policy, controller, service, action, Livewire screens, views, routes, seeder and factory. The exam's slot-list page and the "View exam" action are gone; exams are read in their tables and changed on their edit page. A migration drops the exam_slots table and grade_items.exam_slot_id, and deletes the four "exam slot" permissions. A test checks that the routes, table, column and permissions are gone.

## Load the notice editor from the host that serves the page
- Status: Fixed
- Area: Notices, rich text editor
- Observed: The notice message box showed no editor. The console showed "editor is not defined". The page asked for the editor script at http://localhost/april-ui/editor.min.js, but the app runs on another host and port.
- Impact: Staff could not type a notice message, so they could not send notices. Any deploy where views compile under a different host breaks the same way.
- Reproduction: Compile views under one host (for example, a test run), then open Notices > Create on another host. The message box stays empty and the console shows errors.
- Resolution: The notice form now writes the editor script tag on each request, as a host-relative URL with the April version hash. The April directive built the URL once, when the view was compiled. A test checks the relative URL under a different root URL.

## A new campus's register asked for a section that did not exist
- Status: Fixed
- Area: Attendance register
- Observed: At a campus with no sections this school year, the register said "Choose a section above to mark who attended." The list above it was empty.
- Impact: Staff at a new campus hit a dead end with no hint of what was missing. The dashboard's "Take attendance" button led straight to it.
- Reproduction: Switch to a campus with no sections. Open the attendance register.
- Resolution: The register now says there are no sections this school year and that a register needs one. People who can add sections get a link to add one.

## Messages spoke of "academic levels" and "cycle sections"
- Status: Fixed
- Area: Classes, sections, admissions, audit log
- Observed: Screens call them classes and sections, or the words the school chose. About 25 messages used the internal names instead. For example, adding a class said "Academic level created. Add a cycle section to use it in a cycle."
- Impact: Staff met words they never see anywhere else. A school that renamed "section" to "stream" or "arm" still read "cycle section".
- Reproduction: Add a class. Read the message at the top of the screen.
- Resolution: The messages now use the school's own words for class, section, term and school year. The audit log says "Class created" and "Section created".

## Moves, admissions and holds took the server's date
- Status: Fixed
- Area: Enrollment, admissions, boarding, library, support plans
- Observed: A new admission, a change of class or status, a boarding place, a closed support plan, and a library hold all took their date from the server's clock in UTC.
- Impact: At a school behind UTC, a learner placed in a class in the evening got tomorrow's date. They were missing from that class's register for the rest of the day. A withdrawal or a hold was dated a day late in the same way.
- Reproduction: Give a school the America/Los_Angeles zone. At 8 p.m. local time, withdraw a learner. The withdrawal was dated the next day.
- Resolution: Each date now comes from the school that owns the record.

## Shared records arrived with their fields jumbled
- Status: Fixed
- Area: Record sharing between schools
- Observed: The school that took records in saw the fields in a strange order. For example, attendance read Late, Rate, Absent, Excused, Present, Recorded. Identity, health and table columns were jumbled the same way.
- Impact: Staff at the new school had to hunt for each figure. A rate read before the counts it comes from is easy to misread.
- Reproduction: Ask another school for Attendance. Approve and hand it over. Take it in. Read the Attendance section.
- Resolution: The database sorts the keys of a stored JSON object by length. The package now records the order its fields were built in, and the reader shows them in that order. Packages built before this change still open.

## The school asked for records could not see who they were for
- Status: Fixed
- Area: Record sharing between schools
- Observed: The request page showed the holding school only the learner's admission number. The list of received requests showed the name, but the page where the school approves or declines did not.
- Impact: The person deciding had to look up the number elsewhere before agreeing to share a learner's records. A misread number could lead to sharing the wrong learner's records.
- Reproduction: From school B, ask school A for a learner's records. As school A, open the request. Look at Learner.
- Resolution: The holding school now sees the learner's name above the admission number. The asking school still sees only the admission number, so a request reveals nothing about another school's roll.

## Notices, holds and permissions ended on the server's day
- Status: Fixed
- Area: Notices, library, data sharing, staff, boarding, timetables, scheduled jobs
- Observed: The earlier time zone fix moved bills, registers and forms to the school's clock. Other day checks still used the server's day in UTC. These checks included the notice end date, a library hold's last day, a data sharing request's end date, a leaver's last day, cover that already happened, and the default day for attendance, boarding rolls and assignments.
- Impact: At a school in Los Angeles, a notice ending on 1 October left the board at 5 p.m. local time. A library hold ended in the middle of the afternoon. Leavers lost access before their last day was over. At a school ahead of UTC, the same things ended late.
- Reproduction: Give a school the America/Los_Angeles zone. Publish a notice that ends today. At 5 p.m. local time, run `skuul:process-notices`. The notice expired.
- Resolution: Each check now uses the day at the school that owns the record. The scheduled jobs check each row against its own school's date, and do not miss a school whose day is ahead of the server's.

## Large sums ran off a phone screen
- Status: Fixed
- Area: Finance, invoice, payment and account screens
- Observed: At 390px wide, a large sum such as "NGN 12,345,678.00" was wider than its half of the summary grid. The figure ran past the screen edge.
- Impact: The office could not read the full sum owed on a phone. The page scrolled sideways.
- Reproduction: Open an invoice of more than NGN 1,000,000 on a 390px screen. Look at Owed.
- Resolution: Large sums now take a full row on phones and go back to the grid on wider screens. This applies to the invoice, take payment, student account and finance overview screens.

## A cheque or transfer could be recorded with no reference
- Status: Fixed
- Area: Finance, taking payments and refunds
- Observed: The office could record a cheque or bank transfer payment, or give money back by cheque, with the reference field empty.
- Impact: The bursar could not match the payment to the bank statement. A bounced cheque could not be traced to its record.
- Reproduction: Open an invoice. Take a payment. Choose Cheque. Leave Reference empty. Save. The payment was recorded.
- Resolution: The payment and refund forms now ask for the reference when the payment method needs one. Cash still needs no reference. The placeholder shows "Reference (optional)" only for methods that do not need one.

## Money taken on an older invoice put the account out of step with its bills

- Status: Fixed
- Area: Finance, upgrades, student accounts
- Observed: Invoices raised before the ledger existed have no ledger entry. A payment on one still went into the books and settled the invoice's lines. One learner's account showed NGN 85.00 owed while the invoices on the same page showed about NGN 20 million.
- Impact: After an upgrade, the account balance, the dashboard and the books understated what families owed, and a payment could cut another invoice's debt in the books.
- Reproduction: On a school upgraded from an older version, open a learner's account with old invoices. Take a payment on one of them. The account's owed figure does not match the invoices.
- Resolution: A payment or a use of credit now first puts the learner's older invoices at that campus in the books, charging what each still owes after payments the books never saw. The new `skuul:bring-invoices-into-books` command does this for every older invoice once, after an upgrade. `LegacyInvoiceBooksTest` covers both paths.

## A parent could read other families' accounts and the school's money

- Status: Fixed
- Area: Access control, student accounts, receipts, dashboard
- Observed: Parents and learners hold "read fee invoice" and "read student" so the portal can show their own bills. The office account page, the receipt page and the dashboard checked only those permissions. A parent with no children linked opened other learners' accounts and receipts by changing the number in the address, and saw the school's fees billed, collected and owed by lateness on the dashboard.
- Impact: Any family could read every other family's balance, invoices, payments and receipts, and the school's income.
- Reproduction: Sign in as a parent. Open /dashboard/fees/accounts/1 or /dashboard/fees/payments/1/receipt. Open the dashboard.
- Resolution: The office account page and the school-wide dashboard figures are now for staff only. A receipt opens for staff, or for the learner and their guardians through the portal rules. `PortalFinancePrivacyTest` covers parents, learners, guardians and staff.

## Dates followed the server's clock, not the school's

- Status: Fixed
- Area: Schools, dates across the app
- Observed: The app ran on UTC. Near midnight the header, new payments, invoices, registers and form defaults took the server's date, not the school's. A school east of UTC could not record a payment, expense or register for its own today until the server's date caught up, because "not in the future" checks used the server's date.
- Impact: Receipts and ledger entries carried the wrong day. Staff in Lagos were blocked for an hour each night, and schools further east for longer.
- Reproduction: Run a school in Lagos. At 00:30 local time, take a payment. It is dated the day before. Set the received date to today. The form says the date is in the future.
- Resolution: Each school now has a time zone, picked on the school details form and guessed from the browser when empty. Business dates, date limits, form defaults, printed times and the header use the school's date. Stored moments stay in UTC and show on the school's clock. Times staff type in, such as calendar events, are kept as typed. A school with no zone keeps the server's date. `SchoolTimeZoneTest` covers the setting, payments, the header and form limits.

## A payment taken back after a billing group ended split the debt across two campuses

- Status: Fixed
- Area: Finance, billing groups, campus moves
- Observed: A learner paid at campus A and moved to campus B while the two shared a billing group. The payment was carried to B. The campuses then left the group, and A took the payment back. A's books showed the family owing the money, and B still held the carried credit. A part payment left 40 owed at A and 60 at B, while the bill at B said 100.
- Impact: Both campuses could chase the family for one bill. The new campus kept credit for money the school never received.
- Reproduction: Put A and B in one billing group. Take 200 at A. Move the learner to B. Take A out of the group. Take the payment back at A.
- Resolution: Taking back a payment now carries back the part of it an earlier move took across, through the same campus accounts, even after the group has ended. Debt for bills that stayed at A stays at A. Money A billed or took on its own is not moved. `BillingGroupTest` covers credit, part payments, settled bills and later payments.

## Error pages show the framework's bare default
- Status: Fixed
- Area: Errors
- Observed: A 403, 404, 419 or 500 showed Laravel's plain page with no app look and no way back.
- Impact: A staff member who hit a closed page had to edit the address bar to get out. A 403 also hid the reason the app gave.
- Reproduction: As a school admin, open /dashboard/organizations/1.
- Resolution: Add app-styled pages for 401, 403, 404, 409, 419, 429, 500 and 503 on one shared layout. Each has a dashboard link and a back button. The 403 keeps a reason the app gave. ErrorPagesTest covers them.

## The waiver form is cut off on a phone

- Status: Fixed
- Area: Student accounts
- Observed: The waive or write-off form opened as a row inside the invoice table. On a phone the table scrolls sideways, so the form was wider than the screen. The right side of each field and the button were off screen. Rows with the ⋯ menu also pushed "Take payment" out of line with the other rows.
- Impact: Staff on a phone had to scroll the table sideways to fill in and submit the form, and could miss a field.
- Reproduction: Open a student account at 390px wide. Open ⋯ on a posted invoice and choose "Waive or write off".
- Resolution: The form now opens below the invoice table at full width and names the invoice it changes. Rows without the menu keep an empty space of the same size, so the buttons line up.

## A child who changed school opens the old school's calendar

- Status: Fixed
- Area: Parent portal, sidebar
- Observed: The sidebar's family calendar link opened the enrollment with the lowest id. For a child who left one school and joined another, that was the old school.
- Impact: The family read the old school's term dates and closures, and missed the new school's.
- Reproduction: Withdraw or transfer a learner, then enrol them at another school. Sign in as their guardian. The calendar link names the old enrollment.
- Resolution: The link now prefers an enrollment the child still attends. It falls back to the newest enrollment when none is attended.

## A withdrawn learner's family loses their records

- Status: Fixed
- Area: Parent portal
- Observed: The portal read active, suspended, graduated, and transferred enrollments. A withdrawn enrollment dropped out, so the family could no longer open its report cards, transcripts, or invoices.
- Impact: A family that took a child out could not show earlier results to a new school, or see and settle a debt the school still held. People also have a right to see their own records.
- Reproduction: Withdraw a learner with invoices and results. Open the portal as the learner or their guardian. The enrollment is gone.
- Resolution: The portal now reads withdrawn enrollments, like transferred ones, and each still shows its status. Archived enrollments stay out.
## A billed fee cannot be waived or written off

- Status: Fixed
- Area: Student accounts, `RelieveStudentFees`
- Observed: A posted invoice locks its lines, and a correcting invoice cannot be negative. `RelieveStudentFees` had no caller. No screen could take a scholarship or a bad debt off a fee after billing.
- Impact: The office could not record a late scholarship or give up on a debt. Families showed as owing money the school had already forgiven, or staff edited records outside the books.
- Reproduction: Raise an invoice for a learner. Try to waive part of one fee from the student account or the invoice screens. No control does it.
- Resolution: `RelieveStudentFees::relieveLine()` locks the learner and the line, and takes part of one fee off a posted invoice. It raises the line's waiver and posts the cost to scholarships or bad debt in the books of the campus that billed it. It refuses more than the line still owes or the campus books still hold, and records a `FeesRelieved` audit event. The student account screen offers "Waive or write off" in each unpaid invoice's ⋯ menu, to people who can refund student payments. A save sent again takes the fee off once.
## A move closes a support plan and nobody who runs it hears

- Status: Fixed
- Area: Support plans, campus moves, enrollment status
- Observed: A campus move or a closed enrollment cancelled the old campus's open support plans in silence. The person who looked after the plan found out only when they next opened it.
- Impact: Unfinished steps, such as a referral or a meeting with the family, were dropped with nobody to hand them over.
- Reproduction: Open a support plan and give it an owner. Move the learner to another campus, or withdraw them. The owner gets no message.
- Resolution: `ManageSupportPlan::closeAtSchool()` now mails the plan's owner, or its author when nobody owns it. The mail is sent after the commit. It names no learner, no need and no new campus, and only links to the plan. The person who made the change and anyone no longer at the campus are not told.
## A lost library book runs up an unlimited fine

- Status: Fixed
- Area: Library lending rules, `ReturnLoan`
- Observed: A late fine was days late times the daily fine, with no upper limit. A book kept for a term at 50 a day cost 4,500.
- Impact: A family could owe far more than the book is worth. Staff then had to reverse the charge by hand, and the fee account showed a debt nobody meant.
- Reproduction: Set a daily fine. Issue a book and take it back 90 days late. The fine is 90 times the daily fine.
- Resolution: The lending rules now have an optional "Most a late loan can cost". `LibraryLendingRules::fineForDaysLate()` stops at that cap, and `ReturnLoan` uses it. An empty cap keeps the old behaviour. The new migration `cap_library_fines` adds a nullable `fine_cap` column.
## Pages holding school records sent no browser protections
- Status: Fixed
- Area: HTTP, sessions
- Observed: Responses carried no frame, content-type or referrer headers, and no HSTS over HTTPS. Session data was stored in clear text, and the session cookie was not marked secure in production unless an environment value said so.
- Impact: Another site could frame a page and trick a click. Full addresses with record ids leaked in the Referer header. Anybody who could read the sessions table could read names and messages about people.
- Reproduction: Request any page and read the response headers.
- Resolution: Every response now carries X-Frame-Options, X-Content-Type-Options, Referrer-Policy and Permissions-Policy, plus HSTS over HTTPS. Sessions are encrypted by default, and the cookie is secure-only by default in production. Turning on encryption signs everyone out once.
## Scheduled jobs ran once per app server
- Status: Fixed
- Area: Scheduler
- Observed: No scheduled job used onOneServer. withoutOverlapping only stops a job overlapping itself on one server.
- Impact: With two app servers, reminders and notices went out twice and two backups ran at the same time.
- Reproduction: Run `schedule:run` on two servers that share a database and cache at 07:15. Both send the calendar reminders.
- Resolution: Every job except the scheduler heartbeat runs on one server. The heartbeat still runs on each server, so each one reports its own scheduler. The lock needs a cache shared by all servers, such as Redis or the database.
## Delegated organization scope opened every campus for editing
- Status: Fixed
- Area: Organizations, campuses
- Observed: The school policy asked only whether a person administers the organization. An administrator whose scope was cut to reading could still open and change every campus.
- Impact: Narrowing an administrator's organization permissions did not narrow what they could do to its campuses.
- Reproduction: Give an organization administrator only "read organization". They can still open a campus's edit page and save it.
- Resolution: Viewing a campus through organization scope needs "read organization". Editing one needs "manage organization campuses". The organization page links a campus to its edit page only for somebody who may edit it, and offers "Add campus" only to somebody who may add one.

## School forms offered choices and links the person could not use
- Status: Fixed
- Area: School create and edit
- Observed: The create form listed every organization the person belonged to, and saving to one without campus management returned 403. The edit page linked to the school list and the school page for people who could open neither. The school list offered Edit and Delete on campuses the person could not change. A replaced logo stayed on disk.
- Impact: People met forbidden pages in the middle of routine work, and old logos piled up in storage.
- Reproduction: As a campus admin with "update school" only, open the edit page and follow the "Schools" breadcrumb.
- Resolution: Both forms are Livewire forms now. The create form lists only organizations the person may add a campus to, and says so when there are none. Links and row actions appear only where the policy allows them. Replacing a logo deletes the old file.
## A teacher could approve the result they sent
- Status: Fixed
- Area: Gradebook
- Observed: A person holding both "publish result" and "approve result" could send a result and approve it at once.
- Impact: An official result could pass with one person's check only.
- Reproduction: As a teacher with both permissions, send a result for approval, then press Approve.
- Resolution: The person who sent a result cannot approve it. The mark sheet hides Approve on their own results, and the action refuses it.
## The last role manager could be recorded as leaving and keep access
- Status: Fixed
- Area: Staff records
- Observed: Recording a future leaving date for the only person who can manage roles succeeded. After that date the daily run could not end their membership, so it warned and left their access in place.
- Impact: A person who left kept access to the campus with no end date.
- Reproduction: At a campus with one role manager, record that they leave in three days. Four days later they can still sign in.
- Resolution: Recording a leaving for the last person who can manage roles is refused until somebody else can manage roles. The daily run keeps its warning as a backstop.
## Any organization could attach another organization's person by email
- Status: Fixed
- Area: Accounts, organization members
- Observed: Creating a teacher, parent, student or admin with an email already used in another organization attached that account. The organization members screen granted organization scope to any account on the platform and said whether an email existed.
- Impact: One organization could read the profile another organization recorded, without the person's consent, and could learn which emails use the platform.
- Reproduction: At a campus of organization A, create a parent with the email of a parent at organization B. The form succeeds and the parent joins campus A with B's profile.
- Resolution: An existing account is attached only when the organization already holds its record: a past or present place at one of its campuses, or past or present organization scope. Otherwise provisioning refuses the email, and the members screen answers as it does for an unknown email.
## Deleting a subject takes its lessons off published timetables
- Status: Fixed
- Area: Subjects
- Observed: A subject with no course offering could be deleted while a published or archived timetable showed it. The delete removed its lessons from every timetable.
- Impact: Classes lost lessons from the timetable they follow, with no revision and no audit record. Custom timetable items already refused this.
- Reproduction: Place a subject on a timetable and publish it. Delete the subject from the subject list.
- Resolution: A subject shown on a published or archived timetable cannot be deleted. Lessons on draft timetables still go with it.

## Deleting an archived timetable erases the cover given against it
- Status: Fixed
- Area: Timetables
- Observed: Only a published timetable was kept from deletion. An archived timetable, one that was in use before a revision replaced it, could be deleted.
- Impact: Cover records delete with their timetable, so the school lost who covered which lesson. Schools use that record for pay and absence reviews.
- Reproduction: Publish a timetable, record cover against it, and publish a revision so the first is archived. Delete the archived timetable.
- Resolution: Only a draft timetable can be deleted.

## Deleting an emptied school destroys its books
- Status: Fixed
- Area: School deletion
- Observed: A school could be deleted once it had no active members. People who left or transferred out do not count as active, so a closed campus could be deleted with its history.
- Impact: Schools are removed for good, and the ledger and payment tables delete their rows with the school. A closed campus lost every record of the money it took.
- Reproduction: End every membership at a school that recorded payments. Delete the school as a platform administrator.
- Resolution: A school that ever had a member, an enrollment or a ledger entry cannot be deleted. Only a school that was never used can go.

## A leaver can be booked to cover after their last day
- Status: Fixed
- Area: Timetable cover
- Observed: Cover checked leave, other lessons and other cover, but not a recorded leaving date. A teacher who leaves on Friday could be booked to cover next Monday.
- Impact: The leaver's duties end when their access ends, so the cover was deleted without notice and the class had nobody.
- Reproduction: Record that a teacher leaves on a date ahead. Book them to cover a lesson after that date.
- Resolution: Cover is refused for a date after the teacher's last day at the campus, with the date in the message. Their last day itself stays bookable.

## Lending a class set twice gives every learner a second copy
- Status: Fixed
- Area: Library class sets
- Observed: Lending a title to a class did not check who already had a copy. A second lend, by a colleague or a repeated click, gave each learner another copy of the same book.
- Impact: Copies went out twice to the same learners, so other classes found the shelf empty and the loans had to be undone by hand.
- Reproduction: Lend a title to a class that has enough copies for two rounds. Lend the same title to the same class again.
- Resolution: A class set passes over learners who already hold a copy of the title, read under the copy lock. When everybody has one, the lend is refused with a clear message.

## A members-only organization administrator can remove the owner
- Status: Fixed
- Area: Organization members
- Observed: A member trusted only with "manage members" could end the scope of a member with full authority. The permission editor refused the same change, but removal did not.
- Impact: A delegated member could remove everybody above them and be left running the organization.
- Reproduction: Give a member only "manage members". On the organization members screen, remove the member who has full authority.
- Resolution: Removal needs the actor to hold every permission the member holds. Anybody can still give up their own scope.

## A limited HR editor can end the access of the people above them
- Status: Fixed
- Area: Staff records
- Observed: Someone with only "update staff profile" could record that the principal or an administrator left, with a past date.
- Impact: Leaving ends campus access at once, so a lesser role could lock out the people above it.
- Reproduction: Give a user only "read staff profile" and "update staff profile". Open an administrator's employment record, set the state to Left with yesterday's date, and save.
- Resolution: Only somebody who holds at least as much at the campus can record that another person left. Other job details stay editable.

## A limited remover can delete a person who holds more
- Status: Fixed
- Area: People removal
- Observed: Someone with only "delete teacher", "delete admin" or "delete parent" could remove a person who also holds more power at the campus, such as a parent who is also an administrator.
- Impact: A lesser role could lock out the people above it and end their membership or account.
- Reproduction: Give a user only "read parent" and "delete parent". Delete a parent who also holds the admin role.
- Resolution: The delete policy refuses a person who holds more at the campus than the remover could give.

## A limited editor can take over an account that holds more
- Status: Fixed
- Area: People editing
- Observed: Someone with only "update teacher" or "update admin" could change the email of a person who holds more power at the campus.
- Impact: The new email receives the password reset, and with it every power of that person.
- Reproduction: Give a user only "update teacher". Edit a teacher who also holds the admin role. Change the email and save.
- Resolution: Only someone who holds at least as much at the campus can change a person's email. Other details stay editable.

## A custom role with account access can lock out the principal
- Status: Fixed
- Area: Account access
- Observed: Anybody holding "manage account access" could suspend or archive any campus member, whatever that member held.
- Impact: A front-office role given account access could suspend the principal or another admin, and lock them out of the school.
- Reproduction: Write a role with "manage account access" and "read student". Give it to a person. As that person, suspend an admin of the campus.
- Resolution: Account access now stops at people who hold staff power the manager could not give. What a learner or family reads through the portal does not count, so learners and families stay in reach.

## A role manager can empty a role of powers they do not hold
- Status: Fixed
- Area: Campus roles
- Observed: Changing a role checked only the permissions left in it. A person could take out permissions they did not hold themselves.
- Impact: A junior role manager could strip a bursar or registrar role, and so everybody holding it, of work they needed.
- Reproduction: Give a person "manage role" and "read student". Open a role that holds "read fee invoice". Save it without that permission.
- Resolution: Taking a permission out of a role now needs the same power as putting it in.

## A role manager can strip roles they could not give
- Status: Fixed
- Area: Campus roles
- Observed: Giving a role checked that the giver held everything in it. Taking a role away checked only "manage role".
- Impact: A junior role manager could take the admin role from the principal, or any powerful role from its holders, and lock them out of their work.
- Reproduction: Give a person only "read role" and "manage role". Open the Admin role and take it from another admin.
- Resolution: Taking a role away now needs the same power as giving it. The screen says the role holds more than the person does.

## A fee above about 21 million cannot be invoiced
- Status: Fixed
- Area: Fee invoices
- Observed: Invoice lines were kept in a plain integer column of minor units. A line of 25,000,000 overflowed it and the save failed with a server error.
- Impact: A school with large annual fees could not bill them, while the payment desk took up to 100,000,000.
- Reproduction: Create an invoice with one fee of 25,000,000.
- Resolution: Amount, waiver and fine are now big integers, like payments. Invoice line fields take at most 100,000,000, the same limit as payments.

## A budget in fractions of a cent is rounded without a word
- Status: Fixed
- Area: Budget planner
- Observed: The planner took 10.555 and the database stored 10.56.
- Impact: The saved plan differed from what the person typed, and nothing told them.
- Reproduction: Open the budget planner. Enter 10.555 for an account. Save.
- Resolution: The amount now takes at most two decimal places, like every other money field.

## Lifting a section's limit strands its admission queue
- Status: Fixed
- Area: Admissions waitlist
- Observed: After staff cleared a full section's capacity, Offer said "There is no open place or pending candidate" for every waiting family.
- Impact: Families stayed on the waitlist of a section with room for them, and no screen could move them on.
- Reproduction: Fill a section with capacity 1. Put a family on its waitlist. Clear the capacity. Press Offer.
- Resolution: A section with no limit is never full, so the next family is offered a place.

## The attendance register offers draft, archived and old sections
- Status: Fixed
- Area: Attendance register
- Observed: The section list on the register held every section the campus ever had, including drafts, archived ones, and sections of other years.
- Impact: Staff scrolled past sections with no class to call, and could open a register for the wrong year's section of the same name.
- Reproduction: Keep a draft section in the working year. Open Attendance register as a teacher.
- Resolution: The list now holds the running sections of the working year. A section opened from a link stays in the list.

## A payment to the old campus reads the new campus's balance
- Status: Fixed
- Area: Finance, student payments
- Observed: RecordStudentPayment booked money to the campus it was given, but worked out what was owed from the campus the learner now attends.
- Impact: Money paid to the old campus could go to held credit while the old debt stayed open, or settle a debt the old campus was not owed.
- Reproduction: Invoice a learner at campus A. Move them to campus B. Record a payment for campus A without saying how much it settles.
- Resolution: The amount owed is now read from the campus that takes the money. The payment desk already passed the amount, so no stored money was affected.

## Balance reports drop a learner who moved to another campus
- Status: Fixed
- Area: Finance reports
- Observed: Student balances and Student balances by age listed only learners now enrolled at the campus. A learner who moved on while owing the old campus vanished from its reports.
- Impact: The old campus could not see or chase a debt its own books still carried. The reports did not agree with the receivables account.
- Reproduction: Invoice a learner at campus A. Move them to campus B. Ask campus A for Student balances or the aging report.
- Resolution: Both reports now add learners enrolled elsewhere who still owe, or hold credit at, the campus. They show "Moved to" and the new campus. The aging report reads only the asking campus's invoices.

## Suspended staff still get calendar reminders
- Status: Fixed
- Area: Academic calendar reminders
- Observed: The daily reminder emailed every campus member who may close a term, including suspended accounts.
- Impact: A person the school locked out kept getting school mail about term dates.
- Reproduction: Suspend a staff member who may close academic periods. Run skuul:send-academic-calendar-reminders before a term starts.
- Resolution: Reminders go only to accounts that can still sign in.

## Parents are told to add student records
- Status: Fixed
- Area: Students list
- Observed: A parent with no linked child opened Students and saw "Add the first student record for this school."
- Impact: The parent got staff instructions they cannot follow and no hint of what to do.
- Reproduction: Sign in as a parent with no linked learner. Open /dashboard/students.
- Resolution: The empty list now tells a guardian to ask the school office to link their children.

## The upgrade hides learners who had no class

- Status: Fixed
- Area: Upgrade from the single-school release
- Observed: The upgrade read each enrollment's campus from its class. An enrollment with no class was left with no campus. Every staff list filters by campus, so nobody could see or place that learner.
- Impact: Learners who were between classes at upgrade time disappeared from the school.
- Reproduction: Upgrade an install with a learner who has no class. Open Students.
- Resolution: A new migration places such an enrollment at the learner's only campus. Unclear cases and admission numbers the campus already uses are left for staff.

## An enrollment with no campus crashes the family portal

- Status: Fixed
- Area: Family portal, upgraded data
- Observed: The upgrade gives a campus only to enrollments that had a class. For a learner with an enrollment that had no class, "Everything of mine" failed with "Attempt to read property "name" on null". The notification settings page failed the same way.
- Impact: The learner and their family could not open the portal. Without a campus, the portal also judged the record by whichever campus was current.
- Reproduction: Take an enrollment whose school is empty after the upgrade. Sign in as that learner and open "Everything of mine".
- Resolution: The portal now leaves out enrollments with no campus until staff place them.

## The new timetable calendar overflows a phone screen

- Status: Fixed
- Area: Timetables, create form
- Observed: At 390px the week and month grids of the create form pushed the page to 809px. The grid scrolled itself, but its minimum width stretched the page.
- Impact: The whole form scrolled sideways on a phone.
- Reproduction: Open "Create timetable" at 390px width.
- Resolution: The grid now sits in a scroll container, the same as the read-only timetable.

## The setup page links to areas the person cannot open

- Status: Fixed
- Area: School setup page
- Observed: The "Day to day" list and the staff access step linked every person to Staff access, Fees and other areas. A school administrator without the admin permission got "Forbidden".
- Impact: The setup page sent people to dead ends.
- Reproduction: Sign in as a person who manages school settings but cannot read administrators. Open School setup and select "Staff access".
- Resolution: The list shows only the areas the person can open. The staff access step goes to the teachers list when the person cannot open administrators.

## The dashboard's campus count opens a forbidden page

- Status: Fixed
- Area: Dashboard
- Observed: A campus administrator saw the organization's campus count as a link. The link opened the organization page, which answered "Forbidden".
- Impact: The dashboard sent campus staff to a dead end.
- Reproduction: Sign in as a campus administrator who is not an organization member. Select "Campuses" on the dashboard.
- Resolution: The count is a link only for people who may open the organization. Others see the count alone.

## A library copy waits for staff who left

- Status: Fixed
- Area: Library, staff leaving a campus
- Observed: Learners who left lost their reservations, but staff did not. A returned copy was held for a teacher who had left the campus.
- Impact: The next person in the queue waited until the hold ran out.
- Reproduction: A teacher reserves a title that is out. The teacher leaves the campus. The copy comes back.
- Resolution: Ending a campus membership now takes off the person's reservations there. A held copy goes to the next person.

## An old copy takes a published notice off the board

- Status: Fixed
- Area: Notices
- Observed: Scheduling and expiring read the status of the copy on the screen. A notice published since the page opened could be scheduled again, which took it off the board.
- Impact: A notice families were already reading disappeared until its new time.
- Reproduction: Open a draft notice in two tabs. Publish it in one. Schedule it in the other.
- Resolution: Scheduling and expiring now re-read the notice under a lock.

## One notice that cannot go out stops the notice scheduler

- Status: Fixed
- Area: Notices, scheduled publishing
- Observed: The scheduler stopped at the first notice that failed to publish. A scheduled revision whose original was replaced by another revision failed on every run.
- Impact: Every later scheduled notice stayed unpublished, and no notice expired, until somebody found the cause.
- Reproduction: Revise a published notice twice. Schedule the first revision. Publish the second. Schedule another notice. Run the scheduler.
- Resolution: The scheduler now returns a notice that cannot go out to draft, reports other errors, and carries on.

## A replaced timetable goes live again from an old copy

- Status: Fixed
- Area: Timetables
- Observed: Publishing checked the status of the copy on the screen. A revision replaced since the page opened was published again and archived the newer one. Two revisions published at the same moment could both go live, and two sections could publish lessons into one room.
- Impact: Learners and teachers saw an old week, or two weeks, for one section.
- Reproduction: Open a published timetable's page. Publish a revision elsewhere. Press publish on the old page.
- Resolution: Publishing now locks the period, re-reads the revision, and checks conflicts under that lock.

## A case or support plan moves again from an old copy

- Status: Fixed
- Area: Discipline cases, support plans
- Observed: A status change used the status loaded on the screen. After a colleague closed a case, a second person could still move it to review from their open page. The history then showed a step from a state the case was not in.
- Impact: Closed cases and cancelled plans reopened without a record of the reopening.
- Reproduction: Open a case in two tabs. Close it in one. Move it to review in the other.
- Resolution: Status changes now re-read the case or plan under a lock and decide from that state.

## An answer overwrites a request the family took back

- Status: Fixed
- Area: Family portal requests
- Observed: Staff answered from the status they loaded earlier. If the family took the request back meanwhile, the answer turned the cancelled request into an answered one.
- Impact: The family saw an answer to a request they had withdrawn. The record no longer showed that they withdrew it.
- Reproduction: Open a request in the staff inbox. Withdraw it in the portal. Answer it in the inbox.
- Resolution: Withdraw and every status change now re-read the request under a lock. A change to a closed request is refused.

## Lessons stay in a room taken out of use

- Status: Fixed
- Area: Facilities and timetables
- Observed: Taking a room out of use gave up its bookings but said nothing about its timetabled lessons. A draft timetable with a lesson in that room still published without a conflict.
- Impact: Classes were sent to a room the campus had closed.
- Reproduction: Place a lesson in Lab 2. Take Lab 2 out of use. Publish the timetable.
- Resolution: The conflict check now names each room out of use. Taking a room out of use says how many published lessons still use it.

## A course roster naming a learner who left cannot be saved

- Status: Fixed
- Area: Curriculum, course offering roster editor
- Observed: The roster editor chose every learner still on the offering, including one who had left or moved class. The form showed no box for that learner, so nobody could untick them. Every save failed with "Choose learners who attend this class this year."
- Impact: After one learner moved campus, staff could not change a named-learner roster at all.
- Reproduction: Name two learners on an offering. Move one to another campus. Open the roster editor and save.
- Resolution: The editor now keeps only the learners and sections it still offers. A save drops the learner who left.

## A class teacher who left stayed on the class
- Status: Fixed
- Area: Staff and academic structure
- Observed: When a class teacher left the campus, the class kept them as its class teacher.
- Impact: The setup page did not show that the class needed a teacher. Every later edit of the class was refused with "The class teacher does not work in this school".
- Reproduction: Make a teacher the class teacher of a section. Remove them from the school, or mark them as left. Rename the section.
- Resolution: `EndSchoolMembership` takes a leaver off the classes they lead in years still running, when their last day has passed. Classes in finished years keep the name as history.

## A family lost a notice attachment after a campus move
- Status: Fixed
- Area: Portal notices
- Observed: The portal listed a notice sent to a learner before a campus move, but its attachment download gave 403.
- Impact: Families saw a notice and could not open the permission slip or guide attached to it.
- Reproduction: Send a notice with an attachment to a learner. Move the learner to a sibling campus. Open the attachment from the guardian's portal.
- Resolution: `NoticeAttachmentController` follows the same rule as the portal notice list. It checks readable enrollments with the notices area open, not the campus that sent the notice.

## An entry could land in a financial period closed a moment earlier
- Status: Fixed
- Area: Finance ledger
- Observed: Posting checked that the period was open before its transaction, without a lock. A close committed in between let the entry into the closed period.
- Impact: Closed books could change after the accountant closed them, so closed totals would not match the reports sent out.
- Reproduction: Start a payment, and close its financial period at the same moment. Or post with a period loaded before somebody closed it.
- Resolution: `PostLedgerTransaction` re-reads the period under a shared lock inside its transaction and refuses a closed one. `ChangeFinancialPeriodStatus` locks the period, so a close waits for entries already being written.

## A blocked account still counted as the organization member manager
- Status: Fixed
- Area: Organization access
- Observed: A suspended or archived account still counted as somebody who can manage an organization's members. The last such manager could be suspended.
- Impact: An organization could be left with nobody able to sign in and grant access to its campuses.
- Reproduction: An organization has two member managers. Suspend both, or suspend one and revoke the other.
- Resolution: `Organization::hasAnotherMemberManager` skips suspended and archived accounts. `ChangeAccountStatus` refuses to block the last member manager of an organization.

## A blocked account still counted as the campus role manager
- Status: Fixed
- Area: Roles and account access
- Observed: A suspended or archived account still counted as somebody who can manage roles. The last role manager's account could be suspended.
- Impact: A campus could be left with no role manager who can sign in. Nobody at the campus could give out access again.
- Reproduction: A campus has two role managers. Suspend one, then suspend the other, or let the other leave.
- Resolution: `RoleAuthority::campusHasARoleManager` counts only accounts that are not suspended or archived. `ChangeAccountStatus` refuses to block the last role manager of any campus the person belongs to. The account screen shows the refusal.

## An unproved web address claim blocked its real owner
- Status: Fixed
- Area: Organization domains
- Observed: The first organization to claim a web address held it, even if it never proved it owned the address.
- Impact: One organization could claim another school's address and keep that school from ever using it.
- Reproduction: Organization A claims `school-b.example` and does not prove it. Organization B claims the same address and is told it is already claimed.
- Resolution: An address is unique only once proved. `AddSchoolDomain` refuses only a proved claim or a second claim by the same organization. `VerifySchoolDomain` refuses a second proof of the same address. A migration replaces the unique `host` index with a unique proved-host column.

## A section with learners could be archived for good
- Status: Fixed
- Area: Academic structure
- Observed: A section could be archived while learners were placed in it or its admission queue was open. An archived section never opens again.
- Impact: Learners were left in a section nobody could use. Offered places could no longer be accepted.
- Reproduction: Place a learner in an active section. Archive the section.
- Resolution: `ChangeAcademicCycleSectionStatus` refuses to archive a section that has enrolled learners or open admission queue entries.

## Library loans renewed for leavers and past a waiting queue
- Status: Fixed
- Area: Library
- Observed: A loan could be renewed for a borrower who had left the campus, and while other people waited for the title.
- Impact: A copy left the campus with a learner who moved or withdrew. The reservation queue waited through every renewal.
- Reproduction: Lend a copy to a learner. Withdraw the learner, or reserve the title for somebody else. Renew the loan.
- Resolution: `RenewLoan` refuses a borrower who no longer belongs to or attends the campus, and a title somebody else is waiting for.

## A whole-school notice reached every parent
- Status: Fixed
- Area: Notices
- Observed: A notice for "Everyone in this school: staff and learners who attend" went to every member of the school. Parents are members, so they received it even with "include guardians" off.
- Impact: Internal staff notices, such as meetings, inspections or disciplinary matters, reached families.
- Reproduction: Publish a notice with the whole-school audience and guardians off. A parent of the school gets it.
- Resolution: The whole-school audience is the people with a staff role or a current staff record, plus attending learners. Families get a notice only when it includes guardians. Named recipients still reach any member.

## A safeguarding case could be handed to a parent or a learner
- Status: Fixed
- Area: Discipline cases, support plans, programmes
- Observed: The assignee pickers listed every member who was not a learner, parents included. The server accepted any active member, learners included. Being assigned a restricted case opens it.
- Impact: A parent, possibly the parent of the child in the case, could read a restricted safeguarding case or a support plan, and be listed as the person running it.
- Reproduction: Open "Record a case" and choose a parent in the assignee list, or send a learner's id. The case saves with that person as the owner.
- Resolution: `schoolWorkers()` lists members who hold a staff role at the working school and are not enrolled learners. Cases, case actions, support plans, plan actions and programmes offer only them and refuse anyone else ("Choose somebody who works in this school.").

## A campus could change the email of an organization administrator
- Status: Fixed
- Area: User profiles
- Observed: The email of a person at two schools was protected. The email of a person at one campus who holds organization or platform authority was not.
- Impact: A campus administrator could change that email to their own, then reset the password and sign in with organization authority.
- Reproduction: Give a campus admin organization membership. As another admin of that campus, change their email in the admin form.
- Resolution: The profile update refuses an email change for a person who holds power beyond the working school ("This person has authority beyond this school, so only they can change their email.").

## Removing a person from one campus deleted an organization administrator's account
- Status: Fixed
- Area: Teachers, parents, students, user removal
- Observed: Removing a person from the school deleted the whole account whenever no other school membership was active. It ignored organization and platform authority.
- Impact: A campus administrator could delete the account of an organization administrator who taught at only that campus. The organization then lost its administrator.
- Reproduction: Give a teacher organization membership. Remove them from the teacher list of their only campus. The account is deleted.
- Resolution: `User::keepsAccountWhenRemovedHere()` keeps the account when another school still has the person or they hold power beyond this campus. Removal and the list messages both use it.

## One school could take over an account that holds power at another school
- Status: Fixed
- Area: Account access, user policy
- Observed: The account screen let any member with "manage account access" set the password, suspend, archive or re-invite anyone who belongs to the working school. It did not look at what the account can do elsewhere.
- Impact: An administrator at one school could set the password of a person who is a teacher or administrator at another school, or an organization or platform administrator, and sign in as them. They could also lock that person out everywhere. A family account shared with another organization's school exposed that school's children.
- Reproduction: Make a person a teacher at school A and a parent at school B. As an administrator of school B, set their password.
- Resolution: `User::holdsPowerBeyond()` detects system roles, organization membership, staff roles or permissions at other campuses, and membership at a school of another organization. The account-access policy refuses such accounts, and the account menu and password form are hidden for them. Learners and families of sibling campuses stay manageable.

## Setting a password lifted an account suspension
- Status: Fixed
- Area: Account access
- Observed: Setting a password from the account screen always made the account Active. A suspended or archived account was reactivated, with no status change recorded.
- Impact: One school could undo another school's suspension of a shared account by setting a password, and the audit showed only a password change.
- Reproduction: Suspend an account. Set a new password for it from the account screen. The account becomes Active.
- Resolution: Setting a password makes only an invited account Active. A suspended or archived account keeps its state, and it must be reactivated through the account status action.

## A parent removed from one school still read that school's children
- Status: Fixed
- Area: Parents, portal access
- Observed: Removing a parent who also belongs to another school only ended this school's membership. The portal follows guardian links, not memberships, so the parent still read this school's children.
- Impact: After a school removed a parent, for example after a custody order, that parent still saw the child's grades, invoices and attendance, and could send requests.
- Reproduction: Link a parent to a learner here and to a learner at a sibling campus. Remove the parent from this school. The portal still opens this school's learner for them.
- Resolution: Removing a parent first ends their guardian links to learners of this school. Each unlink is audited and cancels that parent's open requests. Links to children at other schools stay.

## A campus could lose the last person who can manage it
- Status: Fixed
- Area: School membership, campus roles
- Observed: Removing a person from the school, or a Left staff record, ended the membership with no check for role management. The guard used when roles change also counted people whose membership had ended, because their roles stay.
- Impact: A campus could be left with nobody who can give roles or permissions. Only a platform administrator could recover it.
- Reproduction: At a campus with two role managers, remove one from the school, then remove the other. Both succeed.
- Resolution: The role-manager check counts only people with an active membership. Ending a membership is refused when it would remove the last one ("Nobody at this campus could manage roles after that."). The daily leaver command skips such a person and warns.

## A leaver taken back could not sign in, and a learner could be taken back as staff
- Status: Fixed
- Area: Staff records
- Observed: After a Left record ended the campus membership, setting the record back to Active left the membership ended. The screen said the person worked here, but they could not open the campus. Setting a Left record back to Active also did not check whether the person had since enrolled as a learner.
- Impact: A rehired teacher was locked out with no sign of why. A former teacher who is now a learner could get their staff roles back.
- Reproduction: Mark a teacher as Left with yesterday's date, then set them back to Active.
- Resolution: Taking a leaver back grants the campus membership again, so their roles apply again. It is refused when the person is now an enrolled learner, and the error shows under the state field.

## Staff marked as Left kept their campus access
- Status: Fixed
- Area: Staff records, school membership
- Observed: Setting a staff record to Left ended teaching, boarding duty and cover. The school membership and roles stayed, so the person could still sign in to the campus and use every permission.
- Impact: A dismissed teacher or administrator kept access to learner records, marks and money after their last day.
- Reproduction: Open a staff record. Set the status to Left with yesterday as the leaving date. The person still belongs to the campus.
- Resolution: Saving a Left record whose last day has passed ends the membership. A new daily command, `skuul:end-leavers-access` (00:15), ends it the day after a leaving date that was set ahead. The person keeps access on their last day.

## A learner could be made staff through the teacher form, the staff import or a reopened enrollment
- Status: Fixed
- Area: Teachers, staff import, enrollment status
- Observed: The Add Teacher form and the staff import reuse an existing account by email. Neither checked for an open enrollment, so an enrolled learner got the teacher role or a staff record. A former learner hired as staff could also have their old enrollment reopened.
- Impact: One account held both a learner portal and staff powers, such as marking or reading other learners' records.
- Reproduction: Enter an enrolled learner's email in Add Teacher and save. Or hire a withdrawn learner as a teacher, then return their enrollment to attendance.
- Resolution: The teacher form and the staff import refuse an enrolled learner ("A learner cannot be made staff."). `ChangeEnrollmentStatus` refuses to reopen an enrollment when the person now works as staff at a school they belong to.

## A former guardian's requests stayed open in the school inbox
- Status: Fixed
- Area: Guardian links, portal requests
- Observed: Removing a guardian link left that person's open portal requests about the learner in the staff inbox. Nothing showed that the requester had lost access.
- Impact: In a custody change, staff could answer or hand over documents for a child to a person who is no longer a guardian.
- Reproduction: A guardian asks for a report card. The school removes the link. The request still waits in the inbox as Submitted.
- Resolution: `ChangeGuardianLink::unlink` cancels that guardian's open requests about the learner, with the note "The guardian link to this learner ended." Answered requests and requests about other children stay as they are.

## A member of staff could be admitted as a learner
- Status: Fixed
- Area: Admissions, student import, admission waitlist
- Observed: The admission form, the student import and waitlist acceptance only checked for an existing learner role and an enrollment elsewhere. A teacher or administrator of this or a sibling campus was admitted and given the learner role.
- Impact: One account held staff powers and a learner portal. The learner could keep marking, reading records or approving results.
- Reproduction: Give a person the teacher role at a sibling campus. Admit their email at this campus.
- Resolution: `User::worksAsStaff()` reads staff roles at every school where the person is still a member. Admission, import and waitlist acceptance refuse such a person. A teacher whose membership ended can be admitted.

## Open cases stayed with a member of staff who left
- Status: Fixed
- Area: Discipline, support plans, staff leavers
- Observed: When a person left a campus, their teaching, boarding duty and cover ended, but discipline cases, support plans and their steps stayed assigned to them.
- Impact: The work looked owned while nobody did it. A restricted case or confidential plan is read by its assignee, so after the leaver lost access it could be left without a reader among the people doing the work.
- Reproduction: Assign an open case and a support plan to a member of staff. End their membership, or record a leaving date that has passed. Open the case.
- Resolution: When a person's first day away has come, open cases, open plans and unfinished steps at that campus lose their assignee, so each shows it needs somebody. Finished work keeps the name.

## The old campus could still edit a learner who moved away
- Status: Fixed
- Area: Student profiles, campus moves
- Observed: A move keeps the learner's membership at the old campus, so its staff can read what the learner did there. The edit check asked only for that membership. Staff at the old campus could open the edit form and change the learner's name, address and other details.
- Impact: A campus the learner no longer attends could rewrite details the new campus relies on, without the new campus knowing.
- Reproduction: Move a learner to a sibling campus. At the old campus, open the learner's profile and choose Edit.
- Resolution: Editing a learner now needs their enrollment at the campus you work in. The old campus still reads the profile.

## Cover could be booked for a lesson on a holiday
- Status: Fixed
- Area: Timetable cover, calendar
- Observed: The cover screen took a lesson on a published holiday or closure. A teacher was booked to cover a class that would not meet.
- Impact: The booked teacher showed as busy that day, which blocked real cover elsewhere, and the cover record claimed a lesson that never happened.
- Reproduction: Publish a holiday for next Monday. Book cover for a Monday lesson on that day.
- Resolution: Cover is refused on a day the calendar shuts the campus or the lesson's home group, with the holiday named.

## A register could be taken on a day the school was shut
- Status: Fixed
- Area: Attendance, calendar
- Observed: The calendar marks holidays and closures, but the register never asked it. A teacher could open the register on a published holiday and mark learners absent.
- Impact: Absences on days the school was shut counted against the learner's attendance rate, in reports and in the family portal.
- Reproduction: Publish a holiday for yesterday. Open yesterday's register and mark a learner absent.
- Resolution: A published holiday or closure now shuts the register for that day, for the whole campus or only the home group it names. The register screen says the school was shut and why, instead of listing learners.

## A family could not ask the new campus what the old one still owed
- Status: Fixed
- Area: Portal requests, campus moves
- Observed: A request was a duplicate when the same person had the same subject open for the learner anywhere. After a move, a family still waiting on the old campus for a transcript could not ask the new campus for one: "You already asked for this. The school has not answered yet."
- Impact: The new campus never heard the request, and the family was told to wait for a school that may never answer.
- Reproduction: As a family, ask for "A copy of the result slip". Move the learner to a sibling campus. Ask again.
- Resolution: A duplicate is now the same subject open at the same campus.

## A campus move stayed waiting after a campus left the organization
- Status: Fixed
- Area: Organizations, campus moves
- Observed: A move request between two campuses stayed "Waiting for a decision" after one campus joined another organization. Approving it could only fail, because a move needs both campuses in one organization.
- Impact: The inbox of both campuses kept a request nobody could act on. The learner could not be asked for again, because one open request per learner is allowed.
- Reproduction: Ask to move a learner to a sibling campus. Assign the sibling campus to another organization. Open the move inbox.
- Resolution: Assigning a campus to another organization cancels the requests waiting between it and campuses of the old organization, with the reason in the decision note.

## A leaver was closed before their last day
- Status: Fixed
- Area: Enrollment status
- Observed: The profile took a later effective day for a change of state, such as a withdrawal from next Friday. The state changed at once. The history said it started on the later day.
- Impact: The learner lost their register, bed, programmes and support plans days early, while the record claimed they were still active.
- Reproduction: On a learner's profile, withdraw them with an effective day next week. Take their register today.
- Resolution: A change of state cannot start after the day it is made. The profile refuses a later day, so staff make the change on the day itself.

## A placement could start on a later day while the learner already sat there
- Status: Fixed
- Area: Enrollment placement, campus moves
- Observed: A campus move request can name a later day. Approving it moved the learner at once but wrote the placement from that later day. The profile forms also took a later day for a section change or a move.
- Impact: Until that day, the history said the learner sat in the old section, or at the old campus, while every screen showed the new one. The new campus's register refused the learner as belonging to the old campus.
- Reproduction: Ask to move a learner from two weeks ahead. Approve it today. Take the new campus's register today.
- Resolution: A placement cannot start after the day it is made. Approving an early request starts the move today. The profile forms refuse a later day.

## A campus could overwrite the old campus's attendance of a learner who moved
- Status: Fixed
- Area: Attendance, campus moves
- Observed: A move keeps the learner's one enrollment. The new campus could take a late register for a day the learner spent at the old campus. The record for that day was found by learner and day only, so the old campus's record was rewritten with the new campus, its section and the new answer. A late register also filed every day under the learner's section of today.
- Impact: The old campus lost its own attendance history without a trace, and a back-dated register showed learners who were not there yet. One refused learner could leave a register half saved.
- Reproduction: Move a learner between campuses. At the new campus, open the register for a day before the move and save it.
- Resolution: Each record is filed under the section the learner sat in on that day, from the placement history. A day at another campus is refused, and a record held by another campus is never changed. A late register leaves off learners who were at another campus that day. A whole register now saves in one transaction.

## An offer held its seat after the learner was placed another way
- Status: Fixed
- Area: Admissions waitlist, enrollment placement
- Observed: A candidate with an open offer could be taken back or placed in another section of the school, for example by a campus move. The offer stayed open. It held its seat, and accepting it could only fail.
- Impact: The section looked full. The next family on the waitlist got no offer until staff found and declined the stale one.
- Reproduction: Offer a seat to a former learner. Before they answer, take them back from their profile, or move them into another section. Try to offer the seat to the next family.
- Resolution: Placing a learner, or taking a leaver back, withdraws their open waitlist entries at that school. The seat goes to the next family.

## A returning learner could never take a waitlist offer
- Status: Fixed
- Area: Admissions waitlist, enrollment status
- Observed: A learner who had left the school (withdrawn or graduated) could join a full section's waitlist and receive an offer. Accepting the offer failed with "This candidate already has an enrollment in the school." Re-admitting them from the profile also failed while their old section was full.
- Impact: The seat stayed held by an offer nobody could take. The family was stuck until staff declined the offer by hand.
- Reproduction: Withdraw a learner. Add them to a full section's waitlist. Free a seat and offer it. Accept the offer.
- Resolution: Accepting now opens the learner's one enrollment at the school again, with its history and admission number, and places it in the offered section. Only the offered section's seats count. An enrollment that cannot reopen (transferred or archived) gets a clear refusal, and the offer stays open.

## Support plans stayed open for a learner who left or moved
- Status: Fixed
- Area: Wellbeing support plans
- Observed: A learner was withdrawn, or moved to another campus. Their support plans at the old campus stayed open. Staff there could still add steps and notes, and the plans stayed on the review list.
- Impact: A campus kept working on, and being reminded about, a learner it no longer teaches. The old campus could keep writing about a learner who now attends elsewhere.
- Reproduction: Open a support plan for a learner. Move the learner to another campus. Open the plan at the old campus and add a note.
- Resolution: Closing an enrollment, or moving the learner, now cancels the open plans at the campus they left, with the reason in the plan history. Finished plans are left as they were. The plans stay at that campus as history.

## A teacher marked as left kept teaching and covering
- Status: Fixed
- Area: Staff records, teaching and timetable cover
- Observed: Marking a staff record as Left withdrew the person's leave, but nothing else. Their subjects, boarding duty and booked cover ran on past their last day.
- Impact: Timetables and cover lists named a teacher who had left. Classes after the leaving date had nobody.
- Reproduction: Book a teacher as cover two weeks ahead. Mark their staff record Left with a leaving date next week.
- Resolution: Leaving now ends the person's subjects and boarding duty the day after their last day, and gives up cover booked after it. Their campus membership stays, because the same person may also be a guardian there. The same step runs when a membership ends.

## A deleted or departed teacher kept their lessons and cover
- Status: Fixed
- Area: Staff removal, teaching and timetable cover
- Observed: Deleting a teacher who worked at one campus only removed the account. Their teaching assignments kept running, and cover booked for them stayed. A teacher who left one of several campuses gave up their subjects there, but kept the cover booked there.
- Impact: Lessons and cover were assigned to somebody who no longer worked there. Nothing showed that the class needed a teacher.
- Reproduction: Book a teacher as cover next Monday. Delete the teacher from the teacher list. Open the cover list.
- Resolution: Deleting a person now ends their membership first, which ends their teaching and boarding duty. Ending a membership also gives up cover booked from today on at that campus. Cover already given stays in the record. A notice test that shared a section with seeded learners now uses its own section.

## A lesson could be published into a hall somebody had booked
- Status: Fixed
- Area: Timetable and facility bookings
- Observed: A hall was booked for Sports day next Monday morning. A timetable was then published with a Monday morning lesson in that hall. Publishing succeeded.
- Impact: The class and the event both expected the hall. The booking screen refuses the same clash in the other order.
- Reproduction: Book the hall for next Monday 09:30 to 11:00. Put a Monday 09:00 lesson in the hall and publish the timetable.
- Resolution: Publishing now lists every booking still ahead in the timetable's period that a lesson in that place would cross. Given-up bookings and bookings on other days do not count.

## Shared records could be taken in but never read
- Status: Fixed
- Area: Data sharing between schools
- Observed: A school asked another for a learner's records. The other school approved and handed them over, and the asking school took them in. No screen showed what was sent.
- Impact: The whole sharing flow ended with nothing to read. Schools would fall back to email or paper for health, results and guardian details.
- Reproduction: Ask for a learner's health record. Approve and hand it over at the other school. Take it in, then look for the allergies.
- Resolution: Once the asking school takes the package in, the request shows each category that was sent. Single values show as a list, and lists show as tables. Internal ids are left out, and missing values show as "—". Before the package is taken in, and at the school that sent it, nothing is shown.

## A learner who moved away could be made staff at the campus they left
- Status: Fixed
- Area: Staff records and staff pickers
- Observed: A learner who moved keeps their membership at the old campus. The staff pickers there left out only people enrolled at that campus, so the moved learner was listed as staff. The staff record form saved an employment record for them.
- Impact: A learner could be made a case handler, a club leader or a member of staff at the campus they left.
- Reproduction: Move a learner to another campus. At the old campus, open New staff record. The learner is listed and can be saved.
- Resolution: The staff pickers leave out anybody still attending any campus. Writing an employment record for a person still enrolled anywhere is refused.

## A learner could be given a staff role
- Status: Fixed
- Area: Campus roles
- Observed: The role screen offered every member of the campus, learners included. A learner could be given a role such as Registrar and gain its permissions. A learner who moved to another campus kept their membership at the old one, so the old campus could still give them a role.
- Impact: A learner could read or change other learners' records, or money, with staff permissions.
- Reproduction: Open a campus role. Pick a learner from the list and give the role.
- Resolution: A person enrolled as a learner at any campus cannot be given a campus role, and the picker leaves them out. A former learner, for example a graduate, can still join the staff.

## Leave was approved while the teacher still had cover booked
- Status: Fixed
- Area: Staff leave and timetable cover
- Observed: A teacher was booked to cover a lesson. Their leave for that day was then approved. The cover stayed booked to a teacher who would not come.
- Impact: The class was left without a teacher, and the cover list said it was covered. Leave at another campus had the same effect.
- Reproduction: Book a teacher as cover next Monday. Ask for their leave on that Monday at any campus. Approve it.
- Resolution: Approval is refused while the teacher still covers a lesson on or after today in the leave days. The message gives the number of lessons and asks to withdraw that cover first.

## A learner who left could still join a group
- Status: Fixed
- Area: Groups, programmes, boarding and support plans
- Observed: The group screen offered withdrawn, transferred and graduated learners. Adding one gave them a new group place. The programme, dormitory and support plan pickers also listed learners who had left.
- Impact: Former learners came back into class groups and watchlists. Staff chose from lists padded with people who no longer attend.
- Reproduction: Withdraw a learner. Open a group. Pick the learner and add them.
- Resolution: A group now takes only a learner who still attends, suspended ones included. The four pickers list only attending learners.

## The sidebar listed Subjects twice
- Status: Fixed
- Area: Sidebar and command palette
- Observed: A school that calls its courses subjects saw two sidebar links named "Subjects". One opened the subject list. The other opened the subjects being taught this year.
- Impact: Staff could not tell the two pages apart. The command palette showed the same name twice.
- Reproduction: Use the home sections preset. Open the sidebar as an admin. Look under Academics.
- Resolution: The course offerings link now reads "<course term> being taught". A test checks that no two sidebar links share a name.

## A seat offered to a waiting family could be filled another way

- Status: Fixed
- Area: Admissions waitlist, placement
- Observed: Direct placement, re-admission and the section form counted only enrolled learners. Staff could place a new learner into a seat already offered to a waitlisted family.
- Impact: The family accepted the offer and found the section full.
- Reproduction: Offer the last seat of a section to a waitlisted family. Before they answer, place another learner in that section. The placement succeeds.
- Resolution: A new SectionSeats service counts enrolled learners and open offers. Placement, re-admission, the waitlist and the section's capacity check all use it. Accepting an offer closes it first, so the family fills the seat that was held for them.

## A deleted student kept their seat and their place on registers

- Status: Fixed
- Area: Students, deletion
- Observed: Deleting a student removed the account, or ended this school's access, but left the enrollment active.
- Impact: The learner still held a seat in the section, still showed on attendance registers as "Unnamed learner", and could still be billed. A waitlisted family could not get that seat.
- Reproduction: Put a learner in a section of 1. Delete the learner from the students list. The section is still full.
- Resolution: Deleting a student now withdraws their enrollment at this school first, with the reason "Removed from the school". The withdrawal releases the bed, reservations and groups, as any withdrawal does. Invoices and results stay.

## A teacher on leave could be booked to cover a lesson

- Status: Fixed
- Area: Timetable cover, staff leave
- Observed: Recording cover checked the teacher's lessons and other cover, but not their leave. A teacher off sick, or on leave at another campus, could be named as the cover.
- Impact: The lesson went uncovered while the timetable said a teacher was there.
- Reproduction: Record sick leave for a teacher on Monday. Record cover for a Monday lesson and choose that teacher. The cover is saved.
- Resolution: Cover now refuses a teacher whose leave at any campus touches that day, and says they are on leave.

## Carried bills were missing from the new campus's invoice list

- Status: Fixed
- Area: Finance, campus moves inside a billing group
- Observed: Bills carried to a new campus kept the old campus's financial period. The new campus lists invoices by its own period, so the carried bills showed in neither campus's list or outstanding total.
- Impact: The new campus under-reported what families owed it.
- Reproduction: Put two campuses in one billing group. Bill a learner at the first. Move them to the second. Open Fee invoices at the second campus. The bill is not listed.
- Resolution: A carried bill now joins the new campus's open financial period, the same period as the carry entry in its books.

## An old campus's open screen could change a learner who moved away

- Status: Fixed
- Area: Student profile, health records, campus moves
- Observed: A student profile or health form opened before a campus move still acted after it. The old campus could withdraw, graduate or re-place the learner at their new campus, and could write their health record.
- Impact: One campus changed another campus's enrollment and medical data. The learner could be closed out of the school they now attend.
- Reproduction: Open a learner's profile at campus A. Move the learner to campus B. On the open screen, set the status to Withdrawn and save. The learner is withdrawn at campus B.
- Resolution: The profile's status, placement and campus actions now check that the enrollment still belongs to the working campus. If it does not, the screen names the campus the learner now attends. The health form refuses to save for a learner who has left the campus.

## One free seat was offered to two families

- Status: Fixed
- Area: Admissions waitlist
- Observed: "Offer next" counted only enrolled learners. Pressed twice for one free seat, it offered the seat to two families. A new candidate was also refused a waitlist place because the offered seat looked free.
- Impact: Two families were told they had a place. The second to accept found the section full.
- Reproduction: Fill a section of 1. Waitlist two candidates. Graduate the learner. Press "Offer next" twice. Both candidates hold an offer.
- Resolution: An open offer now holds its seat. "Offer next" and joining the waitlist count offered seats with enrolled learners.

## A bounced payment left spendable credit at the new campus

- Status: Fixed
- Area: Finance, campus moves inside a billing group
- Observed: A learner paid 200 at one campus and moved to another campus of the same billing group. The 200 of credit was carried. The first campus then took the payment back because the cheque bounced. The new campus still held 200 of credit.
- Impact: The family could spend money the school never received. A payment taken back after a move also left the debt at the old campus while its bill sat at the new one, and money taken at the old campus after a move stayed there as credit the new campus could not use.
- Reproduction: Put two campuses in one billing group. Take a 200 payment at the first. Move the learner to the second. Take the payment back at the first. The second campus shows 200 of credit.
- Resolution: A payment taken or taken back at a campus the learner left now carries the change on to the campus they attend, when the two keep one purse. A carry can now move a balance below nothing. Campuses with separate books keep their own money, as before.

## A carried debt could not be paid at the new campus

- Status: Fixed
- Area: Finance, campus moves inside a billing group
- Observed: A move inside a billing group carried the debt to the new campus's books, but the bills stayed at the old campus. A payment at the new campus found no bill to settle and became credit. The debt stayed open.
- Impact: The family paid but still showed as owing at the new campus. A payment at the old campus cleared a debt its books no longer held, so its receivable went below nothing.
- Reproduction: Put two campuses in one billing group. Bill a learner 100 at the first. Move them to the second. Take 100 at the second campus. The bill shows 0 paid.
- Resolution: The carry now moves the learner's posted, still-owed bills to the new campus with the debt. Campuses with separate books keep their bills, as before.

## A refund sent twice paid out twice

- Status: Fixed
- Area: Finance, student account refunds
- Observed: A refund request sent again, from a retry or a stale tab, recorded a second refund while the learner still held enough credit.
- Impact: The school paid the family twice and the books showed less credit than the family was owed.
- Reproduction: Hold 100 in credit for a learner. Refund 20. Send the same refund request again. Two refunds of 20 are recorded.
- Resolution: The account screen names each refund with a locked key. A request with a key already paid out records nothing. The key changes after each refund, so a real second refund still goes through.

## A payment taken twice from the invoice screen

- Status: Fixed
- Area: Finance, taking payment at the counter
- Observed: Pressing "Take payment" twice, or a browser retry after a lost answer, recorded the same cash payment twice.
- Impact: The ledger showed money the school never received. The family looked paid ahead, and the cash count did not match.
- Reproduction: Open an invoice's payment screen. Enter 40 in cash. Press save twice before the page leaves.
- Resolution: The screen now names each payment with a locked key. A second save with the same key goes to the invoice and records nothing. A refused payment frees the key, so the cashier can correct it and save again.

## Some checkboxes lost their size, and the phone check flagged error lines
- Status: Fixed
- Area: Views, form controls
- Observed: Seventeen checkboxes asked for size-5, which the app-wide checkbox paint overrides to 1rem. The phone keyboard check read the error line under each phone field as a field without type="tel".
- Impact: The views said one size and showed another. Two view checks failed on every run, which hid new failures.
- Reproduction: Run tests/Unit/NativeChoiceControlPaintTest.php and tests/Unit/PhoneFieldKeyboardTest.php.
- Resolution: The checkboxes now ask for size-4, the size they show. The phone check reads only input lines, including Livewire-bound ones.

## A learner's passes at their old campus did not count toward graduation
- Status: Fixed
- Area: Graduation plans, campus moves
- Observed: A graduation requirement matched results by the subject's own record. Each campus keeps its own subject list, so the old campus's Mathematics was a different subject.
- Impact: After a campus move, the new campus showed "no result" for subjects the learner had already passed. The learner could not be shown as ready to graduate.
- Reproduction: Publish a Mathematics result at campus A. Move the learner to campus B. Open B's graduation plan that asks for B's Mathematics.
- Resolution: A requirement also counts the learner's results in a subject of the same name. Only the learner's own enrollment is read, so marks from another organization never count.

## A CSV cell on two lines broke the import, and Windows files lost accents
- Status: Fixed
- Area: Imports, CSV reading
- Observed: The reader split the file into lines before it read the cells. A quoted cell with a line break became two broken rows. A CSV saved in the Windows character set was read as UTF-8.
- Impact: An address typed on two lines in Excel shifted every later column of that learner, or split them into two bad rows. Accented names were garbled, or the database refused the row.
- Reproduction: Import a CSV with an address cell that holds a line break. Or save a CSV with a name such as Renée from older Excel as plain CSV, and import it.
- Resolution: The reader now reads whole CSV records, so quoted line breaks stay in their cell. A file that is not valid UTF-8 is read as Windows-1252.

## A suspended person kept working in a tab they had open
- Status: Fixed
- Area: Accounts, Livewire screens
- Observed: The active-account check ran on page loads only. Screen actions go to Livewire's own address, which skipped that check. Suspending an account did not end its sessions.
- Impact: A suspended bursar with an open payment screen could still take payments, refunds and other actions until the session ran out.
- Reproduction: Open a form. In another browser, suspend that account. Press save on the open form.
- Resolution: The active-account check now runs on every Livewire request. The person is signed out on the next action and it is refused.

## An invitation accepted during a suspension opened the account again
- Status: Fixed
- Area: Accounts, invitations
- Observed: Accepting an invitation always made the account Active. A suspension saved the new status first and revoked open invitations after, outside a transaction.
- Impact: A person accepting at the moment an administrator suspended them ended up with an active account and a password.
- Reproduction: Send an invitation. Suspend the account while the person submits the invitation form.
- Resolution: A status change and an acceptance both lock the person's row. Acceptance refuses an account that is suspended or archived.

## A learner who left could still borrow from the old campus library
- Status: Fixed
- Area: Library, lending and reservations
- Observed: The lending desk and the queue checked only that the borrower was a member of the campus. A learner keeps that membership after a campus move or a withdrawal.
- Impact: A book could go home with a learner who no longer attends. The move cancelled their queue places, but the desk let them queue again. They also got the larger staff limit.
- Reproduction: Withdraw a learner, or move them to another campus. At the old campus, lend them a copy or put them in a title's queue.
- Resolution: Lending and reserving refuse a learner with no open enrollment at the campus. People with a staff role are not affected.

## Two managers could remove each other and lock the organization out
- Status: Fixed
- Area: Organization, members
- Observed: Removing a manager, or taking away their right to manage members, checked that someone else could still manage. The check ran without a lock.
- Impact: Two managers who removed each other at the same moment each saw the other as the backup. The organization was left with nobody who could manage its members.
- Reproduction: Give two people the right to manage members. In two browsers, have each remove the other at the same moment.
- Resolution: Both actions lock the organization row before they count its managers. The second change waits and then sees the first.
- Note: A single-connection test cannot run the two requests at once. The existing tests cover the rule itself.

## A teacher kept changing marks after their assignment ended
- Status: Fixed
- Area: Gradebook, access
- Observed: The gradebook let any teacher who was ever assigned to an offering record marks and send results. It did not check whether the assignment had ended.
- Impact: A teacher who left a campus and came back could edit the marks of the teacher who took the subject over.
- Reproduction: Assign a teacher to an offering. End the assignment. Open the offering's mark sheet as that teacher.
- Resolution: Recording marks and sending results need an assignment that has not ended. A teacher can still read the marks of any offering they taught.

## A transfer sent twice opened two enrollments
- Status: Fixed
- Area: Enrollment, transfers
- Observed: Transferring an enrollment that was already transferred opened another enrollment at the destination. It could also open one at a third school.
- Impact: A retry or a double click left the learner enrolled twice at the new school, or at two schools at once.
- Reproduction: Transfer an enrollment to another organization's school. Transfer the same enrollment again.
- Resolution: The transfer locks the old enrollment first. A repeat to the same school returns the enrollment it already opened. A repeat to another school is refused, and so is a transfer to a school where the learner is already enrolled.

## Taking back a leaver could push a section past its size
- Status: Fixed
- Area: Enrollment, status changes
- Observed: Returning a withdrawn or graduated learner to attendance put them back in their old section without counting its seats.
- Impact: A full section went over the size the school set. Waitlisted candidates lost the seat they were promised.
- Reproduction: Set a section's size to 1. Withdraw its learner, then place another one. Return the first learner to attendance.
- Resolution: A closed enrollment reopens only when its section still has a free seat. The section row is locked while seats are counted.

## A graduate or leaver could be reopened while enrolled at another campus
- Status: Fixed
- Area: Enrollment, status changes
- Observed: Undoing a graduation, or taking back a withdrawn learner, reopened the old enrollment. It did not check whether the learner had started again somewhere else.
- Impact: One learner was enrolled at two campuses at once. Both campuses counted, placed and billed them.
- Reproduction: Withdraw a learner at campus A. Enroll them at campus B. At campus A, return them to attendance.
- Resolution: A closed enrollment reopens only when the learner has no other open enrollment. Otherwise the action names the school they now attend.

## Resetting an old promotion pulled learners back from where they had moved
- Status: Fixed
- Area: Students, promotions
- Observed: A reset put every learner of the promotion back in the old section. It did not check where each learner was now. A graduated learner made the reset stop part-way.
- Impact: Learners moved to a later section were pulled back without a word. A stopped reset left some learners moved and some not, with the promotion still listed.
- Reproduction: Promote two learners. Move one to another section and graduate the other. Reset the promotion.
- Resolution: Only learners still in the section they were promoted to go back. Closed enrollments are left alone. The reset runs in one transaction.

## Two tabs could spend the same credit or take a payment back twice
- Status: Fixed
- Area: Finance, payments
- Observed: A refund, a credit use, and a payment reversal each checked the learner's money, then wrote later without a lock. A payment worked out its split before its lock.
- Impact: Two cashiers, or one person in two tabs, could refund the same credit twice in cash, reverse one payment twice, or settle one fee twice.
- Reproduction: Open the same learner in two tabs. Refund the full credit in both at the same moment.
- Resolution: Each of these actions now locks the learner's record, or the payment, first. It checks and writes inside that lock. A refund no longer keeps its bank reference from a later payment.

## The same payment could be recorded twice under one reference
- Status: Fixed
- Area: Finance, payments
- Observed: A cashier could record a bank transfer, then record it again with the same reference. A double click or a second tab did the same.
- Impact: The learner's account showed money the school never received twice. Fees looked paid, and credit could be refunded in cash.
- Reproduction: Take a payment with reference TRF-1 for a learner. Take another with reference trf-1 for the same learner.
- Resolution: A payment is refused when the learner already has a standing payment with that reference, in any letter case. The learner's record is locked while the payment is written. The same reference can still pay for a sibling. A reversed payment frees its reference.

## A case recorded late was filed under the wrong term

- Status: Fixed
- Area: Discipline
- Observed: A case was always filed under the term that is open now, even when it happened in an earlier term.
- Impact: Term-by-term discipline counts moved cases into the wrong term, so a learner's record for a term was wrong.
- Reproduction: In term two, record a case that happened during term one. It is filed under term two.
- Resolution: A case is filed under the term that covers the day it happened. It falls back to the open term only when no term covers that day.

## A late register slipped into a closed term

- Status: Fixed
- Area: Attendance
- Observed: A register taken for an earlier day was filed under the term that is open now. Only that open term was checked for closure.
- Impact: Once a new term opened, staff could change attendance in a term that was already closed and reported. The record counted towards the wrong term's totals.
- Reproduction: Close term one and open term two. Take the register for a day in term one.
- Resolution: A register is filed under the term that covers its day at the learner's campus. A day in a closed term, or a term of a closed year, is refused.

## A CSV export could run a formula typed into a name or note

- Status: Fixed
- Area: Reports, syllabus coverage export
- Observed: CSV exports wrote cells exactly as stored. A value starting with =, +, -, @, a tab or a return opens as a formula in a spreadsheet program.
- Impact: Anybody who can type a name, a note or a title could make a bursar's or an administrator's spreadsheet run a formula, for example a link that sends data out.
- Reproduction: Name a learner `=HYPERLINK("http://example.test","Ada")`. Export a report that lists them as CSV and open it in a spreadsheet.
- Resolution: `CsvFormat` puts an apostrophe before such text cells, so they show as typed. Plain numbers, including negative balances, are left alone. XLSX already writes plain text cells.

## A failed report showed the fault's inner details

- Status: Fixed
- Area: Reports
- Observed: When building a report failed, the report desk showed the raw exception message on the run.
- Impact: Staff could read SQL text, file paths or class names meant for developers.
- Reproduction: Make a report build fail with an unexpected error. The run on the report desk shows the raw message.
- Resolution: Refusals the application makes still show their message. Any other fault shows a plain sentence. Its details stay in the failed job.

## A failed import row showed the fault's inner details

- Status: Fixed
- Area: Imports
- Observed: The import runner wrote the message of any exception onto a failed row, including database and framework errors.
- Impact: People who import could read SQL text and table names. The fault itself was never reported, so nobody saw it in the logs.
- Reproduction: Make a row fail with an unexpected error, for example a database constraint. The row shows the raw message.
- Resolution: Refusals the application makes, and validation messages, are still shown. Any other fault is reported, and the row says it could not be written.

## A student import showed a raw database error for a taken admission number

- Status: Fixed
- Area: Imports
- Observed: A row whose admission number the school already used failed on the database unique index. The row showed the SQL error text. A row with no admission number created a learner without one, unlike the admission form.
- Impact: The person importing could not tell what to fix, and the error exposed database details. Learners imported without a number had no admission number on report cards or in searches.
- Reproduction: Import a student with an admission number that another learner at the school has. Or import one with the number left blank.
- Resolution: The row is refused with "Admission number … is already used in this school." A blank number is generated the same way as on the admission form.

## A campus move crashed when the admission number was taken there

- Status: Fixed
- Area: Enrollment, campus moves
- Observed: A move keeps the learner's admission number. Admission numbers are unique per campus, so a number already used at the destination hit the database unique index.
- Impact: Approving the move failed with a server error instead of saying what to fix. Hand-entered and imported numbers make this likely between campuses.
- Reproduction: Give a learner admission number ADM/001. Give a learner at a sibling campus the same number. Move the first learner to the sibling campus.
- Resolution: The move is refused first with a message that names the number and the campus.

## A warden removed from a campus stayed on duty

- Status: Fixed
- Area: Boarding, school membership
- Observed: Ending a membership left the person's boarding house supervision open at that campus.
- Impact: The house listed a warden who could no longer open the school. Nobody saw that the house had no supervisor.
- Reproduction: Make a staff member warden of a house. End their membership at that campus. The duty is still running.
- Resolution: Ending a membership ends the duties at that campus that have already begun.

## A teacher removed from a campus still led its classes

- Status: Fixed
- Area: Teaching assignments, school membership
- Observed: Ending a membership, now also what "delete" does for a person shared with another campus, left their teaching assignments at that campus running. An assignment that already had a later end date could not be ended sooner.
- Impact: Offerings kept a lead teacher who could no longer open the school. Nobody saw that the class needed a new teacher, and mark sheets had nobody to fill them.
- Reproduction: Assign a teacher to an offering. End their membership at that campus. The assignment still runs.
- Resolution: Ending a membership ends the person's teaching at that campus today. `AssignTeacher::end` now brings a later end date forward.

## Work could be handed to somebody who had left the school

- Status: Fixed
- Area: Discipline, support plans, admissions, calendar, staff profiles
- Observed: The "belongs to this school" rule checked that a membership row existed, not that it was still active. Several forms also wrote the rule out by hand.
- Impact: A case, a support plan action, or a waitlist place could be assigned to a person whose membership had ended. They could not open the school to act on it, so the work sat unowned.
- Reproduction: End a teacher's membership. Record a case and pass their user id as the assignee. It saves.
- Resolution: `ValidatesSchoolMembership::memberOfWorkingSchool()` now requires an active membership. The five hand-written copies use it. The staff picker those forms show lists only active members.

## A seat was offered to a candidate the school had already enrolled

- Status: Fixed
- Area: Admissions waitlist
- Observed: The next offer went to the top waiting entry even when the school had meanwhile enrolled that candidate another way, for example straight into another section.
- Impact: The freed seat was held for a family who already had a place. The next family waited until somebody noticed.
- Reproduction: Waitlist a candidate for a full section. Enrol them directly in another section. Free a seat and offer it. The offer goes to them.
- Resolution: The offer skips candidates enrolled at the school, withdraws their entry with a reason, and offers the seat to the next one. A candidate enrolled at another school keeps waiting, because they join through a move or a transfer.

## A night away stayed open after the learner left the house

- Status: Fixed
- Area: Boarding
- Observed: Ending a boarding place, by hand, on a campus move, or when the enrollment closed, left the learner's overnight leave requests open. Staff could still approve them.
- Impact: The house's leave desk listed requests for learners who no longer slept there. An approval could put a former boarder on the "away tonight" list.
- Reproduction: Ask for a night away for a boarder. End their boarding place. The request still waits, and approving it works.
- Resolution: Ending a boarding place cancels requests still waiting and approved nights not yet begun. Approval is refused when the learner no longer has a bed at that campus.

## A library copy was held for a learner who moved on

- Status: Fixed
- Area: Library, enrollment
- Observed: A campus move or a closed enrollment left the learner's library reservations open at the old campus. The learner keeps that campus membership on purpose, so nothing stopped the queue.
- Impact: A returned copy was held behind the desk for a learner who no longer came, and the next person waited until the hold ran out. The portal lists only the current campus's reservations, so the family never saw it.
- Reproduction: Reserve a title for a learner, then move them to a sibling campus, or withdraw them. The reservation stays Waiting or Ready.
- Resolution: A move cancels every open reservation at the source campus. Closing an enrollment cancels them at its campus. The held copy passes to the next person in the queue.

## A campus a teacher left still blocked publishing a timetable

- Status: Fixed
- Area: Timetable publishing
- Observed: The teacher clash check at publish time read every campus of the organization, including campuses the teacher no longer belongs to.
- Impact: A campus could not publish a timetable for a new teacher when a campus the teacher had left still named them at that time.
- Reproduction: Publish a lesson for a teacher at campus A. End their membership at A. Timetable them at campus B at the same time and publish. It is refused.
- Resolution: A clash with another campus counts only for teachers who still hold an active membership there.

## A campus a teacher left still blocked their cover elsewhere

- Status: Fixed
- Area: Timetable cover
- Observed: The cover check read lessons at every campus of the organization, including campuses the teacher no longer belongs to.
- Impact: A teacher who left a campus, while its timetable still named them, could not cover a lesson at their current campus at that time.
- Reproduction: Timetable a teacher at a sibling campus, then end their membership there. Try to book them as cover at the same time at the working campus. It is refused.
- Resolution: The check reads only campuses where the teacher still holds an active membership.

## A suspended learner vanished from the student list

- Status: Fixed
- Area: Students
- Observed: The student list and the dashboard count showed only Active enrollments. The list even had an "Enrollment" column that could only say Active.
- Impact: Once a learner was suspended, staff could not find them to bring them back, except by typing the profile URL.
- Reproduction: Suspend a learner from their profile. Open Students. The learner is gone.
- Resolution: The list and the dashboard count use the new `enrolledStudents()` scope (Active or Suspended).

## Deleting a person at one campus deleted them at every campus

- Status: Fixed
- Area: People (teachers, parents, students)
- Observed: The delete action in the teacher, parent and student lists soft-deleted the account. One account serves every school the person belongs to.
- Impact: A campus that removed a shared teacher, or a parent with a child at a sibling campus, locked that person out of the other campus too. The other campus lost them from its lists, timetables and portal.
- Reproduction: Make a teacher a member of two campuses. Delete them from the teacher list of one campus. They can no longer sign in anywhere.
- Resolution: When another school still has the person, the delete only ends this school's membership and says "removed from this school". The account is deleted only when this was their last school.

## A suspended learner's seat and fees were treated as free

- Status: Fixed
- Area: Enrollment, admissions, fees
- Observed: Seat counts, waitlist offers, bulk invoicing and "attends elsewhere" checks counted only Active enrollments. A Suspended learner was ignored.
- Impact: A full section could take one more learner than its capacity while a learner was suspended, and the waitlist offered that seat. Bulk invoicing skipped suspended learners, so they were never billed. Another campus could enrol a learner who was only suspended at their school.
- Reproduction: Place a learner in a section with capacity 1 and suspend them. Place another learner in the same section. Or invoice the section and see the suspended learner missing.
- Resolution: These checks now use the `enrolled()` scope (Active or Suspended). Attendance registers stay Active only.

## A suspended learner vanished from mark sheets and school notices
- Status: Fixed
- Area: Gradebook and notices
- Observed: Course rosters and notice audiences read only active enrollments. A learner suspended for a week dropped off every mark sheet and stopped getting the school's notices.
- Impact: Teachers could not finish or publish the learner's results while they were suspended. The family missed notices during the suspension.
- Reproduction: Suspend an enrollment. Open a mark sheet for their section, or publish a whole-school notice.
- Resolution: `StudentRecord::enrolled()` covers active and suspended enrollments. The gradebook roster and notice audiences use it. Attendance registers still list only learners in class.

## A suspended learner's family was locked out of the portal
- Status: Fixed
- Area: Portal
- Observed: The portal read only active, graduated, and transferred enrollments. Suspending a learner made every portal page for them refuse the family.
- Impact: During a suspension the family could not see the school's notices, invoices, or requests, when they needed them most.
- Reproduction: Suspend an enrollment. Open any portal page for it as the learner or a guardian.
- Resolution: `PortalAccess` reads suspended enrollments. Withdrawn and archived enrollments stay closed.

## A student import could enrol a learner who already attends another school
- Status: Fixed
- Area: Imports
- Observed: A student import row reused the account for its email and enrolled it at the working campus. It did not check for an active enrollment at another school, which the admission form refuses.
- Impact: One learner attended and was billed at two schools at once.
- Reproduction: Enrol a learner at campus A. At campus B, import a students file with that learner's email.
- Resolution: `StudentImporter` refuses the row and names the school the learner attends. No account or enrollment is changed.

## A waitlist place could enrol a learner who already attends another school
- Status: Fixed
- Area: Admissions
- Observed: Accepting a waitlist offer checked only for an enrollment at the same campus. A learner attending a sibling campus, or another school, got a second active enrollment.
- Impact: One learner attended and was billed at two campuses at once. This bypassed the campus move and the transfer, which carry history and balances.
- Reproduction: Enrol a learner at campus A. Put them on a full section's waitlist at campus B. Offer and accept the place.
- Resolution: `AcceptWaitlistEntry` refuses a candidate with an active enrollment at another school, as the admission form already does. The offer stays open.

## A school notice still reached learners who had moved on
- Status: Fixed
- Area: Notices
- Observed: A whole-school notice went to every person with an active membership at the campus. A learner who moved campus, graduated, or left keeps that membership, so they kept receiving the old campus's notices. A notice to the student role reached them too.
- Impact: Families got another campus's sports days, closures, and fee reminders, and could act on them.
- Reproduction: Move a learner to a sibling campus. Publish a notice to the whole old campus.
- Resolution: `NoticeAudience` leaves out members who hold a learner record but do not attend the campus, unless they hold a staff role there.

## A campus move waited forever for a learner who had left
- Status: Fixed
- Area: Campus moves
- Observed: Withdrawing, transferring, or graduating a learner left their campus move request open. Approving it then failed, because a closed enrollment cannot move.
- Impact: Both campuses' inboxes kept a request nobody could approve. Someone had to reject it by hand.
- Reproduction: Ask to move a learner to a sibling campus. Withdraw the learner before the other campus decides.
- Resolution: Closing an enrollment cancels its waiting move request, with the reason as the note.

## The collapsed sidebar pushed its icons into the border and hid later items
- Status: Fixed
- Area: Layout sidebar
- Observed: Collapsed, the 3rem rail left 15px for each 32px button. The icons sat off-centre against the right border, out of line with the logo. The rail could not scroll, so items below the fold could not be reached.
- Impact: The collapsed sidebar looked broken, and most of the menu could not be used in it.
- Reproduction: Collapse the sidebar on a desktop screen.
- Resolution: april-ui v1.3.0 pads the sidebar content (`p-2`) as well as each group; shadcn pads only the group. It also copies shadcn's `overflow-hidden` for the collapsed rail. The menu drops the content padding and lets the rail scroll without a bar when collapsed.

## A learner who moved campus or left stayed in the old campus's clubs and groups
- Status: Fixed
- Area: Programmes and cohorts
- Observed: A campus move or a closed enrollment left programme places running and cohort memberships open at the old campus. The old campus could also reactivate a place for a learner who now attends another campus.
- Impact: Clubs and groups listed learners who no longer attend. Their counts were wrong.
- Reproduction: Give a learner a club place and a cohort membership. Move them to a sibling campus, or withdraw them.
- Resolution: A move or a closed enrollment withdraws the old campus's places and closes its cohort memberships. A graduate completes active places and keeps their class. A place can only run for a learner at the programme's campus.

## The organization dashboard counted a person once per campus
- Status: Fixed
- Area: Organization dashboard
- Observed: "Campus access" added up each campus's member count. A person with access to two campuses counted twice. Every learner who moved campus keeps both memberships, so each move raised the total. The campus row also showed the raw label "AcademicPeriod".
- Impact: The organization overstated how many people can work in its campuses.
- Reproduction: Give one person active memberships at two campuses of an organization. Open the organization dashboard.
- Resolution: The total counts distinct people with an active membership at any campus. The row uses the school's own name for a period.

## The campus that taught a term could not issue its report card after the learner moved
- Status: Fixed
- Area: Report cards
- Observed: The report card screen listed only learners who attend the working campus. `PublishReportCard` refused a period from a campus other than the learner's current one.
- Impact: A learner who moved after a term had approved results and no report card for it. The new campus could not issue it, because the period was not its own.
- Reproduction: Approve a result for a learner in a closing period. Move them to a sibling campus. Publish their report card for that period at the old campus.
- Resolution: `StudentRecord::studiedInSchool()` also finds learners who hold results at the campus. The report card is issued by, and filed at, the campus that owns the period. A learner with no results there is still refused.

## A learner who moved mid-term lost the term's marks at the old campus
- Status: Fixed
- Area: Gradebook
- Observed: The roster read only the learner's current section and campus. After a campus or section move, the old offering dropped the learner. Its teacher could not finish their marks or publish their result.
- Impact: The term the learner studied at the old campus never reached a result, a report card, or a transcript.
- Reproduction: Record a mark for a learner. Move them to a sibling campus. Record another mark, or publish their result, at the old offering.
- Resolution: `CourseOfferingRoster` keeps a learner who already holds a mark in the offering. A learner with no mark there is still refused.

## A bed stayed taken after its learner moved campus or left
- Status: Fixed
- Area: Boarding
- Observed: A campus move, a transfer, a withdrawal, or a graduation left the learner's bed taken. After a campus move, the old house could not free it: "end placement" looked the learner up at the working campus and failed.
- Impact: Beds were lost for good at the old campus. The house showed a learner who no longer attends.
- Reproduction: Give a learner a bed at campus A. Move them to campus B. Open the house at campus A and end the placement.
- Resolution: `AssignBoardingPlace::release` frees the bed when a learner moves campus or an enrollment closes. The end is recorded at the house's campus. The house screen finds the learner by id, because the bed already proves the placement is its own.

## The portal hid a book still out from a campus the learner left
- Status: Fixed
- Area: Portal library
- Observed: The portal listed loans only at the learner's current campus. After a campus move, a book still out from the old campus's library vanished from the list.
- Impact: The old library still fines the late book, but the family cannot see which book to return or where.
- Reproduction: Issue a book at campus A. Move the learner to campus B in the same organization. Open the portal library.
- Resolution: `PortalSummary::library` lists open loans from every campus of the organization. A loan from another campus shows "Return to <campus>".

## A teacher could be sent to cover a lesson while timetabled to teach their own class
- Status: Fixed
- Area: Timetable cover
- Observed: Cover only checked other cover on the same date. A teacher with their own published lesson at that hour, at this campus or a sibling campus, could still be chosen.
- Impact: Two classes expect the same teacher at the same time. One class is left without a teacher.
- Reproduction: Publish two timetables with overlapping 08:00 lessons for teachers A and B. Record cover for B's lesson with teacher A.
- Resolution: `CreateTimetableSubstitution` reads the teacher's own lessons on that date across the organization's campuses. It refuses the cover unless someone else already covers that lesson.

## A teacher could be timetabled at two campuses at the same hour

- Status: Fixed
- Area: Timetables, campuses
- Observed: Publishing a timetable checked teacher clashes only against timetables of the same academic period. Each campus keeps its own periods, so a teacher who works at two campuses could teach at both at 08:00 on Monday, and nothing warned.
- Impact: Organizations that share teachers between campuses published impossible timetables. The clash showed up only when a class had no teacher.
- Reproduction: Give a teacher a Monday 08:00 lesson at campus A and publish it. At campus B, whose term covers the same dates, give them a Monday 08:30 lesson and publish.
- Resolution: The clash check also reads published timetables at the other campuses of the organization whose periods share dates, and compares only the shared dates. Another organization's timetables stay private. `TimetableRevisionTest` covers both cases.

## A family lost the old school's records after a transfer

- Status: Fixed
- Area: Parent portal, transfers between organizations
- Observed: A transfer closes the old enrollment as "Transferred". The portal only read active and graduated enrollments, so the family could no longer open the old school's report cards, transcripts, or invoices.
- Impact: Families could not show earlier results to the new school, and could not see or settle a debt the old school still held.
- Reproduction: Charge a learner at school A. Transfer them to a school of another organization. Open the portal as their guardian.
- Resolution: The portal now reads transferred enrollments, as it already read graduated ones, and each still shows its status. Withdrawn, suspended, and archived enrollments stay out, as before.

## A library fine vanished when the learner had moved campus

- Status: Fixed
- Area: Library, fines, campus moves
- Observed: A learner borrowed a book at campus A, moved to campus B, then returned it late at A. The loan recorded the fine, but no books showed it owed. The fine looked up the learner's enrollment at the working school only, found none, and charged nothing.
- Impact: The lending library lost every fine from a learner who had moved. Had the lookup found an enrollment, the fine would have gone into campus B's books.
- Reproduction: Lend a book at campus A with a daily fine. Move the learner to campus B. Take the book back late at A.
- Resolution: `ReturnLoan` finds the borrower's enrollment at the lending campus or another campus of its organization and charges the fine to the lending campus. `ChargeStudent` takes the campus that is owed. The account screen also opens where the learner has charges in the books, so the library campus can collect.

## Changing a billing group after a move put credit at the wrong campus

- Status: Fixed
- Area: Billing groups, credits, campus moves
- Observed: Held credit followed today's billing groups, but the books follow the groups at the time of the move. When campuses joined a group after a learner moved, the new campus could spend credit the old campus still held. When a campus left a group after a move, carried credit became unusable where it was carried, and the old campus could refund money its books no longer held.
- Impact: One campus's books went negative while another kept money that was already spent. A family's carried credit could be stuck.
- Reproduction: Pay ahead at campus A. Move the learner to campus B while the campuses bill separately. Put A and B in one billing group, then use credit at B.
- Resolution: A campus's held credit is now what its own books hold, capped by the learner's unused payments. Money the campus took itself is used first. `BillingGroupTest` covers joining and leaving a group after a move.

## A campus could not reach the account of a learner who moved on

- Status: Fixed
- Area: Student accounts, refunds, campus moves
- Observed: After a learner moved to a campus with separate books, the old campus got 403 on their account, including from the "Student account" link on its own invoice. Money it still held for the family could not be given back or used.
- Impact: Credit stayed stuck at the old campus, and the family could not get it back.
- Reproduction: Take 200.00 from a learner at campus A. Move them to campus B (no shared billing group). At campus A, open the learner's account from one of A's invoices.
- Resolution: The account screen opens at any campus that billed the learner or took money from them, and reads that campus's books. Balance, credit, invoices, refunds, credit use, and reversals all use the campus the screen is open at. A school with no account for the learner still gets 403.

## A new campus could spend or give back money its old campus held

- Status: Fixed
- Area: Student accounts, credits, refunds, campus moves
- Observed: A learner paid ahead at campus A, then moved to campus B, which keeps separate books. Campus B's account screen counted campus A's credit as its own. B could spend that credit against its own invoices, refund it from its own books, and take back payments campus A had recorded.
- Impact: Money crossed between campuses that keep separate books. Campus B's books went negative, and campus A still showed the credit.
- Reproduction: Take 200.00 from a learner at campus A. Move them to campus B (no shared billing group). Open their account at campus B and refund 50.00.
- Resolution: Held credit now counts only payments at the learner's campus or a campus of the same billing group. A campus takes back only payments it recorded, and the reverse action is hidden on the others. `BillingGroupTest` and `StudentPaymentTest` cover both paths.

## A family could not see what a campus they left was still owed

- Status: Fixed
- Area: Parent portal, invoices, campus moves
- Observed: A learner moved to a campus with separate books while owing the old campus. The portal showed only the new campus's balance, so the family saw nothing owed.
- Impact: Families did not know about a debt the old campus still chased. Staff saw it on the account screen, but the family did not.
- Reproduction: Charge a learner at campus A. Move them to campus B (no shared billing group). Open the portal's invoices page as their guardian.
- Resolution: The portal invoices page lists "Owed at <campus>" beneath the current balance for each other campus the learner still owes.

## Report cards and transcripts reloaded the page to publish

- Status: Fixed
- Area: Report cards, transcripts
- Observed: Publishing a report card or issuing a transcript posted a classic form and reloaded the page. The directory below lost its filters and page.
- Impact: Staff issuing cards for a whole class started their search again after every card.
- Reproduction: Filter the report-card directory, then publish a card.
- Resolution: The Livewire `ReportCardDirectory` and `TranscriptDirectory` publish in place, show a notification, and clear the form. The `report-cards.store` and `transcripts.store` routes and their controller methods are removed. A flaky `TimetableRevisionTest` case now builds its foreign section in another school.

## A campus lost the money it collected after its learner moved on

- Status: Fixed
- Area: Fee invoices, payments, campus moves
- Observed: A learner moved between two campuses that keep separate books. The old campus took payment on its own open invoice. The payment was booked at the new campus as unused credit, and the invoice stayed unpaid.
- Impact: The old campus could never clear the debt it was owed. The new campus showed money it never took.
- Reproduction: Invoice a learner at campus A. Move the learner to campus B (no shared billing group). At campus A, take payment on the invoice.
- Resolution: A payment taken on an invoice is now booked at the campus that issued the invoice. Other payments still go to the campus the learner attends.

## People tables and resets reloaded the page, and unused write routes stayed open

- Status: Fixed
- Area: Students, teachers, parents, admins, promotions, graduations; fee, fee-category, timetable, and time-slot controllers
- Observed: Deleting a person, resetting a promotion, or resetting a graduation posted a classic form and reloaded the page. An administrator could delete their own account. The fee, fee-category, and timetable store and update routes, and the whole time-slot controller, had no screen that used them.
- Impact: A delete lost the table's place. An administrator who deleted themself was locked out of their school. The unused routes were open surface that no screen covered.
- Reproduction: Delete a teacher from page 2 of a searched teachers table. As an admin, delete your own row in the admins table.
- Resolution: The four people tables and the promotion and graduation tables now act through Livewire methods that find the row with the table's own school scope. Nobody can delete their own admin account. The unused routes, `TimetableTimeSlotController`, and five unused form requests are removed.

## Seven more tables deleted rows through a full page reload

- Status: Fixed
- Area: Subjects, notices, fees, fee categories, fee invoices, exams (list and academic-year page), exam slots
- Observed: Each row's delete posted a classic form. The page reloaded and dropped the table's search, sort, and page. Unused store, update, and edit routes for subjects and notices were still open.
- Impact: After each delete, the person had to find their place again. The unused routes were extra surface that no screen used or tested through the UI.
- Reproduction: Search the fees table, go to page 2, and delete a fee.
- Resolution: Each table now deletes through a Livewire method. The method finds the record inside the working school, checks the policy, and shows a refusal as a message. The delete routes, the subject store and update routes, the notice edit and update routes, and `UpdateNoticeRequest` are removed. `CrossSchoolAccessTest` now checks that these records have no delete route left.

## Table row buttons were 32px and deleted through a page reload

- Status: Fixed
- Area: Shared table actions (`components/table-actions`), custom timetable items
- Observed: Each row's action buttons were 32px high, under the 44px touch target. A delete posted a classic form, reloaded the whole page, and lost the table's search and page.
- Impact: On a phone, the row buttons were easy to miss. After a delete, the person had to find their place again.
- Reproduction: Open custom timetable items on a phone, search, then delete an item.
- Resolution: Row buttons are now 44px. The table-actions component takes an `action` item that calls a Livewire method on the row after the confirm. Custom timetable items delete this way, through `InteractsWithAprilTable::changeRow`, which shows a refusal as a message and steps back from an emptied last page. The old delete route is removed.

## A school could share records it no longer held, and guess another school's roll

- Status: Fixed
- Area: Data sharing (`RequestDataSharing`, `FulfilDataSharingRequest`, `DataSharingRequest`, `CreateDataSharingRequestForm`)
- Observed: A request names the school that held the learner when it was sent. After a campus move, that school could still approve and hand over the records, including those the new campus wrote. Also, the ask form told a person when an admission number missed, with no limit on tries.
- Impact: A campus that no longer holds a learner could send out another campus's records. A person could walk through another school's admission numbers to learn who attends.
- Reproduction: Ask school A for a learner. Move the learner from A to a sibling campus. Approve and hand over as A. For the second case, submit many admission numbers against one school.
- Resolution: Approving and handing over now refuse when the learner no longer attends the asked school, and the handover button hides. One person may miss 10 admission numbers an hour, then the form asks them to wait.

## A campus move could place a student in a draft, archived, or past-year section

- Status: Fixed
- Area: Student profile campus move (`ShowStudentProfile::moveCampus`)
- Observed: The form listed only open sections of the sibling campuses. The move checked only that the section's campus was a sibling, so any section id from the browser passed.
- Impact: A student could land in an archived section or in last year's class at the other campus, and drop out of that campus's current lists.
- Reproduction: Open a student's profile, set `campusCycleSectionId` to an archived or past-year section of a sibling campus, and move.
- Resolution: The list and the move now read the same query: open sections in each campus's current school year. The offered list is locked.

## A stale campus move request pulled a student out of a campus that never agreed

- Status: Fixed
- Area: Campus moves (`RequestCampusMove::approve`, `ListCampusMoveRequests`)
- Observed: Campus A asked campus B to take a student. The organization then moved the student to campus C. B could still approve, and the student moved from C to B.
- Impact: Campus C lost a student without a request or a decision. When the move itself refused, the queue screen failed with an error page.
- Reproduction: Request a move from A to B. Move the student from A to C as an organization person. Approve the request as B.
- Resolution: Approving now refuses when the student no longer attends the campus that asked, and says to reject the request. The queue shows any refusal as a message.

## The calendar editor named any account on the platform

- Status: Fixed
- Area: Calendar event editor (`CalendarEventEditor`)
- Observed: The browser could write `userIds`. The page then read those users with no school limit and showed their names as chosen people.
- Impact: A person who can add a calendar day could read the name behind any user id on the platform.
- Reproduction: Open the add form, set `userIds` to another school's user id from the browser, and read the chosen list.
- Resolution: `userIds` is locked, so only "add" and "remove" change it. The chosen list reads this school's active people only. Editing a day drops people who have since left the school.

## A members-only organization administrator could give themself full authority

- Status: Fixed
- Area: Organization members (`OrganizationMembers`, `SetOrganizationMemberPermissions`, `GrantOrganizationMembership`)
- Observed: A member trusted only to manage members could open their own row, tick "Full authority", and save. They could also grant a new member, who always started with every permission.
- Impact: One delegated administrator could take over campus setup, organization settings, and student moves between campuses.
- Reproduction: Delegate only "Manage organization members" to a member. As that member, edit your own row, tick "Full authority", and save.
- Resolution: A person can now only give or take away the permissions they hold. A new or returning member gets no permission the granting administrator lacks. The editor disables the permissions the administrator cannot give. The row buttons are now 44px high.

## A timetable cell took another school's subject, item, or room

- Status: Fixed
- Area: Timetable builder (`ManageTimetable::assign`, `TimeSlotService::placeRecord`)
- Observed: The builder sent the subject, item, and room ids from the browser. The service attached them with no school check. The old record route checked only that the ids existed somewhere.
- Impact: One school's timetable could show another school's subject names and book another school's rooms. A closed room or a bus could also hold a lesson.
- Reproduction: Open the builder, choose a cell, then call `assign('subject', <another school's subject id>)`. Or post that id to the old record route.
- Resolution: `placeRecord` now refuses a subject or item of another school, and a room that is not this school's, open, and able to hold a lesson. The unused record route, its controller method, and `StoreTimetableRecordRequest` are removed.

## Switching the working school reloaded the whole page through a classic form
- Status: Fixed
- Area: Layout, school switcher
- Observed: The sidebar's working-school select posted a hidden form to `schools/set-school` on change. The select was shorter than 44px, and the sidebar's menu lists were browser-writable Livewire properties.
- Impact: The switch skipped Livewire's loading state, so a slow switch looked like nothing happened and invited a second change.
- Reproduction: Belong to two schools and change the working school in the sidebar on a slow connection.
- Resolution: The sidebar calls a Livewire action that checks access, switches the school, and returns to the dashboard. The select is 44px and disabled while it works. The POST route and controller method are gone, and the sidebar's lists are locked.

## Timetable event dates and audiences could skip their checks through browser-edited lists
- Status: Fixed
- Area: Timetables, guardians, enrollment, fees
- Observed: The new-timetable form checked event dates against a browser-writable list of terms, and event audiences against a browser-writable list of roles. The guardian screen, the enrollment status choice, and the invoice filter also checked choices against browser-writable lists.
- Impact: A person could place an event outside its term or give it an audience that is not a school role. The guardian and enrollment actions refuse bad values on their own, so those were defence in depth.
- Reproduction: Open a new timetable, change a term's dates in the component's `periods` from the browser console, and add an event outside the real term.
- Resolution: Each of these lists is locked, so only the server sets it. A unit test lists the locked allowlists so a later change cannot unlock one quietly.

## The dashboard's counts and setup checklist could be rewritten from the browser
- Status: Fixed
- Area: Dashboard
- Observed: Every count on the dashboard, the campus tile flag, and the setup checklist were browser-writable Livewire properties.
- Impact: A changed value survived the next request from the same component, so a person could show a campus tile or a checklist that the server had not chosen. No data leaked, but the screen could no longer be trusted as the server's view.
- Reproduction: Open the dashboard and set `showCampuses` or `students` from the browser console, then press a dashboard button.
- Resolution: The dashboard's display properties are locked. Only the server sets them.

## A student could list any section's timetables, drafts included, by editing the timetable list in the browser
- Status: Fixed
- Area: Timetables, security
- Observed: The timetable list kept `isStudent` and the allowed section list as browser-writable properties. A student could set `isStudent` to false and add another section to the list, then see that section's timetables, drafts included. The component had no access check of its own. A student's section came from any enrollment, not the one they attend, and students never saw schoolwide timetables that they may open.
- Impact: Students saw unpublished timetables and other classes' schedules. A student who had left a section still listed its timetables. Students missed assemblies and other schoolwide schedules.
- Reproduction: Sign in as a student, open Timetables, and set `isStudent` to false and `cycleSections` to another section from the browser console.
- Resolution: The component checks timetable access on mount, locks the properties the server sets, and works out a student's attended section on the server each time. Students see published timetables for their section and the school, and a student-facing empty state.

## The classes page linked to "sections this year" but opened every year, and the class tree took its display flags from the browser
- Status: Fixed
- Area: Academic structure, classes
- Observed: The classes page's "Go to sections this year" link opened the sections list for every year. The status filter buttons were 36px. The page wrapped a short explanation in a card above a second card. The class tree's school-setup, link, and status flags were writable from the browser.
- Impact: Staff landed on a list that did not match the link. A changed flag in the browser could show setup links or a different status filter than the page chose.
- Reproduction: Open Classes and press the sections link. Or set the tree's status property from the browser console.
- Resolution: The page shows the status filter as 44px links with the current one marked, a plain "Sections" link, and the help button. The controller checks for a match instead of loading a page of classes it never showed. The tree's flags are locked.

## The subjects list printed the school year twice, and the sections list said "this year" while showing every year
- Status: Fixed
- Area: Curriculum lists
- Observed: Each subject row read "2026 - 2027 · 2026 - 2027 · Term 1". The sections page was titled "Sections this year" when its filter showed every year. Its filters needed a separate Apply press on a phone, and each row carried View and Edit buttons beside the status control.
- Impact: Rows were longer than the screen on a phone and the heading misled staff about what they were looking at.
- Reproduction: Open the subjects being taught list. Open the sections list and choose "Every school year".
- Resolution: The subject row shows the year once. The sections list is now a Livewire component: filters apply as they change, the title is "Sections", the section name opens the section, and the status control carries the only row actions.

## The menu button, year switcher, and create buttons were too small to tap on a phone
- Status: Fixed
- Area: Layout, shared controls
- Observed: At 390px the sidebar trigger was 28px, the working year and term selects 32px, the theme and profile buttons 40px, every "Add …" page action 40px, and each help "?" 28px. The section list filters were 34px.
- Impact: The sidebar trigger is the only way to reach the menu on a phone. Staff on phones missed taps on controls that sit on every page.
- Reproduction: Open any dashboard page at 390px wide and measure the header controls.
- Resolution: Each shared control is now at least 44px on a phone. The help button keeps its look and gets a 44px hit area. A unit test guards the shared controls. The april-ui data table (search, sort, page size, pagination) is still 32 to 36px and needs a fix in the package.

## Rolling sections into a new year brought back archived sections and retired classes
- Status: Fixed
- Area: Academic structure, section roll forward
- Observed: The roll forward copied every section of the source year. Archived sections, and sections of a class that was archived or had become a group, came back as drafts in the new year.
- Impact: A school that closed a stream or retired a class saw it return every year. Staff could activate a section under a class that no longer takes learners.
- Reproduction: Archive a section, or archive its class, in last year. Roll last year's sections into this year.
- Resolution: The roll forward copies only live sections of active classes that take sections, and lists the rest as "Left behind". The page is now a Livewire component. It proposes last year as the source, refuses a closed year before the button shows, and returns to school setup when opened from there.

## A section added from school setup lost its way back, and a section's capacity could drop below its class list
- Status: Fixed
- Area: Academic structure, sections
- Observed: A section created from the school setup classes step returned to the year setup instead. A section's capacity could be set below the number of learners already placed in it. A class teacher who left the school showed as "Not chosen yet" on edit.
- Impact: Setup lost its place. Capacity numbers were false and could not be trusted for admissions. A save cleared a teacher link without the editor seeing it.
- Reproduction: Open school setup, add a section at the classes step, and save. Or place three learners in a section and set its capacity to 2.
- Resolution: The section form is now a Livewire component. It returns to the step that opened it, refuses a capacity below the placed learners, names a departed teacher, and takes only this school's years, active classes, and current teachers.

## Every dashboard sent the platform's school count to the browser, and "Open terms" counted closed ones

- Status: Fixed
- Area: Dashboard
- Observed: `DashboardDataCards` kept `School::count()` and, for platform staff, `Organization::count()` in public properties that no screen read. Livewire writes every public property into the page, so any teacher, parent, or learner could read how many schools the whole platform hosts. The "Open terms" figure counted every period of the year, including drafts and closed ones. "Continue to dashboard" was a full-page form that always said "Your school is ready for daily work", even when a tab left open had fallen behind a setup step that slipped back and nothing was recorded.
- Impact: One tenant could learn the size of the platform. Staff read a wrong count of open terms. An administrator could believe setup was confirmed when it was not.
- Reproduction: Open the dashboard as any user and search the page source for the component snapshot. Or give the current year one open and two draft periods and read "Open terms".
- Resolution: The unused counts are gone, and the open-term figure counts only open periods. "Continue to dashboard" is a Livewire action that checks the school, confirms only a ready setup, and otherwise says setup needs attention again. The acknowledge POST route and its controller are gone. `DashboardTest` and `SchoolSetupPhaseTest` cover these paths.

## The campus list on a calendar template hid which template a campus followed, and a refused draft lost its date

- Status: Fixed
- Area: Organization calendar templates
- Observed: A campus that followed a different template read "Uses another campus override" without naming it. Drafting a school year that overlapped one the campus already had reloaded the page with only a banner, away from the date that caused it. Each campus row also held its own full-page form with a required reason field, so the page showed one reason field for every campus at once.
- Impact: An organization administrator could not tell which calendar a campus actually followed before drafting a year for it or pointing it at a new template.
- Reproduction: Point campus A at template X, then open template Y. Or draft a year for a campus on a date that overlaps its current year.
- Resolution: The Livewire `CalendarTemplateCampuses` names the template each campus follows. It asks for a reason only for the campus being changed, and shows a refused draft under the start date, keeping the typed date. It lists and accepts only this organization's campuses. The draft, override, and inherit POST routes, `AcademicCycleController`, and their requests are gone. `CalendarTemplateManagementTest` covers these paths.

## A calendar template could lose a sub-period's parent, or generate terms that share days

- Status: Fixed
- Area: Organization calendar templates
- Observed: The template form showed eight numbered rows, and "Parent row" named a row by that number. The save dropped blank rows and then counted only the filled ones, so a parent named after a blank row pointed at the wrong period or was refused. On reopening, periods were listed by display order, so a sub-period with a lower order than its parent was listed above it, where its parent could not be chosen, and the next save quietly removed the link. Nothing checked a period against the year or its parent, or two terms against each other.
- Impact: Every campus year generated from the template could get a half-term under the wrong term, terms that ran past the year's end, or two terms covering the same day. A period typed by hand is refused for that last case, because "the period that covers today" then has two answers.
- Reproduction: Leave row 2 blank, fill rows 3 and 4, and set row 4's parent to row 3. Or give Term 2 a half-term with order 1, save, and save again. Or set Term 2 to start on day 80 while Term 1 runs 84 days.
- Resolution: The save reads each parent by the row number the form showed. It refuses a period that ends after the year, a sub-period outside its parent, and periods side by side that share a day, naming the rows. The Livewire `CalendarTemplateForm` lists every parent before its sub-periods, adds and removes rows (renumbering parents), and fits a phone screen. The store and update routes and their requests are gone. `CalendarTemplateManagementTest` covers these paths. A stale assertion in `AcademicCalendarSetupTest` from the exam form move now checks the preselected period on the component.

## A class added from school setup lost its way back, and a level with sections could become a group

- Status: Fixed
- Area: Academic structure / class levels
- Observed: The school setup links to the add-class screen with `school_setup=1`, but the form posted only `setup` and `academic_year_id`, so a save landed on the "set up an academic year" step. Separately, the edit form let a level that already had sections, or subjects taught by section, be marked as a level group.
- Impact: A new school was sent past the classes step during onboarding. A level turned into a group kept sections that groups may not have, which breaks section creation, rankings, and subject rosters for that level.
- Reproduction: From school setup, add a class and save. Or give a level one section, edit the level, and tick "This is a level group".
- Resolution: The Livewire `AcademicLevelForm` keeps the setup return path, and only a year of this school counts. It offers only this school's active groups as a parent and clears the parent when the level becomes a group. `UpdateAcademicLevel` refuses to make a group of a level that has sections or section-based subjects. The store and update routes and their requests are gone. `AcademicStructureScreenTest` and `CrossSchoolAccessTest` cover these paths.

## Rolling subjects into a new year skipped a section whose sibling already existed
- Status: Fixed
- Area: Course offerings / year rollover
- Observed: The rollover treated any offering for the same subject, period, and level in the new year as "already there". When section A's Maths was set up by hand, section B's Maths was skipped with no warning.
- Impact: Section B started the year with no Maths offering, so it had no timetable slots, gradebook, or report lines until someone noticed.
- Reproduction: Last year has Maths for sections A and B as separate offerings. In the new year, add Maths for section A only, then roll subjects over.
- Resolution: A section-based offering now claims only its own sections, and a whole-level or named-learner offering claims everyone. The rollover screen is a Livewire component that keeps the setup return path and says when nothing new was copied. The POST route and its request are gone.

## Saving a roster from the year setup lost the way back to setup
- Status: Fixed
- Area: Course offerings / rosters
- Observed: The year setup links to the roster editor with setup=1, but the editor's form never sent that flag, so saving always landed on the course offering list. The editor offered sections and learners of child levels that the save then refused with a general message.
- Impact: Setting up a year meant finding the setup screen again after every roster. A choice the screen offered failed with no field named.
- Reproduction: Open a year's setup, subjects step, open a period's roster, save.
- Resolution: The roster editor is a Livewire component. It keeps the setup flag, offers only sections and learners the save accepts, and shows refusals under the field. The PUT route and its request are gone.

## Editing a timetable published in another tab ended in a bare 403
- Status: Fixed
- Area: Timetables
- Observed: The edit screen posted a plain form. When someone published the timetable while the page was open, saving returned a forbidden page with no reason. A blank description could not be cleared cleanly, and the controller built its excluded keys as one joined string.
- Impact: Planners lost their typing and did not learn why the save failed.
- Reproduction: Open a draft timetable's edit page, publish it in another tab, then save the edit page.
- Resolution: The edit screen is a Livewire component. It re-reads the timetable before saving and says it was published; otherwise it saves the trimmed name and description and opens the timetable. The PUT route and its request are gone.

## A second school could rewrite or take over a shared person, and person forms lost details
- Status: Fixed
- Area: People (admins, teachers, parents, students)
- Observed: Adding a person whose email already had an account overwrote their name, phone, address, and photo with the new school's input. An edit from one school could change the email of a person who also belongs to another school. Edits stored the uploaded profile picture before any check, so any file type was saved. Create forms dropped nationality, address line 2, and postal code; every edit cleared nationality. Adding a guardian at a second school made a second guardian record. "Create student" with the email of a learner enrolled here moved that learner to another section, and it enrolled a learner still active at another school. A person with a legacy lowercase gender could not be saved.
- Impact: One school could deface or hijack the account another school relies on: after an email change, a password reset goes to the new address. Details typed on the forms were lost. Learners were moved or double-enrolled without a transfer.
- Reproduction: At school B, add a teacher with the email of a school A teacher and a different name. Their name changes at school A too.
- Resolution: Provisioning an existing person now fills only blank profile fields and keeps their photo. Only the person can change an email another school shares. The eight person forms are Livewire components on one shared field set that validates the picture first and saves every field. Adding a person in a role they already hold here is refused, one guardian record is kept per person, and a learner enrolled here or active elsewhere is refused with a pointer to the transfer flow. The store and update routes and their requests are gone.

## Graduating learners crashed on review and could graduate a learner of another section
- Status: Fixed
- Area: Students / graduations
- Observed: "Review learners" failed with an SQL error (ambiguous user_id) whenever the section had learners. The confirm post accepted any active learner id of the school, whatever section was chosen, and graduated them one by one outside a transaction.
- Impact: A school could not graduate learners from the screen. A tampered or stale form could graduate the wrong learners; a failure midway left the class half graduated.
- Reproduction: Open Students → Graduate, choose a section with learners, press Review learners.
- Resolution: The screen is now one Livewire component with an optional note for the record. It lists only current-year sections of the school, graduates only learners it listed who are still in that section, and runs in one transaction under a section lock. The POST route and its request are gone.

## Promoting learners crashed on review and could run twice
- Status: Fixed
- Area: Students / promotions
- Observed: "Review learners" failed with an SQL error (ambiguous user_id) whenever the chosen section had learners. The confirm step was a plain form post with no lock, and it kept the reviewed list after a section changed.
- Impact: A school could not promote learners from the screen. A double submit or a stale list could move learners the page no longer showed; a failure midway left some learners moved with no promotion record.
- Reproduction: Open Students → Promote, choose a section with learners and a destination, press Review learners.
- Resolution: The screen is now one Livewire component. It lists only current-year sections of the school, drops the reviewed list when a section changes, and moves only learners it listed. The move runs in one transaction under a lock on the source section, so a second click writes nothing. The POST route and its request are gone.

## A teacher who is also a parent could read only their own children

- Status: Fixed
- Area: Students, fee invoices
- Observed: Staff with the parent role at their own school were treated as guardians only. The student list showed only their children, other learners' profiles returned 404, and the fee invoice list and policy narrowed them the same way. A staff member who was also enrolled as a learner was narrowed to their own invoices.
- Impact: A teacher lost access to the learners they teach once their own child enrolled at the school.
- Reproduction: Give a teacher the parent role at the same school and link them to one learner. Open another learner's profile.
- Resolution: `User::readsLearnersOnlyAsGuardian()` and `readsLearnersOnlyAsThemself()` narrow a person only when they hold no staff role (`isPortalOnly()`). The student profile, student list, fee invoice list and fee invoice policy use them.

## A parent's page showed their children at other schools

- Status: Fixed
- Area: Guardians, cross-school
- Observed: A guardian can have children at two schools. On the "assign learners" page, school B listed every linked child, including the child at school A with their name, email, admission number and class. Linking and unlinking a learner wrote no audit entry, although a link opens that learner's records to the guardian in the portal. Unlinking had no confirmation.
- Impact: School B staff could read the details of a learner they have no relationship with. Nobody could tell who gave a guardian access to a child's records.
- Reproduction: Link one guardian to a learner at school A and a learner at school B. Open the guardian's assign page while working in school B.
- Resolution: The page lists only the linked learners who attend the working school, and refuses to unlink any other. `ChangeGuardianLink` refuses a learner of another school, ignores a repeated link, and audits each change as `GuardianLinkChanged`. Linking and unlinking run as Livewire actions, and unlinking asks for confirmation. The POST route, its request class and the old service method were removed.

## Deleting a timetable item emptied cells of published timetables

- Status: Fixed
- Area: Timetables
- Observed: Deleting a custom timetable item, such as "Break", deleted every cell that held it, including cells of published and archived timetables. A published timetable is meant to stop changing. The unique name check was case-sensitive, so "Break" and "BREAK" could both exist.
- Impact: Learners and teachers saw gaps in a timetable that was already published, and no one had changed that timetable.
- Reproduction: Put "Break" on a timetable, publish it, then delete "Break" from the timetable items list.
- Resolution: `SaveCustomTimetableItem` refuses to delete an item that a published or archived timetable shows, and says to rename it instead. It still takes the item off draft timetables. Names are checked case-blind under a school lock. The add and rename forms run through Livewire, and the store and update routes and their request classes were removed.

## A notice without an end date could not be saved, and an empty message passed

- Status: Fixed
- Area: Notices
- Observed: The form marked "Ends on" as optional, but the check refused an empty end date and the column cannot be empty, so the form only failed. A message that held only an empty paragraph from the editor was accepted. After saving, the form went back to an empty page instead of the draft that still had to be published. Classes chosen before switching the audience to the whole school were kept in the saved audience.
- Impact: Staff lost the message they had written, and some saved notices had no words.
- Reproduction: Write a notice, leave "Ends on" empty, and save. Or save a notice whose editor holds only a blank line.
- Resolution: The notice form runs through Livewire. The end date is required and starts two weeks after the start date. A message with no words is refused. Only the ids of the chosen audience scope are saved. A saved draft opens on its own page so it can be read and published. The store route and its request class were removed.

## An exam could hold two papers with one name, and a paper opened under any exam

- Status: Fixed
- Area: Exams
- Observed: One exam could hold "Mathematics paper 1" twice, and the highest mark had no upper limit, so a typo such as 1000000 was saved. The edit and delete addresses did not check that the paper belonged to the exam in the address, so a paper opened under a different exam's breadcrumbs and could be deleted from there.
- Impact: Mark sheets and timetables could not tell two papers apart, and a wrong highest mark made every percentage near zero.
- Reproduction: Add a paper named "Mathematics paper 1" to an exam twice. Or open `exams/{another exam}/manage/exam-slots/{paper}/edit`.
- Resolution: `SaveExamSlot` locks the exam and refuses a paper name the exam already uses, case-blind. The highest mark must be a whole number from 1 to 1000. Edit and delete return 404 when the paper is not part of the exam in the address. The add and change forms run through Livewire, and the store and update routes and their request classes were removed.

## An exam could fall outside its term, share a name, and lost its dates when edited

- Status: Fixed
- Area: Exams
- Observed: An exam could be dated outside its reporting period, for example a first-term exam in March. One period could hold two exams named "Mid-term", so a report card listed both. The edit form printed the stored date with a time, which a date field rejects, so every edit opened with empty dates. An exam whose papers were already in the gradebook could be moved to another period, which left its marks with the classes of the old term.
- Impact: Calendars and report cards showed exams in the wrong term, and a teacher who saved an edit without noticing had to type both dates again.
- Reproduction: Plan an exam from 1 March in a period that ends in December. Or open an existing exam's edit page and look at the date fields.
- Resolution: `SaveExam` locks the period and refuses dates outside it, an end before the start, a name already used in that period (case-blind), and a move of an exam whose papers are in the gradebook. Each change is audited as `ExamChanged`. The create and edit forms run through Livewire, fill the dates as `Y-m-d`, and offer only periods that still take exams. The store and update routes and their request classes were removed.

## An import could be written twice, and Excel files lost their first column

- Status: Fixed
- Area: Imports
- Observed: Writing an import read its status without a lock. A double click, or two people on the same page, both wrote every row. A file that named one source id on two lines wrote the same record twice, so the second line silently undid the first. A CSV saved by Excel starts with a byte order mark, so the first column name did not match and the file was refused as missing a column.
- Impact: Rows without a source id became duplicate people. Schools that export from Excel could not import at all when the first column was required.
- Reproduction: Open one checked import in two tabs and press Write in both. Or import a staff file whose first column is `name`, saved by Excel as "CSV UTF-8".
- Resolution: Writing or dropping an import now claims it in one conditional update, so only the first request goes ahead and the other is told the import is finished. A source id named twice in one file marks the later line as an error. The CSV reader strips the byte order mark. The upload form and the write and drop buttons now run through Livewire, and the three POST routes and their request class were removed.

## Anybody who could read reports could download the general ledger and every learner's results
- Status: Fixed
- Area: Reports (`ReportRegistry`, `RequestReport`, `ReportRunPolicy`, reports screen)
- Observed: A person with "read report" could download every finished report at the campus. This included the general ledger, the balance sheet and the student balances, even without any finance permission. A person with "create report" could ask for any of them. A double click queued the same report twice. The list never updated until the page was reloaded.
- Impact: A librarian or teacher given report access could export the school's accounts and every family's balance.
- Reproduction: Give a person "read report" and "read student" only. Ask for the general ledger as an administrator. As that person, open the report's download link. The file downloads.
- Resolution: Each report names the permission of the data it copies. Finance statements need "read financial period", balances and income need "read fee invoice", expenses need "read expense", cash needs "read cash deposit", budgets need "read budget", and the class list needs "read student". Report cards and transcripts keep "read report". A person only sees, asks for and downloads reports they may read. The same report asked for again while it builds returns the run already queued. The screen runs through Livewire (`ReportDesk`) and checks again every five seconds while a report is building. The store route and form request are removed.

## One teacher could cover two lessons at once, and cover recorded by mistake could not be withdrawn
- Status: Fixed
- Area: Timetable cover and section versions (`CreateTimetableSubstitution`, `CreateSectionTimetableOverride`, timetable screen)
- Observed: The office could book one teacher to cover two overlapping lessons on the same day. Cover could be recorded for a date outside the term or outside the dates the timetable is in use. Cover recorded by mistake could not be removed. Two quick clicks on "Create override draft" made two drafts of one section. A lost race on the same lesson and date showed a database error.
- Impact: A class was left without a teacher on the day while the timetable showed it as covered. A wrong entry stayed on the record, and duplicate section drafts caused confusion.
- Reproduction: Publish two timetables with lessons at 08:00–09:00 and 08:30–09:30 on the same weekday. Record cover by the same teacher for both on the next matching date. Both were saved.
- Resolution: Cover holds the covering teacher while it checks for an overlapping cover on that date, and refuses it. The date must fall inside the term and the timetable's dates. Cover can be withdrawn before the lesson, and the withdrawal is written to the audit log. Cover for a past lesson stays on the record. Only one open section version per template is allowed, and a double click waits on the template lock. The panel now runs through Livewire (`TimetableCoverPanel`). The two POST routes and form requests are removed.

## A fund written in other capitals made a second budget that counted the same spending
- Status: Fixed
- Area: Budgets (`SetBudget`, budget screen)
- Observed: "Library fund" and "library fund" made two budgets for one account. The books match a fund without regard to capitals, so both budgets counted the same spending. Removing a budget wrote nothing to the audit log. Saving a budget with no change wrote another "Budget set" entry. Two saves at the same moment could fail on the unique index.
- Impact: The budget report showed the planned amount twice for one set of spending. A budget could also disappear with no record of who removed it.
- Reproduction: Write a budget for Operating expenses with the fund "Library fund". Write another with "library fund". Both rows show the same actual amount.
- Resolution: A budget is found by its account, stretch, programme and fund, and the fund comparison ignores capitals. The first spelling is kept. The row is held while it is revised. A lost race revises the plan that won. A save that changes nothing writes nothing. Removing a budget writes a "Budget removed" audit entry. The screen now runs through Livewire (`BudgetPlanner`), with Revise and Remove in the ⋯ menu. The store and destroy routes and the form request are removed.

## Revoked records could still be taken in, and sharing answers raced each other
- Status: Fixed
- Area: Record sharing between schools (`RequestDataSharing`, `FulfilDataSharingRequest`, ask form)
- Observed: After the holding school took a permission back, the school that asked could still take in a package that was already built. Two people could answer one request at once without seeing each other's answer. A second click on "Hand the records over", or a revoke at the same moment, could still build a package. A request could be approved after its end date. A school could send the same learner request again and again while the first one was open. Refusals showed raw values such as "approved to declined".
- Impact: Records crossed a school boundary after the holding school withdrew consent. The holding school also had to answer duplicate requests.
- Reproduction: Approve and hand over a request, then take the permission back. At the asking school, "Take the records in" still worked.
- Resolution: Each change holds the request row, or the package row, and reads its state again inside the lock. The action refuses to take in a package whose request was revoked, and the screen says why. The action refuses an approval after the request ran out. While a request for the same learner is open, the school cannot ask again. Messages use labels. The ask form now runs through Livewire (`CreateDataSharingRequestForm`). The store route and form request are removed.

## One campus rewrote a shared role for every campus, showed other campuses' holders, and could lock itself out of role management
- Status: Fixed
- Area: Roles (`WriteCampusRole`, `AssignCampusRole`, `RoleAuthority`, role screens)
- Observed: Shared roles such as Librarian and Accountant are one row for every campus. A role manager at one campus could change or retire one, and this changed it for every campus in every organization. The role page listed the name and email of every holder at every campus. A manager could take role management from the last person who had it, so nobody at the campus could manage roles. Role names could repeat in other capitals, or copy a shared role's name. Giving a role twice wrote a second audit entry.
- Impact: One school silently changed what staff at other schools could do. It also exposed those staff's email addresses, and could lock itself out of its own roles.
- Reproduction: As a role manager at campus A, save the Librarian role with fewer permissions. A librarian at campus B loses those permissions. Open the role: campus B's librarians are listed.
- Resolution: A change to a shared role now makes the campus's own copy. The copy has the same permissions, and the campus's holders move onto it. Other campuses keep the shared role. The campus copy replaces the shared role in the list, and opening the shared role goes to the copy. The copy's page only lists holders at this campus. Taking a role away or changing a role is refused when nobody at the campus could manage roles afterwards. This check holds the campus lock. Names are checked without case against the campus's roles and the shared roles. Giving or taking a role that does not change anything is ignored. The role screens now run through Livewire (`CreateCampusRoleForm`, `CampusRoleRecord`). The seven POST routes and three form requests are removed.

## A graduation plan with nothing in it told families the learner had finished

- Status: Fixed
- Area: Graduation plans, family portal
- Observed: A plan or stage with no requirements and no stages counted as complete, so the portal told a family their child had finished a plan the school had only just named. The plan list showed every nested stage as a plan of its own. A stage asking for "at least 4" items while holding 2 gave no sign that nobody could finish it. A plan name could repeat when only the capitals differed, and two people saving at once hit the unique index. Changing a plan to a simpler rule kept the old count and credits in the record. Excusing a learner, taking an excusal back and removing a requirement left no trace in the audit log.
- Impact: Families read a false "finished". Staff could not tell a plan from its stages, and nobody could see who excused a learner from a subject.
- Reproduction: Write an active plan for every learner and add nothing to it. Open the portal graduation page as a guardian. The plan said the learner had finished.
- Resolution: GraduationProgress never marks a stage without requirements or stages in use as complete. The plan list shows top-level plans only. The page names an empty stage and a count it cannot reach. The new ManageGraduationPlan action writes plans, stages, requirements and excusals under a school lock, compares names without case, keeps only the numbers the chosen rule reads, and records GraduationPlanChanged or GraduationExemptionChanged. The pages are now the Livewire components CreateGraduationPlanForm and GraduationPlanRecord. The seven write routes and five requests were removed.

## Two people could move one programme place at once, and a withdrawn place reopened in a closed programme

- Status: Fixed
- Area: Programmes
- Observed: Two members of staff moving the same place both succeeded, so the second answer quietly replaced the first. A withdrawn place could be marked as taking part again after the programme closed, after the learner's enrollment ended, or while the learner already held a new place, which left two running places. Two people giving the same learner a place at once made two places. A programme could not be renamed or closed from its page. A name could repeat when only the capitals differed. Any school member, a learner too, could be named as the person who runs a place. Nothing about places went to the audit log.
- Impact: Places counted twice, closed programmes kept filling, and nobody could see who moved a learner out of a support programme.
- Reproduction: Open a requested place in two tabs. Mark it as taking part in one and withdrawn in the other. Both saves were accepted.
- Resolution: ChangeProgramParticipation locks the programme and the place, refuses a state that changed since it was shown, and checks the open door and the running place again before a place reopens. A withdrawn place that had not started ends on its start day. The new SaveProgram action opens, renames and closes a programme with a case-blind name check. Both record ProgramChanged or ProgramParticipationChanged. The pages are now the Livewire components CreateProgramForm and ProgramRecord. Only staff can run a place. The three write routes and their requests were removed.

## A closed group still took members, and a person could leave a group twice

- Status: Fixed
- Area: Groups (cohorts)
- Observed: A group marked closed still took new learners. A joining date in the future was accepted, so the learner showed as in the group before they joined. Taking out a person who had already left moved their leaving date to today. Two groups could share a name when only the capitals differed. Two people adding the same learner at once hit the unique index and showed an error page. Nothing about a watchlist or its members went to the audit log.
- Impact: Closed groups kept growing, the record of who left and when was rewritten, and nobody could see who put a learner on a private watchlist.
- Reproduction: Close a group. Open its page in a second tab from before the change and add a learner. The learner joined.
- Resolution: ChangeCohortMembership locks the group, refuses a closed group and a future joining date, and turns a lost race into the place already held. Taking out somebody who already left is refused. The new SaveCohort action compares names without case under a school lock. Both record CohortChanged or CohortMembershipChanged. The pages are now the Livewire components CreateCohortForm and CohortRecord. The four write routes and their three requests were removed.

## A repeated staff number crashed the page, and a person who left kept their leave

- Status: Fixed
- Area: Staff, employment records
- Observed: A staff number another person in the school already held reached the database's unique index and showed an error page. Marking a person as left kept any leave they had asked for or been given after that day, and did not need a leaving date. A leaving date could stay on a person set back to active. Working hours could overlap on one day, and a wrong qualification or wrong hours could not be removed. Nothing about the record went to the audit log.
- Impact: The leave board counted a person who had left as away, and wrong qualifications stayed on the record for good.
- Reproduction: Give one person staff number STF-7. Write a record for another person with staff number stf-7. The page failed.
- Resolution: The new ManageStaffProfile action refuses a staff number the school already uses, without case, and a second record for one person. Leaving sets the date to today unless one is given, and withdraws leave still held after it. Other states clear the date. Hours that overlap on one day are refused. Qualifications and hours can be removed. Every change records StaffProfileChanged. The pages are now the Livewire components CreateStaffProfileForm and StaffProfileRecord. The four write routes and their requests were removed.

## A second leave approver could overturn the first answer

- Status: Fixed
- Area: Staff, leave
- Observed: Two approvers with the leave board open could both answer one request. The later click won, so an approved leave became declined with no warning. Two requests for the same person's days made at the same moment could both pass the clash check. A declined request asked for again was not checked against leave booked since. The old POST routes for asking and answering were still open beside the Livewire board.
- Impact: A teacher could be told their leave was approved and then find it declined, or hold the same days twice.
- Reproduction: Open the leave board in two tabs as two approvers. Approve a request in one, then decline it in the other. The leave ended declined.
- Resolution: ManageStaffLeave locks the person's profile while it checks for a clash, and locks the request while it changes status. A status change made on a stale reading is refused with "Somebody else already made this leave …". Declined days asked for again are checked for a clash. The store and status routes and both requests were removed.

## Saving organization settings logged every field as changed

- Status: Fixed
- Area: Organizations, settings
- Observed: Saving the organization settings recorded every field in the audit log, even when nothing changed. Spaces around the name and code were stored, and an empty email or phone was kept as blank text. No test covered creating or changing an organization.
- Impact: The audit log could not show who changed an organization's name or code.
- Reproduction: Open the organization settings and save without changing anything. The log said name, code, address, email and phone changed.
- Resolution: UpdateOrganization writes and logs only the fields that changed, and nothing when none did. The new Livewire component OrganizationForm creates and edits organizations, trims values and stores blanks as empty. The store and update routes and both requests were removed. OrganizationSettingsTest covers create, a taken code, the log and access.

## A proved web address kept opening a campus that had left the organization

- Status: Fixed
- Area: Organizations, web addresses
- Observed: An address one organization proved still named a campus after that campus joined another organization, so visitors to the old organization's address landed on the new owner's campus. Any number of addresses could be the main one. Two organizations claiming one address at the same moment made the second see a database error.
- Impact: The old organization's address sent staff and families to a campus it no longer ran.
- Reproduction: Claim and prove an address that opens campus A. Assign campus A to another organization. Open the address. It still chose campus A.
- Resolution: DomainContext only opens a campus of the address's own organization. AssignSchoolToOrganization clears the campus from the old organization's addresses. A new main address replaces the old one, and a claim that loses the race is refused as already claimed. The page is now the Livewire component OrganizationDomains, and its three write routes and request were removed.

## A shared residence could hold a house whose campus no longer used it

- Status: Fixed
- Area: Boarding, shared residences
- Observed: Linking, unlinking and adding a house checked their rules outside any lock. One manager could unlink a campus while another added one of its houses, which left a house in a residence its campus did not use. A campus that moved to another organization kept its links and houses in the old organization's residences. Two residences could have names differing only in case.
- Impact: The old organization kept seeing and managing a campus that had left, and room counts for a site were wrong.
- Reproduction: Open the shared residences page in two tabs. In the first, stop a campus using a residence. In the second, add that campus's house to it. The house went in.
- Resolution: The residence and house rows are locked and the rules checked again inside the transaction. AssignSchoolToOrganization takes the campus and its houses out of the old organization's residences. Names are compared without case. The page is now the Livewire component OrganizationBoardingResidences, and its five write routes and three requests were removed.

## A campus that changed organization kept billing with its old sister campuses

- Status: Fixed
- Area: Organizations, billing groups
- Observed: Moving a campus to another organization left its billing group and calendar template in place. The campus still billed with campuses of the organization it left, and still showed in that organization's group. A billing group could not be deleted, a name differing only in case was allowed twice, and a campus could be placed in a group another manager had just deleted. Placing a campus in a group left no audit record.
- Impact: A debt could be carried between campuses of two different organizations, and a new owner could not see why.
- Reproduction: Put two campuses of one organization in a group. Assign one of them to another organization. It still bills with the other campus.
- Resolution: AssignSchoolToOrganization clears the billing group and the calendar template. The new ManageBillingGroups action starts, places and deletes groups under locks, compares names without case, refuses a group that is gone, and records BillingGroupChanged. The page is now the Livewire component OrganizationBillingGroups. The store and update routes and their requests were removed.

## Two nurses saving one health record could silently replace an allergy

- Status: Fixed
- Area: Wellbeing, health records
- Observed: The health form posted every field. A nurse who opened the record before a colleague saved it wrote the old wording back over the colleague's change. Two first saves at once could also both try to create the record. A field emptied with spaces was kept as blank text.
- Impact: A first aider could act on an out-of-date allergy or medication.
- Reproduction: Open one child's health record in two tabs. Change the allergy in the first tab and save. Change the blood group in the second tab and save. The allergy went back to the old text.
- Resolution: The form is now the Livewire component HealthRecordForm. It sends only the fields the nurse changed, with the values as read. RecordHealthInformation locks the learner, refuses a field somebody else changed meanwhile, and shows its new wording under the field. A second save keeps the nurse's text by choice. Blank text is stored as empty. The PUT route and its request were removed.

## Opening notice delivery wrote a row, and a double tap could fail

- Status: Fixed
- Area: Notice email preferences (`NoticeEmailPreferences`, `UpdatePortalNotificationPreferences`)
- Observed: Only opening the staff page created a preference row. Two tabs opening at once could both try to insert the one row per person and school, and one got a server error. The family save used one insert-or-update per campus, which can race the same way on a double tap.
- Impact: A parent or teacher saw an error page when they only wanted to look.
- Reproduction: Open Notice delivery in two tabs at the same moment on a fresh account.
- Resolution: Opening the page writes nothing. Each switch saves at once with one upsert, and a campus outside the family is refused. Both pages now work through Livewire, and the PUT routes are gone.

## Two people changing one grading scale lost each other's options

- Status: Fixed
- Area: Grading scales (`SaveGradingScale`, `GradingScaleManager`)
- Observed: The edit form sent back only the options it showed. When somebody else added an option meanwhile, the later save deleted it without a word. Delete checked for assessments outside any lock. The option checks lived only in the HTTP requests, so any other caller could save a scale with one option or two options named the same.
- Impact: A grade option a colleague had just added disappeared, and the school did not know why.
- Reproduction: Open the same scale in two tabs. Add an option in one and save. Rename the scale in the other and save.
- Resolution: A save names the time the scale was read. If somebody saved since, nothing is written and the form says so. The option checks now live in the action. Delete runs under a lock. The page now works through Livewire: options used in learner records show as locked, and the old routes are gone.

## Anyone could give up anyone's hall booking, and a hall out of use kept its bookings

- Status: Fixed
- Area: Facilities (`BookFacility`, `ManageFacility`, `FacilityBoard`)
- Observed: Anybody who could book could give up another person's booking. A second tap from a stale screen wrote over the first reason. A booking that was over could be given up, which changed the record. Taking a hall out of use left its bookings ahead in place, so people still went there. A booking could be made for a time already gone. The out-of-use check ran outside the lock, so a booking could slip in during retirement. A clash that ran over two days showed only the end hour.
- Impact: A teacher could lose the hall to a colleague's mistake, and the record of who gave it up could be wrong.
- Reproduction: As a teacher with "book facility", open Facilities and give up a booking another teacher made.
- Resolution: Only the person who booked, or a facilities manager, may give a booking up. The booking is read again under a lock, and one that is over is refused. Taking something out of use gives up every booking ahead with a reason, and keeps the past. It can be brought back into use. Times gone by are refused. The page now works through Livewire, with change and bring-back actions, and the old routes are gone.

## Shelving several copies crashed when a numbered barcode was taken

- Status: Fixed
- Area: Library shelving (`ShelveLibraryCopies`, `LibraryShelvingForm`)
- Observed: Only the first barcode was checked. Shelving four copies from "BOX" when "BOX-2" was already on the shelf gave a server error. A long barcode numbered past 60 characters did the same. The book picker only listed the first 200 books, and the same ISBN could be described twice.
- Impact: The librarian lost the form. A big catalogue could not be used, so books were described again.
- Reproduction: Shelve a copy with barcode BOX-2. Then shelve 4 copies of any book from barcode BOX.
- Resolution: Every numbered barcode is checked first, and the error names the ones taken. A long barcode is refused. The book is found by a search of title, author or ISBN, and a known ISBN points to the existing book. Shelving now works through Livewire, and the POST route is gone.

## A library fine with three decimals crashed the rules page

- Status: Fixed
- Area: Library lending rules (`LibraryLendingRulesForm`)
- Observed: Typing a late fine such as 10.005 gave a server error, because the money library cannot round it into kobo. Two first saves at the same moment could also collide on the one-row-per-campus rule.
- Impact: The librarian saw an error page and lost what they typed.
- Reproduction: Open Library, Lending rules. Type 10.005 in "What one late day costs". Save.
- Resolution: The fine allows two decimals at most and says so on the field. The rules save with one upsert per campus. The page now works through Livewire, and the PUT route is gone.

## A library hold was lost to a stale screen, the nightly run, or a withdrawn copy

- Status: Fixed
- Area: Library queue (`CloseReservation`, `WithdrawLibraryCopy`, `LibraryReservationQueue`)
- Observed: Taking a reservation off from a screen opened before the copy was collected turned the loan's reservation into "Cancelled" and cleared its copy. The nightly hold clean-up did the same to a hold collected while it ran. Withdrawing a copy that was held behind the desk left the reader waiting for a copy that no longer existed; the desk refused to lend it.
- Impact: The queue lost its record of who collected what, and a reader at the front of the queue could wait for ever.
- Reproduction: Open the queue. Lend the held copy at the desk. Take the reservation off from the first screen. Or withdraw a copy that shows under "Behind the desk".
- Resolution: A reservation is read again under a lock before it closes, and one that has ended is refused. The nightly run skips such a hold. Withdrawing a copy now puts its reader back at the front of the queue and holds the next free copy for them. The queue and the withdraw button now work through Livewire, and the POST and DELETE routes are gone.

## Two taps on "Take it back" charged a late fine twice

- Status: Fixed
- Area: Library lending desk
- Observed: `ReturnLoan` checked that the copy was out before its transaction, using the loan as the page read it. Two returns at once, from a double tap or two tabs, both passed and both posted the fine to the learner's account. `RenewLoan` had the same gap, so two renewals at once could go past the campus limit. The desk also listed every person on the campus in one select, with no way to search.
- Impact: A family was billed twice for one late book, through the ledger that answers what they owe.
- Reproduction: Lend a copy 20 days ago on a campus that fines. Press "Take it back" twice quickly. Two fines appear on the account.
- Resolution: Both actions read the loan again under a lock. The Livewire `LibraryLendingDesk` works from a scan. A copy that is out offers "Take it back" and "Renew". A copy on the shelf offers a search by name or admission number, limited to this campus. The desk finds no copy and no person of another campus. The class set sits behind the ⋯ menu. The loan POST and PUT routes and their requests are gone. `LibraryTest` covers the double return, the stale renewal, and a class set that is short of copies.

## A boarding house could close in the moment a child took a bed in it

- Status: Fixed
- Area: Boarding houses
- Observed: Closing a house checked for boarders without a lock. A bed placement read the house without a lock too. A placement and a close at the same moment could both pass, which leaves a closed house with a child in it. The edit form also closed the house whenever its checkbox was left clear, and the switch gave no sign that boarders stopped it.
- Impact: A closed house drops off the boarding rolls, so the child in it is never checked.
- Reproduction: Close a house in one tab while another tab places a learner in one of its beds.
- Resolution: `ManageBoardingHouse` locks the house before it checks for boarders. `AssignBoardingPlace` reads the house under the same lock. The Livewire `DormitoryForm` opens and changes houses. Its "Takes boarders" switch is locked while anyone sleeps there and says why. The house POST, PUT and DELETE routes and their requests are gone. `BoardingTest` covers closing, reopening, names that clash, and houses on another campus.

## A boarder who did not come back from a night away left every list

- Status: Fixed
- Area: Boarding, nights away
- Observed: "Out tonight" only lists leave that covers today. When the return day passed and nobody recorded the learner back, the leave stayed approved but showed on no list. Two people could also answer one request at once, and the later answer replaced the earlier one without a word. A night already under way could be called off, which records a learner out of the building as in it. The learner list offered day learners, who are always refused.
- Impact: A child who never came back to the house disappears from the screen staff read at lights out. A family told "yes" could find the request marked refused.
- Reproduction: Approve a night away from today to tomorrow. Do not record the learner back. Two days later the learner is on no list.
- Resolution: The Livewire `OvernightLeaveDesk` lists overdue learners first under "Not back yet". `DecideOvernightLeave` reads the request again under a lock and names the answer given first. It refuses to approve nights that have passed. It refuses to call off a night already under way, and refuses to record a learner back who has not left. Only boarders are offered. A refusal needs a reason. The house can call off a night but cannot approve one. `OvernightLeaveDeskTest` covers these paths.

## Two staff taking one boarding roll erased each other's answers

- Status: Fixed
- Area: Boarding rolls
- Observed: The roll page sent every row on each save, so a save carried the answers as the page first read them. When two staff split a house by floor, the second save put the first person's boarders back to "Not recorded". A save could also land after another person completed the roll, because the action checked completion before it took a lock.
- Impact: A boarder marked present could show as not recorded, or a finished roll could change after it closed. On a curfew roll this hides where a child is.
- Reproduction: Open one roll in two tabs. Mark boarder A present in the first tab and save. Mark boarder B late in the second tab and save. Boarder A is back to "Not recorded".
- Resolution: The Livewire `BoardingRollSheet` saves only the rows this person changed and shows the answers others saved. A row someone else answered since the sheet was read is refused once, with what they recorded. A save after completion is refused with nothing written. `RecordBoardingRoll` reads the roll again under a lock. Completing with a boarder unaccounted for says so. The `BoardingRollBoard` starts rolls without a page reload. It starts none for a future day or a closed house, and it shows today for a date it cannot read. `BoardingRollTest` covers these paths.

## A five-digit year crashed any form with a date

- Status: Fixed
- Area: Every form with a date field
- Observed: PHP reads `20266-09-02` as a real date (2006 at 20:26), so the `date` rule passed it. MySQL then refused the value and the request failed with a 500.
- Impact: One extra key press in a date box broke the save with no message. This is easy on a phone keyboard or a pasted value.
- Reproduction: Type `20266-09-02` as the start of a calendar day, or as the due date of an assessment, and save.
- Resolution: The `date` rule now also requires a year from 1000 to 9999, the range MySQL holds. The check reads the year as typed, so it applies to every form that uses the rule. `DateYearValidationTest` covers ordinary, day-first, written and broken dates.

## An assessment's maximum could drop below a mark already given

- Status: Fixed
- Area: Gradebook setup
- Observed: Changing an assessment from 20 to 10 points was accepted after a learner had 18. Their result then went above 100%. Setup was also a set of plain forms that reloaded the page on each change.
- Impact: Results were wrong without any warning, and could be sent for approval in that state.
- Reproduction: Give a learner 18 out of 20, then change the assessment's maximum to 10.
- Resolution: The Livewire `GradebookSetup` refuses a maximum below the highest mark and names that mark. It also keeps an assessment's kind fixed, refuses a category or scale from elsewhere, and refuses a category or template name already in use. The mark sheet follows new and removed assessments at once. The setup POST routes and requests are gone. `GradebookSetupTest` covers these paths.

## Two teachers could overwrite each other's marks, and a result could be sent twice

- Status: Fixed
- Area: Gradebook
- Observed: Each mark was its own form and a full page reload. A teacher who opened the gradebook, then saved a mark after a co-teacher had changed it, replaced the co-teacher's mark without a sign. Pressing submit twice queued two identical revisions for approval. An approver could also approve revision 1 after the teacher had sent revision 2.
- Impact: Marks were lost without anyone knowing. Approvers saw duplicate work, and an old result could become official over a correction.
- Reproduction: Open one gradebook in two tabs. Save a mark in one, then a different mark for the same learner in the other.
- Resolution: The Livewire `GradebookMarkSheet` takes the marks of one assessment down the class list and saves them in one press. It remembers every mark as it read it. A mark someone else changed since then shows their value and needs a second save. `PublishResult` refuses a result that already waits for approval unchanged, and `ApproveResult` refuses a revision that a newer one replaced. The per-mark and per-result POST routes are gone. `GradebookMarkSheetTest` covers these paths.

## An open tab kept writing after a feature was turned off or the school was switched

- Status: Fixed
- Area: Livewire screens, school features, and working school
- Observed: Feature switches and the active-school check ran only on page addresses. A Livewire screen already open in a tab sent its actions to the Livewire endpoint, which skipped both. A person who worked at two schools could open a form in school A, switch to school B in another tab, and save. The record went into school B.
- Impact: A school that turned off events, boarding, library or another feature could still get records through an open tab. A person with two schools could file a record in the wrong school without any sign of it.
- Reproduction: Open `Add a day` on the calendar. Turn events off, or switch the working school in a second tab. Press save in the first tab.
- Resolution: `EnsureFeatureIsEnabled` and `RequireActiveSchool` now run as Livewire persistent middleware, so each action obeys the rules of the page it came from. Each component also remembers the school it was drawn for. An action from another school gets a 409 page that tells the person to reload. `StaleLivewireTabTest` covers the three paths.

## Academic levels were too dense on mobile

- Status: Fixed
- Area: Academic levels and school setup
- Observed: The hierarchy opened every nested level on phones. Repeated action rows and setup-empty messages made the page excessively long and difficult to scan.
- Impact: Staff had to scroll through the full school structure before reaching the grade they wanted to manage.
- Reproduction: Open `/dashboard/academic-levels` at a phone-sized viewport with multiple nested levels.
- Resolution: The top-level structure remains visible while nested levels collapse by default on mobile and remain expanded on desktop. Redundant per-grade setup-empty messages were removed; the level summary and actions communicate the available next step. Browser QA verified the mobile disclosure behavior.

## Learners and families could not see graduation progress or club places

- Status: Fixed
- Area: Graduation plans, programmes, and learner portal
- Observed: Graduation progress and programme participation were available only on staff screens. Neither module could be switched off in school settings, and the family portal had no enrollment-scoped view for these records.
- Impact: Learners and guardians could not follow published graduation requirements or see club and activity schedules. Staff also had no school-level switch to hide either module.
- Reproduction: Open a plan or programme as school staff, then sign in as a learner or guardian and inspect the campus links. The portal had no corresponding routes.
- Resolution: Both modules are now school-switchable. Learners and guardians can read graduation progress for plans that apply to their enrollment, and student-facing clubs/activities for that enrollment. Portal routes enforce person, campus, module, and area access; staff notes and intervention/support programmes are not exposed. Inactive and withdrawn enrollments are refused. `PortalAcademicActivityTest`, `PortalOverviewTest`, and `FeatureSettingTest` cover these paths.

## Academic-level groups repeated the whole-group teaching explanation

- Status: Fixed
- Area: Academic levels
- Observed: Each group row said whole-group teaching was available in its tag and repeated the same idea in the summary, alongside the child-level count. The page lead also repeated setup details already covered by the help tooltip.
- Impact: The hierarchy became harder to scan, especially on mobile, where each group row used extra lines before its child levels and actions.
- Reproduction: Open `/dashboard/academic-levels` with one or more level groups and inspect the group rows on desktop or mobile.
- Resolution: Group rows now show a simple `Group` tag and one summary with the child-level count and teaching capability. The page lead and ordering hint are shortened. `AcademicStructureScreenTest` checks the user-facing summary and guards against the repeated explanation.

## Portal report-card downloads omitted the academic year

- Status: Fixed
- Area: Learner and family historical documents
- Observed: Staff report-card history showed the academic year, but the learner-facing report-card document showed only `Period: Term 3`.
- Impact: A learner or guardian with records from multiple years could not identify the year of a downloaded report card when period names repeated.
- Reproduction: Sign in as a learner or guardian, open an enrollment's Documents page, download a report card, and inspect the standalone document header.
- Resolution: Portal report-card documents now load and display the academic year beside the period. The existing transcript document already includes `academic year · academic period` for every result. Coverage verifies the year is present in the learner download while portal ownership boundaries remain enforced.

## Historical report cards were ambiguous across academic years

- Status: Fixed
- Area: Report-card history and navigation
- Observed: Report-card filters and rows showed only the period label, so “Term 1” from different academic years looked identical. The records themselves were year-bound, but the user-facing history did not expose that context.
- Impact: Staff could select or review the wrong year when comparing historical report cards, especially after a learner had attended the school for multiple years.
- Reproduction: Publish cards for two years that both contain a period named “Term 1”, then open `/dashboard/report-cards` and inspect the period filter and published-card rows.
- Resolution: Report-card history now provides an academic-year filter, labels period options as `academic year · period`, shows the academic year in each row and detail summary, and rejects mismatched or cross-school year/period selections. Coverage includes normal, duplicate-period alternate, mismatch exception, and cross-school exception flows.

## Historical gradebooks were not discoverable

- Status: Fixed
- Area: Gradebook history and navigation
- Observed: The Gradebooks workspace always queried only the working academic year and period. A staff member could open an older gradebook only if they already had its direct course-offering URL.
- Impact: Historical marks and result history were technically present but difficult to find, and a multi-period historical year could not be reviewed from the gradebook list.
- Reproduction: Open `/dashboard/gradebooks`, then try to select a prior academic year or a different period.
- Resolution: The workspace now provides academic-year and period filters, supports all-period historical views, labels each row with its period and lifecycle state, preserves the working-period default, and rejects cross-school or cross-year period selections. Coverage includes normal, historical, all-period, and invalid-selection flows.

## Closed gradebooks still presented editable controls

- Status: Fixed
- Area: Gradebook usability and academic-period lifecycle
- Observed: A gradebook in a closed reporting period still rendered assessment setup and mark-entry forms. The backend correctly rejected new work, but the screen did not explain the locked state until after a failed submission.
- Impact: Teachers could waste time filling controls that could not be saved, and the page did not clearly separate historical results from current editing work.
- Reproduction: Open a course offering whose reporting period is closed, such as `/dashboard/course-offerings/41/gradebook`, and inspect the assessment setup and learner rows.
- Resolution: Closed gradebooks now show a read-only status banner and summary badges, hide edit controls, keep historical marks/results visible, collapse the workflow guidance, and use a bordered sticky-header grid for long assessment lists. The controller also includes the academic-period status in its eager-load projection so the new state-aware view cannot fail at render time; the summary badges use the card's supported title slot. Screen coverage includes open, closed, and invalid-write paths.

## Exam detail links opened a permanent 404

- Status: Fixed
- Area: Exams and assessment scheduling
- Observed: The exam list generated a detail URL for each exam, but the resource controller's `show` action always aborted with a 404. Staff could create an exam but could not follow its normal detail path to manage exam slots.
- Impact: The assessment workflow stopped after exam creation, leaving exam-slot scheduling unreachable from the user-facing route.
- Reproduction: Create an exam, open `/dashboard/exams/{exam}`, and follow the expected exam detail or slot-management flow.
- Resolution: The exam detail action now renders the existing exam-slot workspace. Regression coverage verifies that an authorized user receives the workspace and can continue to slot management.

## Subject rollover rejected periods with different display labels

- Status: Fixed
- Area: Academic year setup and subject rollover
- Observed: The 2027–28 calendar showed Term 1, Term 2, and Term 3 in the edit form, but its display labels were Autumn, Winter, and Spring. Rolling subjects from 2026–27 reported that the matching reporting period did not exist, even though the period name, type, position, and hierarchy matched.
- Impact: A school could not carry its curriculum into a new year when the new calendar used different presentation labels.
- Reproduction: Open `/dashboard/course-offerings/roll-forward?source_academic_year_id=1&target_academic_year_id=2` with matching period names and different period labels, then review the rollover.
- Resolution: Course-offering rollover now matches reporting periods by stable name, type, position, and parent hierarchy. Display labels are presentation metadata and no longer prevent a valid rollover. Regression coverage includes normal, alternate-label, and missing-period exception flows.

## Production syllabus demo seeder crashed while generating a fake file name

- Status: Fixed
- Area: Demo-school asset creation
- Observed: Running `php artisan db:seed --class=Database\\Seeders\\SyllabusSeeder --force --no-interaction` on Laravel Cloud first failed in `SyllabusFactory.php:22` with `Attempt to read property "name" on null`, then reached `CourseOfferingFactory.php:57` with `Call to undefined function Database\\Factories\\fake()`. No syllabus records were created.
- Impact: The school simulation could not populate syllabus records, so curriculum and learner portal QA could not exercise a populated syllabus workflow.
- Reproduction: Run the existing `SyllabusSeeder` in the production runtime.
- Resolution: The demo seeder now uses existing open or scheduled school offerings and writes deterministic records and PDF placeholders idempotently, so production asset creation does not traverse test-only Faker factories. Cloud command `comm-a2aa40a4-68f3-46bf-ba2f-79f0c9860c37` completed successfully after deployment `171c923e`; live admin and teacher syllabus pages returned 200 and rendered six demo rows, while the learner page returned 200 with the expected scoped empty state.

## Portal roles could read unrelated active notices

- Status: Fixed
- Area: Learner and family notices
- Observed: A learner or guardian with the built-in `read notice` permission could open the workspace notice list, which used the school-wide active-notice query instead of the recipient records used by the portal.
- Impact: A notice aimed at another class, role, or named learner could appear outside its intended audience.
- Reproduction: Publish two active notices, deliver only one to a learner or guardian, then open `/dashboard/notices` and each notice detail route as that portal user.
- Resolution: Portal-role notice lists and detail views now require a published notice with a recipient record for the signed-in account. Staff notice management retains its current school-wide permissions. Regression coverage checks delivered, undelivered, and draft exception paths. Deployed in `a64a94b3`; the live learner and guardian workspaces returned 200 with scoped empty states after release.

## Portal roles could open staff curriculum workspaces

- Status: Fixed
- Area: Learner and family role boundaries
- Observed: On the live site, a student account with the built-in `read syllabus` and `read timetable` permissions could open `/dashboard/syllabi` and `/dashboard/timetables`. The timetable component scoped the learner's rows, but the syllabus list queried the whole school. A guardian account also had the staff-oriented syllabus and timetable navigation available through the seeded read permissions.
- Impact: Learners could be shown curriculum records outside their roster, and guardians could reach staff workspace screens that are not child-scoped.
- Reproduction: Sign in as a student or guardian, open the Syllabi or Timetables workspace directly, and inspect the rendered list and filters. Use a second course offering, section, unpublished syllabus, or draft timetable to verify whether unrelated records are exposed.
- Resolution: The learner timetable route remains a published, own-section view; learners see only published syllabi for active offerings on their roster; guardian access to these staff workspaces is denied and the sidebar and command-palette entries are removed. Regression coverage exercises normal, alternate, and exception paths. Deployed in `a64a94b3`; learner Syllabi and Timetables returned 200 with their scoped empty states, and guardian Syllabi and Timetables returned 403 after release.

## Portal links ignored disabled top-level school tools

- Status: Fixed
- Area: Parent and learner portal
- Observed: Disabling Library on the live School features screen changed the summary to 9 of 10 tools and made the administrator Library route return 404, but the parent overview still rendered two Library links.
- Impact: Families could see a link to a school service that the school had turned off. The direct portal route was also not consistently gated by the top-level feature.
- Reproduction: Turn off Library at `/dashboard/schools/features`, then open `/dashboard/portal/overview` as a guardian with two enrollments.
- Resolution: `PortalAccess::areaIsOpen()` now requires the matching top-level Attendance, Events, Library, or Boarding feature before it evaluates the portal-area setting. Regression coverage covers the overview link, direct Library route, and all four mapped features. Production verification disabled and restored each feature: links disappeared, direct routes returned 404, and all routes returned after restoration. The focused PHPUnit process remains queued behind a pre-existing stalled run.

## Repeated queries on setup pages

- Status: Fixed
- Area: Academic setup and level pages
- Observed: Debugbar reported 52–86 queries, duplicate query groups, and N+1 groups on setup requests. The sidebar alone repeated feature-setting reads for each optional tool.
- Impact: Setup pages could become slow as school data grew.
- Resolution: The academic structure tree eager-loads each section's academic year, and `FeatureManager` now loads applicable school and platform settings once per request with school settings taking precedence. The sidebar regression test measured 16 feature-setting queries before the fix and at most 2 after it.

## Full test suite baseline failures

- Status: Fixed
- Area: Automated QA
- Observed: The baseline full suite completed with 1,270 passed, 129 failed, and 5 skipped tests. Failures included academic calendar screen expectations, academic-period context API mismatches, and other feature-level failures outside the importer and shared edit form.
- Impact: The repository-wide QA baseline was not green, so feature changes could not be trusted across the application.
- Resolution: The academic-period context contract, shared user edit rendering, finance fixtures, school setup assertions, and current route/label expectations were fixed. A fresh isolated run completed with 1,455 tests, 4,393 assertions, zero failures, zero errors, and five explicit skips.

## Feature tests cannot run concurrently against the shared testing database

- Status: Fixed
- Area: Automated QA infrastructure
- Observed: Running multiple PHPUnit files at the same time caused each process to migrate or drop the same MySQL `testing` database. This produced missing-table and table-already-exists errors.
- Impact: Parallel test results are invalid and can leave the testing schema half-migrated.
- Resolution: PHPUnit now acquires a process-wide lock in `tests/bootstrap.php`, so concurrent agent runs wait and execute sequentially against the shared `testing` database. PHPUnit child processes do not reacquire their parent’s lock, so separate-process tests cannot deadlock. The lock is released automatically when the process exits.

## Notice links allowed protocol-relative destinations

- Status: Fixed
- Area: Notice editor and rendered notice content
- Observed: The notice sanitizer allowed an `href` beginning with `//`, which can navigate a reader to an external host while looking like a local link.
- Impact: Notice authors could create misleading external navigation in learner and family portals.
- Reproduction: Save a notice containing `<a href="//example.com">Open</a>` and inspect the stored content.
- Resolution: The allow-list now accepts only `http(s)`, `mailto:`, root-relative single-slash, and fragment links. All other link destinations keep the anchor text but lose the `href`; disallowed attributes remain removed. PHPUnit coverage exercises safe links and unsafe protocol-relative and JavaScript links.

## Partial user profiles emitted PHP 8.5 deprecation warnings

- Status: Fixed
- Area: Profile avatars and eager-loaded user summaries
- Observed: Cloud command output reported `trim(): Passing null to parameter #1 ($string) of type string is deprecated` from `User::defaultProfilePhotoUrl()` when a user was loaded with only selected columns such as `id` and `name`.
- Impact: Normal user-summary rendering could pollute production logs and make PHP 8.5 deprecation handling noisy for incomplete or partially selected accounts.
- Reproduction: Build a user model with a missing name or email value, or eager-load `User::query()->select(['id', 'name'])`, then read `profile_photo_url`.
- Resolution: Avatar generation now casts optional profile values to strings and declares its string return type. PHPUnit coverage verifies an incomplete profile produces a usable avatar URL without deprecation warnings.

## Family-request answer validation was not shown in the school inbox

- Status: Fixed
- Area: Family requests
- Observed: An administrator who selected `Answered` without entering a response was redirected back with the request still unchanged, but the inbox did not show the validation message. The page only rendered errors under `status`, while the required response error is keyed as `response`.
- Impact: Staff could think the answer was saved or be left without a reason why the request remained open.
- Reproduction: Open `/dashboard/portal-requests`, select `Answered` for an open request, leave `The answer` empty, and submit.
- Resolution: The inbox now renders the first validation error for any failed status change. PHPUnit coverage verifies the message appears after the failed answer attempt. Live verification confirmed the request remained unchanged before the valid in-review and answered transitions.

## Health-record validation errors were not shown on the edit screen

- Status: Fixed
- Area: Health records
- Observed: Submitting a health record with 5,001 characters in `Anything else` redirected back with the previous value unchanged, but the edit screen showed neither the validation error nor a success message.
- Impact: Staff could not tell why an emergency record was not saved and could repeatedly submit invalid information without feedback.
- Reproduction: Open `/dashboard/health-records/1`, enter more than 5,000 characters in `Anything else`, and save. Confirm the record remains unchanged and inspect the returned page.
- Resolution: The health-record edit screen now renders a page-level destructive alert with the first validation error, and health fields are excluded from flashed input so a large failed value cannot overflow the production cookie session. PHPUnit coverage exercises the valid write, a subsequent invalid update with the cookie session driver, the visible error, and preservation of the last valid value. Live verification confirmed the invalid write was rejected and role boundaries remained intact: administrator 200, parent 403, student 403.

## The committed test suite could not run

- Status: Fixed
- Area: Test suite
- Observed: `tests/Feature/CourseOfferingRollForwardTest.php` was committed in Pest style. It called `uses(TestCase::class, RefreshDatabase::class)`, `beforeEach()`, and `it()`. PHPUnit stopped the whole run with "Pest must be run through its own binary".
- Impact: Every test in the project failed to start, not only this file. The committed `composer.json` lists PHPUnit and no Pest, so CI could not run any test.
- Reproduction: Run `vendor/bin/sail php vendor/bin/phpunit`. The run stops before the first test.
- Resolution: The file is now a PHPUnit class. It extends `Tests\TestCase`, uses `FeatureTestTrait` and `RefreshDatabase`, moves the shared setup into `setUp()`, and turns the two global helper functions into private methods. The three roll-forward tests pass. The full suite now runs: 1478 tests.

## Gradebook page two dropped the year and period filter

- Status: Fixed
- Area: Gradebook history and navigation
- Observed: `GradebookController::index()` reads `academic_year_id` and `academic_period_id`, then paginates 25 rows. The paginator built its links without the query string, so page two returned to the working year and period.
- Impact: A school with more than 25 offerings in an older year could not read past the first page of that year. The historical gradebook browsing feature stopped at row 25.
- Reproduction: Open `/dashboard/gradebooks?academic_year_id=<older year>` in a school with 26 or more offerings in that year, then select page 2.
- Resolution: The query now ends with `->withQueryString()`. A new test creates 26 offerings in a historical year and asserts both filter names appear in the rendered pagination links. The test fails without the fix.

## Nested buttons inside links broke keyboard navigation

- Status: Fixed
- Area: Organization workspace
- Observed: `resources/views/pages/organization/show.blade.php` wrapped eight `<april:button>` elements in `<a href>` tags. HTML does not allow a button inside a link.
- Impact: A mouse click reached the link, but keyboard users could not follow it. Enter and Space activated the button, and the button did nothing. Screen readers announced two nested controls for one action.
- Reproduction: Open an organization page, move focus to `Members` with the Tab key, and press Enter.
- Resolution: All eight are now `<april:button-link>`, the component the project already uses for this. A repository-wide search found no other nested pair.

## Form controls without names were unreadable by screen readers

- Status: Fixed
- Area: Accessibility
- Observed: A live audit of the production site found form controls with no accessible name. The course list held 50 unnamed selects, one pair per row. The grading-scale screen held unnamed grade option fields. The financial-period form held three unnamed inputs, two of them bare date fields.
- Impact: A screen reader announced "combo box" or "edit text" with no name. A person could not tell which row a select belonged to, or which date field was the start.
- Reproduction: Open `/dashboard/course-offerings`, `/dashboard/grading-scales`, or `/dashboard/fee-invoices` and read the controls with a screen reader or an accessibility inspector.
- Resolution: The teacher and role selects now name their subject. Grade option fields now carry their option number, including the rows Alpine adds after load. The financial-period form now uses the project's visible `april:label` pattern for `Period name`, `Starts on`, and `Ends on`.

## The school switcher used the same id twice

- Status: Fixed
- Area: Sidebar navigation
- Observed: `april:sidebar` renders its header twice, once for the desktop rail and once for the mobile drawer. The school switcher gave its select `id="sidebar-school-switcher"`, so the id appeared twice on every page of the application.
- Impact: A duplicate id is invalid HTML. The mobile drawer's `<label for>` pointed at the hidden desktop select, so tapping the label focused a control the reader could not see.
- Reproduction: Open any dashboard page with more than one school and run `document.querySelectorAll('#sidebar-school-switcher').length` in the console. It returned 2.
- Resolution: The label now wraps the select instead of pointing at it by id, so no id is needed. The select keeps an `aria-label`.

## Five list screens used the wrong pagination view

- Status: Fixed
- Area: Visual consistency
- Observed: Five screens called `->links()` with no view name, so Laravel rendered its stock Tailwind paginator. That paginator hard-codes `bg-white` and `text-gray-600`.
- Impact: On the dark theme the row count text computed to `oklch(0.373 0.034 259.733)` against a `rgb(12, 18, 15)` background. That contrast is below the WCAG AA minimum, and the control did not match the rest of the site.
- Reproduction: Open `/dashboard/cash-deposits`, `/dashboard/expenses`, `/dashboard/course-offerings`, `/dashboard/reports`, or `/dashboard/academic-cycle-sections` with more than one page of rows.
- Resolution: All five now call `->links('components.pagination-links-view')`, the project's own paginator. Livewire tables keep `components.datatable-pagination-links-view`.

## Four fee-invoice-record routes always answered 404

- Status: Fixed
- Area: Routing
- Observed: `Route::resource('fees/fee-invoices/fee-invoice-records', ...)` registered seven routes. The controller's `index`, `create`, `show`, and `edit` methods each called `abort(404)`. Records are edited from the invoice screen, and no view links to those four routes.
- Impact: Four dead URLs sat in the route table behind authorization checks. Any reader of `route:list` had to open the controller to learn they did nothing.
- Reproduction: Run `vendor/bin/sail artisan route:list --path=fee-invoice-records`.
- Resolution: The resource now declares `->only(['store', 'update', 'destroy'])`, and the four stub methods are deleted.

## A project rule described a bug that does not exist

- Status: Fixed
- Area: Project rules
- Observed: `.ai/rules/queries.md` said an `or` inside a `whereHas` closure escapes the relation's join and matches every row. The rule showed SQL without a group. The installed Laravel version runs the closure through `callScope()`, which groups every condition the closure adds.
- Impact: The rule sent readers to rewrite correct code. Following it, this review first changed a working query in `ListAccountInvitations` and wrote a comment describing a leak that could not happen.
- Reproduction: Run `vendor/bin/sail artisan tinker --execute 'echo App\Models\AccountInvitation::query()->whereHas("user", fn ($u) => $u->where("name","LIKE","%a%")->orWhere("email","LIKE","%b%"))->toSql();'`. The output reads `and (name LIKE ? or email LIKE ?)`.
- Resolution: The rule now states the grouped behaviour, shows the real SQL, and tells readers to confirm with `toSql()` before calling a query a leak. It keeps the warning for an `or` chained outside a closure, where the risk is real. The query in `ListAccountInvitations` is back to its committed form. Two search tests were added, because the invitation search had no coverage at all.

## The profile screen showed two nationality fields

- Status: Fixed
- Area: User profile
- Observed: `update-profile-information-form.blade.php` draws its own `Nationality` input, then embeds the `nationality-and-state-input-fields` component, which drew a second one. Both used `id="nationality"` and `name="nationality"`.
- Impact: The screen asked for nationality twice. The second field was under the `Address` heading and had no `wire:model`, so anything typed into it was discarded without a message. The repeated id also made the page invalid HTML, and both `<label for="nationality">` elements pointed at the first input.
- Reproduction: Open `/user/profile` and run `document.querySelectorAll('[id=nationality]').length` in the console. It returned 2.
- Resolution: The component now takes a `showNationality` flag, and the profile form passes `false`. The two screens that rely on the component to draw the field, `create-user-fields` and `edit-user-fields`, keep the default. Three tests cover the flag and assert the profile screen holds exactly one nationality field. The count test fails without the fix.

## A submitted form stayed dead after the reader went back

- Status: Fixed
- Area: Forms, whole application
- Observed: The global submit handler in `resources/js/app.js` sets `data-submitting="true"` on the form and disables every submit button, to stop a double submit. Nothing ever cleared that state.
- Impact: The browser restores a page from its back-forward cache exactly as it was, and `wire:navigate` restores its own snapshot the same way. The form came back with its buttons disabled and its guard flag set, so the reader could not submit it again. Only a hard reload freed it. This hit every form in the application.
- Reproduction: Submit any form, then press the browser Back button. Run `document.querySelector('form[data-submitting]')` in the console. It returned the form, and its submit button stayed disabled.
- Resolution: `releaseSubmittedForms()` clears the flag, re-enables the buttons, and removes `aria-busy`. It runs on `pageshow` when the page came from the cache, and on `livewire:navigated`. Verified in Chrome against the built bundle: after a submit the button is disabled and the flag is set; after a restore both are cleared.

## Four screens warned about deleting things that are not deleted

- Status: Fixed
- Area: Destructive actions
- Observed: The submit handler asks for confirmation before any `DELETE` form. A form that carries no `data-confirm` gets the default text, "Delete this item? This action cannot be undone." Four screens relied on that default for buttons that do not delete a record.
- Impact: The warning did not describe the action. "Take out of use" only deactivates a facility and keeps its bookings. "Give it up" cancels one booking. "Withdraw" takes a library copy off the shelves. "Take it off" removes a queue place. A reader who trusted the warning expected permanent deletion in all four cases.
- Reproduction: Open `/dashboard/facilities`, `/dashboard/library`, `/dashboard/library/queue`, or `/dashboard/grading-scales` and press any destructive button.
- Resolution: Each form now carries `data-confirm` text that names its own action. Three tests assert the exact wording on the four screens. All four fail without the fix, because no `data-confirm` existed on any of these pages before.

## Every dashboard page held two main landmarks

- Status: Fixed
- Area: Page structure, whole application
- Observed: `april:sidebar-inset` renders a `<main>` element. `layouts/app.blade.php` then rendered its own `<main id="main">` inside it.
- Impact: HTML does not allow a `<main>` inside another `<main>`. A screen reader announced two main regions on every dashboard page, so "jump to main content" landed the reader in the wrong one.
- Reproduction: Open any dashboard page and run `document.querySelectorAll('main').length` in the console. It returned 2, and the second was a descendant of the first.
- Resolution: The inner element is now a `<div id="main" tabindex="-1">`. It keeps the same classes, so nothing moves. `tabindex` lets it accept focus, which a `<div>` cannot do on its own and which the old `<main>` could not do either.

## The skip link stayed invisible while it held focus

- Status: Fixed
- Area: Keyboard navigation
- Observed: The "Skip to content" link carried only `sr-only`. That class clips the link to one pixel and never releases it.
- Impact: The link is the first stop for a keyboard reader on a page with over a hundred sidebar links. Focus moved to a control nobody could see, so a sighted keyboard reader lost track of the focus position and could not tell the link was there.
- Reproduction: Open any dashboard page, press Tab once, then look at the top left corner. Nothing appeared.
- Resolution: The link now carries `focus:not-sr-only` with position, padding, background, and a focus ring. Verified in Chrome: while blurred the link measures 1x1 and is clipped; once focused it measures 125x36 at the top left corner, with `clip-path: none`. Three tests in `AppLayoutTest` cover the single landmark, the focusable target, and the focus style. All three fail without the fix.

## Link buttons were painted in a colour nobody could read

- Status: Fixed
- Area: Colour contrast, whole application
- Observed: April UI paints its `link` button variant with `text-primary`, and the rich text editor paints its links the same way. `--primary` is a surface colour in this theme, not a text colour: a light tan on the light background, a dark brown on the dark one.
- Impact: The text landed at 1.59:1 on the light theme and 1.73:1 on the dark one. WCAG AA asks for 4.5:1. The colour is close enough to the page background that the link is hard to find at all. This hit every "link" button in the application, including several primary actions in table rows.
- Reproduction: Open any page with a link button and measure the computed colour against the page background. Light theme: `rgb(214, 187, 164)` on `rgb(235, 241, 234)`. Dark theme: `rgb(84, 54, 28)` on `rgb(12, 18, 15)`.
- Resolution: `resources/css/app.css` repaints these links with `--primary-foreground`, the readable half of the same colour pair. It holds the same brown hue, so the links keep their look. They now measure 12.32:1 on the light background and 8.11:1 on the dark one. The rule sits outside every `@layer`, because a layered rule loses to a Tailwind utility whatever its specificity. Three tests cover it, including one that fails the day April UI changes the variant itself.

## Calendar weekday headers were too faint to read

- Status: Fixed
- Area: Colour contrast, calendar screens
- Observed: The weekday row on the calendar grid, and the header row of the calendar template table, paired `text-muted-foreground` with a `bg-muted/50` surface.
- Impact: On the light theme the pair measures 4.39:1. WCAG AA asks for 4.5:1 on normal text. The gap is small enough to look fine in a screenshot and still fail a reader with low vision. The affected text is the column label, so a reader who cannot make it out cannot tell which column is which day.
- Reproduction: Open `/dashboard/calendar-events` on the light theme and measure "Mon" against the strip behind it. Foreground `rgb(85,99,90)`, background `rgb(210,217,206)`.
- Resolution: The three header rows now use `text-foreground`. A test walks every Blade view and fails if any line pairs `bg-muted/50` with `text-muted-foreground`, so the combination cannot come back anywhere. The test fails without the fix.

## Destructive text was unreadable on the dark theme

- Status: Fixed
- Area: Colour contrast, whole application
- Observed: `text-destructive` reads `--destructive`. On the dark theme that red measures 4.3:1 on a card, 3.73:1 on a muted surface, and 3.61:1 on an accent surface, against the 4.5:1 minimum.
- Impact: Destructive text names the actions a reader most needs to read before pressing, such as "Delete scale". The light theme passes at 4.51:1, so the fault only shows for readers on the dark theme.
- Reproduction: Open `/dashboard/grading-scales` on the dark theme and measure "Delete scale". Foreground `rgb(225,70,70)`, background `rgb(21,27,22)`.
- Resolution: `--destructive` doubles as the background of destructive buttons, so lightening it would repaint those buttons. The application adds `--destructive-text` instead. The light theme keeps the shared token. The dark theme raises the lightness to 68%, which clears 4.5:1 on every dark surface the application uses: 6.46:1 on the page background, 5.97:1 on a card, 5.17:1 on a muted surface, and 5.00:1 on an accent surface. Five tests cover it, including one that fails the day April UI gives destructive text its own readable colour.

## The sidebar was not a navigation landmark

- Status: Fixed
- Area: Screen reader navigation, whole application
- Observed: The sidebar holds over a hundred links, grouped under headings. It rendered as plain `<div>` elements, so it was not a landmark.
- Impact: A screen reader reader moves between landmarks to skip past a menu. With no landmark the sidebar was not in the list, so the only way past it was to read through every link on every page.
- Reproduction: Open any dashboard page and run `document.querySelectorAll('nav, [role=navigation]')` in the console. Only the pagination trail came back.
- Resolution: The sidebar content slot now carries `role="navigation"` and `aria-label="Main"`. april merges slot attributes onto the wrapper it already draws, so no element was added and nothing moved. The label separates it from the pagination trail. april draws the sidebar twice, once for the desktop layout and once for the mobile sheet, and only one is visible at a time, so both carry the landmark. A test asserts both are present and fails without the fix.

## Every screen announced two top-level headings

- Status: Fixed
- Area: Page structure, whole application
- Observed: The top bar marked the product name as an `<h1>`. `layouts/app.blade.php` draws a second `<h1>` for the page heading.
- Impact: A screen reader reader who lists the headings on a page found two at the top level, and the first was the word "Skuul" on all 48 screens. The name gave no clue which page was open, and the real page heading came second.
- Reproduction: Open any dashboard page and run `document.querySelectorAll('h1').length` in the console. It returned 2 on every page.
- Resolution: The product name is now a `<span>`. It keeps its size and weight, so nothing moves. The link around it already carries `aria-label="Home"`, so no label was lost. A test asserts one `<h1>` per screen and fails without the fix.

## Two controls were too small to press on a phone

- Status: Fixed
- Area: Touch target size, whole application and the calendar
- Observed: A sweep of all 48 dashboard screens at a 390px viewport measured every control. The sidebar toggle rendered at 18x28px on 44 screens. The add-day links on the calendar grid rendered at 12x12px, 35 of them on one screen.
- Impact: WCAG 2.2 asks for 24x24px. The sidebar toggle is the only way to reach the menu on a phone, so a reader who misses it cannot navigate at all. The toggle asks for `size-7`, but it sits on a flex line beside a `min-w-0` block, so a narrow screen squeezed it below its own width.
- Reproduction: Open any dashboard page at 390px wide and measure the toggle. It returned 18x28. Open `/dashboard/calendar-events` and measure a plus icon. It returned 12x12.
- Resolution: The toggle now carries `shrink-0`, so the flex line cannot squeeze it. The calendar plus icon is now wrapped in a padded 24x24 link that also carries an `aria-label` naming the day. Neither change moves anything on a wide screen. Two tests cover them, and both fail without the fix.

## Phone fields opened a letter keyboard on a phone

- Status: Fixed
- Area: Mobile data entry, people forms and health records
- Observed: A sweep of all 107 dashboard route shapes read every form control. The school form and the organization form already set `type="tel"` on their phone field. The create-person form, the edit-person form, the profile form, and the emergency contact number on a health record left theirs as plain text.
- Impact: A phone shows the letter keyboard for a text field. The person must switch to the number layer for every digit. The emergency contact number is the worst case: it is typed by a nurse who is often standing next to the child.
- Reproduction: Open `/dashboard/students/create` on a phone and press the phone field. The letter keyboard opens.
- Resolution: The four remaining phone fields now carry `type="tel"`. The health record view takes an optional `type` per field, so the other three fields on that card stay plain text. The profile phone field also carries `autocomplete="tel"`, because it is the reader's own number. The other people forms record somebody else's number, so they are deliberately left without autocomplete. Two tests cover it. One walks every Blade view and fails if any phone field lacks `type="tel"`, so the fault cannot come back. Both fail without the fix.

## Every row on a list said the same thing to a screen reader

- Status: Fixed
- Area: Screen reader navigation, twenty list screens
- Observed: A sweep of all 107 dashboard route shapes grouped the links on each screen by the name a screen reader reads. Fifteen screens held a row action whose name repeated on every row and pointed somewhere different each time. The worst were 25 links called "Open gradebook", 25 called "Edit roster", 20 called "Open", and 20 called "View".
- Impact: A screen reader can list every link on a page. That list read "Open, Open, Open" twenty times over, with nothing to say which record each one opened. The only way to tell them apart was to leave the list and read the table row by row.
- Reproduction: Open `/dashboard/health-records` and run `[...document.querySelectorAll('main a')].map(a => a.textContent.trim())`. Twenty entries came back reading "Open".
- Resolution: Twenty views now give the row action an `aria-label` naming the row: "Open the health record for Ada Bell", "Open the gradebook for Mathematics, Year 7". The visible text is unchanged, so nothing moves and the button stays short. Two tests cover it. One walks every Blade view and fails if a link inside a loop carries a generic name with no `aria-label`, so the fault cannot come back on a new screen. Both fail without the fix.

## The keyboard focus ring was almost invisible

- Status: Fixed
- Area: Keyboard navigation, whole application
- Observed: April UI binds `--ring` to `--primary`. That token is a surface colour, so the ring the browser draws is nearly the colour of the page behind it. On the light theme it measured 1.59:1 on the page background, 1.71:1 on a card, 1.39:1 on a muted surface and 1.33:1 on an accent. On the dark theme it measured 1.73:1, 1.62:1 and 1.39:1. WCAG 2.2 asks for 3:1 on a focus indicator.
- Impact: A reader who moves by keyboard could not see where the focus had gone. Every control on every screen was affected, including the login form and each row action on a list.
- Reproduction: Open any page, press Tab, and read the focused control. `getComputedStyle` returned a ring of `rgb(84,54,28)` on a `rgb(12,18,15)` page. A screenshot showed no visible ring at all.
- Resolution: `resources/css/app.css` binds `--ring` to `--primary-foreground` in both themes. That is the readable half of the same colour pair and holds the same brown hue, so the ring keeps its look. It now measures 12.32:1 on the light background and 8.11:1 on the dark one, and the worst surface in either theme is 6.37:1. Three tests cover it, including one that fails the day April UI gives the ring its own readable colour.

## The breadcrumb trail named the same page three different ways

- Status: Fixed
- Area: Navigation, twenty-eight screens
- Observed: The breadcrumb trail is a list of page names, but the older screens wrote those names by hand and never agreed. The administrator list was called "Administrators" on its own screen, "Administrator" on the create screen, and "admins" on the edit screen. Twenty-three crumbs started with a small letter, so a trail read "Dashboard > students > Edit Ada Bell". Twelve more crumbs said only "Create" or "Edit" and never named the page.
- Impact: A reader uses the trail to learn where they are and to go back one step. A crumb that reads "students" on one screen and "Students" on the next looks like two different places. A crumb that reads only "Create" says nothing about what is being created, and it is the one crumb the reader lands on. Screen readers read the trail aloud, so the mismatch is heard as well as seen.
- Reproduction: Open `/dashboard/students`, then open a student and press Edit. The trail changed from "Dashboard > Students" to "Dashboard > students". Open `/dashboard/fees/create`. The trail ended in "Create".
- Resolution: Every crumb now carries the name of the page it points at, taken from that page's own heading. A crumb that names an action now names the record class with it: "Create student", "Create fee invoice". Four page headings were reworded to match, including "Create Fees Invoice", which was not grammatical. A bare "Edit" is still allowed where the crumb before it names the record, because "Classes > Kindergarten 1 > Edit" reads well. Two tests cover it. They walk every Blade view, so a new screen cannot bring the fault back. Both fail without the fix.

## Five checkboxes were painted by the browser, not by the app

- Status: Fixed
- Area: Visual consistency and target size, four screens
- Observed: Five checkboxes carry no class of their own, so Chrome paints them itself. On the grading scale screen the box beside "Available for new assessments" rendered bright blue at 13x13, on a page whose every other accent is warm brown. The same screens also hold hand-painted checkboxes of the same meaning, 16x16 and brown, so one screen showed two kinds of the same control.
- Impact: The blue is the operating system accent colour, so it changes from reader to reader and belongs to no theme. On the dark theme it reads as a control from another program. At 13px it was also the smallest target in the application, three pixels under every other checkbox.
- Reproduction: Open `/dashboard/grading-scales` and read the checkbox. `getBoundingClientRect` returned 13x13 and `accentColor` returned `auto`, which Chrome resolves to its own blue.
- Resolution: `resources/css/app.css` sets `accent-color` and a 1rem size on every native checkbox and radio. The hand-painted ones set `appearance: none`, and a browser ignores `accent-color` once it does, so they are untouched. Every sized checkbox in the app already measures 1rem, so nothing else moves. Three tests cover it, including one that fails if a new view asks for a size the repaint would override.

## Every Livewire form stopped working after its first submit

- Status: Fixed
- Area: Forms, nine forms across seven screens
- Observed: The bundle guards against a double submit. On submit it flags the form and disables every submit button inside it. Two things undo that flag: the browser restoring a cached page, and a `wire:navigate` visit. A `wire:submit` form does neither. Livewire cancels the native submit and answers over AJAX, so the page never moves and neither event ever fires. The button stayed disabled and the flag stayed set.
- Impact: The reader pressed the button once and it went dead. Nothing said why, and the only way back was a full page reload. This hit the promotion and graduation screens, the timetable time slot form, the academic calendar form, the organization member form, the three status forms on a student profile, and every profile screen that uses the shared form section.
- Reproduction: Open `/dashboard/students/promote` and submit the "Load students" form. The button came back with `disabled` still set and `aria-busy="true"` still on it. Pressing it again did nothing. The same state was reproduced in the live page by dispatching a cancelable submit on a copy of the form: `{"disabled":true,"flag":"true"}` before and after a Livewire round trip.
- Resolution: `resources/js/app.js` now registers a Livewire `commit` hook and releases the form when the request comes back, on both the response and the failure path. The release only touches forms carrying a `wire:submit` attribute, so a plain form still stays disabled while it navigates and cannot be sent twice. The fix was proven in the live page: the same stuck form read `{"disabled":false,"flag":null}` once the hook was in place. Five tests cover it, three of which fail without the fix.

## A refused field said nothing on the control itself

- Status: Fixed
- Area: Forms, 46 screens
- Observed: The application refuses a field by printing a red line under it. Nothing joined the two. No view in the application used `aria-invalid`, and only two used `aria-describedby`, against 166 error blocks across 48 views.
- Impact: A screen reader read the control as if nothing were wrong. It gave the label, then the value, then moved on. The message sat on the page as loose text with nothing to say which field it belonged to, so a reader who could not see the layout had no way to tell. On a long form the reader was told the form was refused and then had to hunt for the field.
- Reproduction: Open any create screen, submit it empty, and read the refused control. `getAttribute('aria-invalid')` returned null and `getAttribute('aria-describedby')` returned null, while the message was on the page.
- Resolution: A refused control now carries `aria-invalid="true"` and points at its own message with `aria-describedby`. The message carries the matching id. Three helpers in `app/helpers.php` work that id out, so both sides always agree. 162 error blocks became `<x-field-error name="..." />`, which renders the same markup and adds the id. 61 native controls carry `{{ field_error_bindings('...') }}`. `april:input`, `april:native-select` and `april:textarea` read their own `name` or `wire:model` binding, so 72 more controls needed no view change at all; that took three small overrides under `resources/views/vendor/april/components/`, because a Blade directive inside an `<april:*>` tag breaks April's tag precompiler. Three tests cover it and two fail without the fix. `.ai/rules/views.md` records the convention.
- Not covered: 13 controls that use `april:select`, `april:combobox` or `april:editor`. See the next entry.

## The April select and combobox were not announced as selects

- Status: Fixed
- Area: Forms, 12 controls
- Observed: `april:select` and `april:combobox` build their own menu out of a plain `<button>` and a `<div role="listbox">`. The trigger carried no `role="combobox"`, so nothing said the button was a select. The `april:select` list was never tied back to its trigger and never said whether it took more than one answer. An earlier reading of this recorded `aria-expanded`, `aria-controls` and `aria-haspopup` as missing as well. That was wrong: April binds all three from Alpine at runtime, so they were never absent from the live control, only from the view file that was read.
- Impact: A screen reader announced a button. It did not say a list of options sat behind it, and on a multiple-choice select it did not say more than one answer was allowed.
- Reproduction: Open a screen with an `april:select` and read the trigger. It was a `<button type="button">` with no role of its own.
- Resolution: Fixed in April UI 1.2.6. The select trigger and the combobox search field carry `role="combobox"`. The select list points back at its trigger with `aria-labelledby` and reports `aria-multiselectable`, and the trigger carries the id that link needs. A rendering test and a browser test cover it in the package.
- Still open: these 12 controls carry no `aria-invalid` or `aria-describedby` when a field is refused, unlike the controls in the entry above. The composite control would have to read the error state itself.

## Every card and alert title skipped a heading level

- Status: Fixed
- Area: Page structure, 45 dashboard screens
- Observed: A sweep of 45 live dashboard pages found 33 skipped heading levels: 32 read `h1` then `h3`, and one read `h1` then `h5`. The page heading is the only `h1`. April UI fixed a card title at `h3`, an alert title at `h5`, a dialog and sheet title at `h4`, and a dropdown menu label at `h6`. Two application views added to it: the command palette labelled its dialog with an `h2` that sat before the page `h1`, and a toast notification titled itself with an `h5`.
- Impact: A screen reader reader moves through a page by heading. When the level jumps from 1 to 3 that reader cannot tell whether a section was missed or the page simply left a number out, so they stop trusting the outline and read the whole page instead. WCAG technique G141 asks for heading levels that step down one at a time.
- Reproduction: Open any dashboard screen and read the headings in order. `/dashboard/cohorts` gave `h2` (command palette), `h1` (page), `h3` (card), `h3` (card), `h5` (toast). `/dashboard/calendar-events` gave `h1` then `h5` for "1 event is still a draft".
- Resolution: Fixed in April UI 1.2.6. Card, alert, dialog-header and sheet-header now render their title as an `h2`, one level under the page heading, and take a `level` prop for a title that belongs inside a section. A dropdown menu label names a group of items inside `role="menu"`, so it is now a `div` and stays out of the heading order. On the application side the command palette labels its dialog with a `<p>`, because `aria-labelledby` takes any element, and the toast titles itself with a `<p>`, because `role="alert"` reads the whole message out and a message that disappears does not belong in the heading list. Three tests walk real screens and fail on any skipped level.

## Adding a fee to an invoice crashed the screen

- Status: Fixed
- Area: Finance, the create fee invoice screen
- Observed: The waiver box on a fee row carried `:max="amount"`. Blade reads a leading colon as a PHP expression, not as an Alpine binding, so the view compiled to a constant named `amount`. The row threw "Undefined constant \"amount\"" as soon as it rendered. A stray bare `x-bind` attribute beside it suggests somebody had already tried to work around this.
- Impact: The screen worked until the first fee was added, then the whole Livewire component threw and the reader saw an error page. No invoice could be raised from this screen at all. The waiver cap the binding was meant to apply never worked either.
- Reproduction: Open the create fee invoice screen and press "Add Fee(s)". Rendering the component with one added fee throws a `ViewException`.
- Resolution: The binding is written in Alpine's long form, `x-bind:max="amount"`, which Blade passes through untouched. `tests/Feature/ControlNameTest.php` renders the component with a fee added; it throws without the fix.

## A form control in a table row did not say what it was for

- Status: Fixed
- Area: Forms, three screens
- Observed: Seven controls sat in table cells with no name of their own. The house roll gave each boarder a status select and two text boxes, the create fee invoice screen gave each fee an amount, a waiver and a fine box, and the gradebook reject box carried only a placeholder. A column heading names the column. It is not the accessible name of a control inside the column.
- Impact: A screen reader read a row as an unlabelled select and two unlabelled boxes, repeated once per boarder or per fee. Nothing said which row was being filled in. WCAG 2.1 SC 1.3.1 and 4.1.2 both ask for a name on the control itself.
- Reproduction: Open a house roll with one boarder and read the status select. It had no `aria-label`, no `aria-labelledby`, and no label pointing at an id.
- Resolution: Each control names its row and its column, for example `aria-label="Status for Ada Bello"`. This follows the convention the gradebook mark boxes already used. `tests/Feature/ControlNameTest.php` renders all three screens and fails on any control in a table cell that nothing names.

## Removing a fee moved typed amounts onto the wrong fee

- Status: Fixed
- Area: Finance, the create fee invoice screen
- Observed: The added-fee rows and the added-student rows carried no `wire:key`. Livewire matches rows by position when no key says otherwise. A typed value lives on the DOM node, not in the markup Livewire sends, so removing a row from the middle left the typed amounts where they were while the fee ids under them shifted up by one.
- Impact: The reader removed one fee and the amounts, waivers and fines they had already typed silently moved onto the fees below. The `name` attributes moved with the markup, so the wrong figures were posted under the wrong fee ids. The running total in the last column showed the same wrong numbers, so nothing looked out of place.
- Reproduction: Add three fees, type an amount into each, then remove the first. The second row kept the first row's amount.
- Resolution: Both loops key their rows by record id, so Livewire moves the right row. `resources/views/livewire/assign-students-to-parent.blade.php` rebuilds its rows on a Livewire action too and is keyed the same way.

## Every destructive button asked the same vague question

- Status: Fixed
- Area: Forms, 15 screens
- Observed: `resources/js/app.js` asks the reader to confirm any form carrying the DELETE method. Fifteen hand-written forms gave it no message, so all fifteen read "Delete this item? This action cannot be undone." Three of them do not delete anything: returning a campus to the default calendar, ending a boarding placement and revoking an invitation all use the DELETE method for a change that is not a deletion. A sixteenth form, the fee record on the edit invoice screen, already asks in its own dialog, so the reader was asked twice in a row.
- Impact: The question named nothing, so a reader with several houses, budgets or domains on screen could not tell which one they were about to lose. Worse, three actions that are reversible announced themselves as permanent deletions, and one asked twice, which teaches a reader to click through the question without reading it.
- Reproduction: Open `/dashboard/organizations/{id}/domains` and press "Give it up". The browser asked "Delete this item? This action cannot be undone." with no mention of the domain.
- Resolution: Each of the fifteen forms carries a `data-confirm` that names what it undoes, for example "Give up boarding.example.test? Nobody reaches the school at this address afterwards." The handler now honours `data-confirm="false"`, so the fee record form, which has its own dialog, asks once. That dialog names the fee instead of saying "this resource". `tests/Unit/DestructiveFormConfirmationTest.php` fails on any new DELETE form without a message of its own.

## Two boxes on the edit invoice screen shared one id

- Status: Fixed
- Area: Finance, the edit fee invoice screen
- Observed: Each fee row rendered a waiver box and a fine box, both with `id="name-{id}"`. Both labels pointed at the same id. The rows also carried no `wire:key`.
- Impact: Pressing the "Fine" label put the cursor in the waiver box, and a screen reader read the fine box as "Waiver". A duplicate id also breaks any script that looks a box up by id.
- Reproduction: Open the edit screen for an invoice with one fee and read the two ids. Both were `name-` followed by the same record id.
- Resolution: The boxes take `waiver-{id}` and `fine-{id}`. The "fine" label reads "Fine", like every other label on the row, and each row carries a `wire:key`.

## Removing a paid fee deleted the record of where the money went

- Status: Fixed
- Area: Finance, fee invoice records
- Observed: `FeeInvoiceRecordService::deleteFeeInvoiceRecord()` refused to remove a line from a posted invoice, but allowed one that already had money against it. `payment_allocations.fee_invoice_record_id` is declared `cascadeOnDelete`, so the database removed the allocations with the line. The sibling method `updateFeeInvoiceRecord()` guards this exact case, which shows the author had it in mind for a change but not for a removal.
- Impact: The `student_payments` row kept its full amount while the allocations that said what it settled were gone. The invoice then reported less paid than the family had handed over, so the office asked for money it already held. Nothing in the audit trail said the allocations had ever existed.
- Reproduction: Raise an invoice, take a payment against it, then press "Delete" on the paid fee row. The line and its allocations both went, and the invoice showed the fee as unpaid.
- Resolution: The service refuses to remove a line whose `paid` is positive and says to raise the waiver instead. The edit screen no longer offers the button on such a row; it explains what has been paid. Its dialog now names what the family stops owing instead of promising that payments stay on record, which was never true. `tests/Feature/FeeInvoiceRecordTest.php` takes a payment and then fails if the line can still be removed.

## Three tables showed a heading over blank space

- Status: Fixed
- Area: Finance and the family portal
- Observed: The fee table on the invoice screen and both tables in the printed portal document looped without an empty branch. An invoice can reach zero fees, because every line can be taken off. A report card can be published before any subject is marked, and the view already wrote `payload['results'] ?? []`, which says the author knew the list could be empty.
- Impact: The reader saw column headings with nothing under them and no way to tell an empty record from a screen that had failed to load. On a printed report card a family got a blank grid with no explanation.
- Reproduction: Open an invoice with no fee lines, or download a report card whose payload holds no results.
- Resolution: All three loops use `@forelse` and say what is missing: "No fees are on this invoice yet.", "No subject was marked in this period." and "No subject has been marked yet." `tests/Feature/FeeInvoiceTest.php` and `tests/Feature/PortalTest.php` cover them.

## Deleting a taught subject broke every course offering screen

- Status: Fixed
- Area: Academics, the subject catalog
- Observed: `SubjectService::deleteSubject()` removed a subject with no check on what taught it. `Subject` uses soft deletes, so the row stayed but the `subject` relation on a course offering read null. Every screen that names a subject reads it straight off that relation, for example `{{ $courseOffering->subject->name }}` on the course offerings index and at the top of every gradebook.
- Impact: One press of "Delete subject" took the course offerings index, the gradebook list and every gradebook screen to a 500 error. Nothing on the subject screen said the subject was in use, and nothing pointed at the subject as the cause once the screens had gone.
- Reproduction: Create a subject, give it a course offering, delete the subject, then open `/dashboard/course-offerings`. The page returned 500 with "Attempt to read property name on null".
- Resolution: The service refuses to delete a subject that has a course offering and says to close its offerings first. The catalog hides the delete action on any subject whose offering count is above zero, so the screen no longer offers an action the server will refuse. `tests/Feature/SubjectTest.php` deletes a taught subject, then opens the course offerings index.

## A row action was offered where the server would refuse it

- Status: Fixed
- Area: Tables, `x-table-actions`
- Observed: `x-table-actions` rendered every action on every row. A row where the action could not work still showed the button, and the reader only learned that after pressing it.
- Impact: A reader who pressed such a button was bounced back with a message and no change. Repeated often enough, that teaches a reader to distrust the row actions.
- Reproduction: Open the subject catalog with one taught subject. The delete action showed on it, and pressing it did nothing but raise an error.
- Resolution: An action may carry a `when` key holding an Alpine expression over `row`, for example `row.course_offerings_count === 0`. The control is hidden on rows the expression rejects. The expression is worked out in a `@php` block, because a Blade directive inside an april tag breaks its precompiler.

## Deleting a fee or its category broke the screens that named it

- Status: Fixed
- Area: Finance, the fee catalog
- Observed: `FeeService::deleteFee()` and `FeeCategoryService::deleteFeeCategory()` both deleted with no check on what pointed at them. Both models soft delete, so the row stayed while every `belongsTo` back to it read null. `show-fee-invoice.blade.php` and `edit-fee-invoice-form.blade.php` read `$record->fee->name`, `ListFeesTable` reads `$fee->feeCategory->name`, and `FeePolicy` reads `$fee->feeCategory->school_id` on every check.
- Impact: Deleting a fee that was on an invoice took both invoice screens to a 500 error, so the invoice could no longer be read or corrected. Deleting a category took the whole fees index down, and the policy threw on any single fee, so even opening one fee failed. This is the same fault as the taught subject, in the two places above it.
- Reproduction: Raise an invoice carrying one fee, delete that fee from the catalog, then open the invoice. The page returned 500.
- Resolution: A fee that is on an invoice and a category that holds fees are both refused, with a message saying what to clear first. Both tables hide the delete action on rows that are in use, using the `when` key added to `x-table-actions`. `tests/Feature/FeeTest.php` and `tests/Feature/FeeCategoryTest.php` delete a record that is in use, then open the screen that reads it.

## Removing a student took down the fee invoices index

- Status: Fixed
- Area: Finance, invoices and receipts
- Observed: `StudentService::deleteStudent()` soft deletes the person. `FeeInvoice::user()` and `StudentRecord::user()` were plain `belongsTo` relations, so both read null once the person was removed. `ListFeeInvoicesTable` reads `$invoice->user->name` on every row, and `show-fee-invoice.blade.php` and `edit-fee-invoice-form.blade.php` both read it at the top of the screen.
- Impact: Removing one student took the fee invoices index to a 500 error for the whole school, so no invoice could be read or paid. Every screen that names a learner through their enrollment fell back to the admission number, so a report card, a boarding roll and a payment receipt all stopped naming the person they were about.
- Reproduction: Raise an invoice for a student, remove the student, then open `/dashboard/fees/fee-invoices`. The page returned 500 with "Attempt to read property name on null".
- Resolution: Both relations resolve a removed person with `withTrashed()`. A refusal is wrong here: a school may remove a student, and the invoices raised for them are real financial history that has to keep naming them. `tests/Feature/FeeInvoiceTest.php` removes an invoiced student, then opens the invoice, its edit screen and the index.

## A deleted invoice broke the policy on every line it carried

- Status: Fixed
- Area: Finance, invoice lines
- Observed: `FeeInvoice` soft deletes, but `FeeInvoiceRecord` does not, so a line outlives the invoice it sat on. `FeeInvoiceRecordPolicy` reads `$feeInvoiceRecord->feeInvoice->school_id` on all three of its checks, and that relation read null once the invoice was gone.
- Impact: Any request touching a line whose invoice had been deleted returned a 500 from the policy, before the request reached a controller. A policy is the wrong place to fail: it should decide yes or no, not throw.
- Reproduction: Raise an invoice with one fee, delete the invoice, then send a DELETE to that line's route. The policy threw "Attempt to read property school_id on null".
- Resolution: `FeeInvoiceRecord::feeInvoice()` resolves a deleted invoice with `withTrashed()`, so the policy reads the school and answers normally. `tests/Feature/FeeInvoiceRecordTest.php` covers it.

## Money on the finance screens did not name its currency

- Status: Fixed
- Area: Finance and the family portal
- Observed: A model column cast to `Money` formats itself and prints "NGN 250,000.00". A figure that arrives as a plain number, such as a sum or a `decimal` column, had nothing to format it, so ten screens rendered money with `number_format($amount, 2)` and no currency at all.
- Impact: The fee invoices index showed "1,897,000.00" in its summary cards directly above a table showing "NGN 147,000.00", so the same page disagreed with itself about the same money. The budget register, the expense register, the cash deposit list and both ledger account balances did the same. Worst of all, the family portal told a parent they still owed "250,000.00", with nothing saying in what.
- Reproduction: Open `/dashboard/fees/fee-invoices`. The four cards carried bare numbers while the table under them carried the currency.
- Resolution: A new `money_text()` helper in `app/helpers.php` formats a plain amount through `Brick\Money`, the same way the `Money` cast does, so both routes read identically. All ten renders use it. It takes a major amount, rounds half up so a float sum cannot throw, and treats null as zero. `tests/Feature/FeeInvoiceTest.php` and `tests/Feature/PortalInvoiceScreenTest.php` both fail if a bare figure comes back.

## A section select named "A" without saying which class

- Status: Fixed
- Area: Calendar, timetables, course offerings
- Observed: A section is named "A" or "B" inside its class, so the name means nothing on its own. Five screens rendered `$section->name` with no class beside it: the calendar event audience checkboxes and the audience column on the calendar index, the class select on the timetable screen, the class select on the course offering edit screen, and the clash message from `FacilityAvailability`.
- Impact: Choosing who a calendar event is for meant picking between three boxes all labelled "A". The reader could not tell JSS 1 A from JSS 2 A, so an event could be sent to the wrong class with nothing on the screen to catch it. The room clash message named a section the reader could not place.
- Reproduction: Open `/dashboard/calendar-events/2`. The audience list showed "A", "B", "A" with no class.
- Resolution: `AcademicCycleSection::qualifiedName()` writes the class and the section together, as "JSS 1 · A", and `displayName()` prefers the school's own label where it set one. The five ambiguous renders use it. The three queries behind them widened their column select and eager load the level, so the accessor cannot throw or lazy load in a list. Sixteen other renders keep the bare name, because a class column already sits beside them. `tests/Feature/CalendarEventScreenTest.php` covers both calendar screens.

## A delete question that named no record

- Status: Fixed
- Area: Every list screen with a row delete action
- Observed: `x-table-actions` wrote a fixed `data-confirm` string on every row of a table, so a page of twenty-five students asked "Delete this student?" whatever row the reader picked. Eighteen row actions did this, across students, teachers, parents, administrators, schools, subjects, fees, fee categories, fee invoices, exams, exam slots, syllabi, notices, school years, timetable items, promotions and graduations.
- Impact: A row action opens from a dropdown, so the row that started it is no longer under the pointer when the question appears. The reader had nothing to check the question against and could only trust that they clicked the right row. On the schools table this deleted a whole school.
- Reproduction: Open `/dashboard/students`, open the row menu on any student and choose Delete. The question read "Delete this student?" with no name.
- Resolution: A row action may carry a `names` key holding an Alpine expression over `row`, and `:name` in its message is replaced with what that expression reads, so the question names the record: "Delete Ada Bello? Their invoices and results stay." All eighteen actions name their record. `tests/Unit/DestructiveFormConfirmationTest.php` fails on a row action that carries no `names` key, and `tests/Feature/ResourceIndexActionTest.php` renders the students table and checks the binding.

## The test suite would not run at all

- Status: Fixed
- Area: Test tooling
- Observed: The in-flight move to Pest 5 and PHPUnit 13 left two faults. `tests/Pest.php` turned on test impact analysis with `pest()->tia()->locally()`, and TIA refuses to run a PHPUnit class. Every test in this project is a PHPUnit class, so a full run stopped on the first file it read. Separately, `tests/bootstrap.php` never defined `PHPUNIT_COMPOSER_INSTALL`, which the child process of a `#[RunInSeparateProcess]` test reads to find the autoloader.
- Impact: `artisan test` with no filter ended in "Tia mode requires Pest tests" and ran nothing, so nobody could check the whole suite. A single file still ran, which hid the fault. The isolated installer test died with `Class "PHPUnit\TextUI\Configuration\Registry" not found`, because its child process loaded no autoloader at all.
- Reproduction: Run `vendor/bin/sail artisan test`. It stopped at `Tests\Unit\BreadcrumbLabelTest` without running a test.
- Resolution: `tests/Pest.php` turns TIA off and says why, so it is not put back. `tests/bootstrap.php` defines `PHPUNIT_COMPOSER_INSTALL`, and its database lock now recognises a child of either binary, so an isolated test does not wait on a lock its own parent holds. `.ai/rules/tests.md` records both.

## Sorting two columns took the page down

- Status: Fixed
- Area: Finance invoices, student promotions
- Observed: A sortable column sorts on the field it names, and the table adds `ORDER BY <field>` to the query. Two columns named a field the row works out rather than a column the table holds: the invoice list sorted on `due_date_label`, a written date such as "Mar 3, 2026", and the promotion list sorted on `learners_count`, counted from a JSON column.
- Impact: Clicking either column heading returned a 500 from MySQL, "Unknown column in order clause". Both headings looked exactly like the ones that work, so the only way to find out was to click.
- Reproduction: Open `/dashboard/fees/fee-invoices` and click the Due date heading.
- Resolution: Both columns sort through a callback on the value the record really holds: the invoice by `due_date`, the promotion by `JSON_LENGTH(students)`. `tests/Feature/FeeInvoiceTest.php` and `tests/Feature/StudentTest.php` sort each column.

## Sorting the exam list on a calendar page took the page down

- Status: Fixed
- Area: School calendars
- Observed: Livewire finds a component's root element by reading the first `<` in its rendered HTML. `livewire/set-academic-period.blade.php` wrapped its whole body in `@if ($academicYear !== null)`, which writes a `<!--[if BLOCK]>` marker before the root element, so Livewire read an empty tag name and stored it against the component.
- Impact: The calendar overview carries the period switcher as a child component. The first render worked, so the screen looked healthy. Any later request from the page then read the stored tag back and threw "Invalid Livewire child tag name", which returned a 500. Sorting, searching or paging the exam list on the working calendar all did this.
- Reproduction: Open the working calendar at `/dashboard/academic-years/{id}` as a user who can set the academic period, then click a heading on the exam list.
- Resolution: The view now opens with an unconditional root element and holds its conditions inside it. `tests/Unit/LivewireRootElementTest.php` fails on any Livewire view that opens with a directive, and `tests/Feature/AcademicYearTest.php` sorts the exam list on the calendar overview.

## Two forms on the student screen did nothing

- Status: Fixed
- Area: Enrollment
- Observed: The student screen carries a form that changes the enrollment status and another that changes the placement. Both call a Livewire method by name: `wire:submit="changeStatus"` and `wire:submit="changePlacement"`. A work-in-progress commit that added the campus move removed both methods from `ShowStudentProfile` and left the forms in place.
- Impact: The forms rendered, took what the reader typed and refused to do anything with it. Livewire answered "Unable to call component method", so nothing was saved and nothing said why. `ChangeEnrollmentStatus` and `ChangeEnrollmentPlacement` were still there, unreachable from the only screen that offered them.
- Reproduction: Open a student, choose a new status under "Change enrollment status" and press Save status.
- Resolution: Both methods are back on the component and report through `notify()` like the campus move beside them. A placement now names the working calendar when the section does not belong to it, rather than throwing a 404 inside a Livewire request. `tests/Feature/EnrollmentStatusTest.php` and `tests/Feature/EnrollmentPlacementTest.php` drive each form through the screen.

## School feature settings repeated the same explanation for every optional tool

- Status: Fixed
- Area: School setup and feature management
- Observed: Every optional tool repeated the same two-sentence explanation that it starts off and that the school decides whether to use it. The page already showed each tool's description and its On or Off state.
- Impact: The settings list became unnecessarily tall and pushed the save action farther down the page. The repeated copy competed with the actual decision and made the mobile layout harder to scan.
- Reproduction: Open `/dashboard/schools/features` and inspect any tool that defaults off.
- Resolution: The page now explains the setting once in its introductory card, then shows only each tool's name, state, and specific description. The save note wraps from the top on narrow screens so it stays aligned with the button. `tests/Feature/FeatureSettingTest.php` checks the concise heading and that the repeated explanation is absent.

## The family overview led with duplicate explanatory copy

- Status: Fixed
- Area: Family portal overview
- Observed: The overview showed a large “Everything in one place” heading followed by a paragraph explaining campus record boundaries before showing the campus cards. Each campus header then repeated that its enrolment was “at this campus”.
- Impact: Families had to read introductory copy before reaching the actions they came to use, and the repeated campus wording added height without helping them choose a page.
- Reproduction: Open `/dashboard/portal/overview` as a learner or guardian with an enrollment.
- Resolution: The overview now uses one short orientation line and puts the campus name and enrollment count directly in each card header. `tests/Feature/PortalOverviewTest.php` checks the concise orientation and removes the duplicate heading.

## A closed gradebook told staff to edit locked work

- Status: Fixed
- Area: Gradebook hierarchy and period lifecycle
- Observed: A closed gradebook showed “Record grades and publish results” and an empty-state instruction to open Assessment setup, even though the setup controls were hidden. A period in the `Closing` state was also presented as fully read-only, although closing periods accept corrections to existing marks.
- Impact: Teachers received contradictory instructions and could not tell whether a missing assessment was a setup problem or a historical fact. Staff could also lose the correction window intended for a period that is closing.
- Reproduction: Open a gradebook with no assessments in a closed period, then open one in a closing period with an existing assessment.
- Resolution: The view now has three visible states: Editing open, Corrections open, and Read-only. New assessment setup is available only while the period accepts new work; existing marks and result workflows remain available during closing; closed and archived periods show historical results without edit actions. `tests/Feature/GradebookScreenTest.php` covers closed and closing states.

## The family request form led with too much explanation

- Status: Fixed
- Area: Family portal requests
- Observed: The request form used a four-line explanation of how messages are handled, then asked “Anything else the school should know” for an optional message.
- Impact: On a phone, the first action was pushed below unnecessary copy and the long label made the form feel heavier than the simple task.
- Reproduction: Open `/dashboard/portal/enrollments/{student}/requests` as a learner or guardian at a narrow viewport.
- Resolution: The form now says what the action does in one sentence, uses “Additional details (optional)”, and keeps the same workflow and privacy meaning. `tests/Feature/PortalRequestScreenTest.php` checks the concise copy and guards against the old wording.

## A long school name was clipped in the page heading on mobile

- Status: Fixed
- Area: Shared dashboard layout and school setup
- Observed: The page heading kept a long school name at its minimum content width while the date stayed beside it. On a narrow screen, the heading extended beyond the content column and the first characters were clipped.
- Impact: Staff could not read the current page title, and the overflow made the header and breadcrumb feel misaligned on the quick-setup screen.
- Reproduction: Open `/dashboard/schools/1/setup/classes` at a 390px viewport for a school with a long name such as “Gentle Touch School for International Science and Creative Arts Campus”.
- Resolution: The shared `h1` now permits shrinking and wraps long text inside the header width. `tests/Feature/SchoolTest.php` checks that long school names use the wrapping classes.

## The facilities introduction explained the timetable in the page lead

- Status: Fixed
- Area: Facilities
- Observed: The facilities page opened with a four-line explanation of moving lessons and timetable conflict rules before showing the catalogue.
- Impact: The primary task—checking or booking a shared space—was pushed down, especially on a phone. The conflict rule belongs beside the booking action where it is needed.
- Reproduction: Open `/dashboard/facilities` on a narrow screen and read the text below “What the campus shares”.
- Resolution: The page lead now identifies shared spaces, vehicles, and equipment in one short sentence. Booking rules remain beside the booking form. `tests/Feature/FacilityTest.php` checks the new lead and guards against the old copy.

## Long report and transcript options pushed selects past the card edge

- Status: Fixed
- Area: Reports and historical academic records
- Observed: The shared native select component has no intrinsic width constraint. Learner and academic-period options therefore sized the select to their longest option instead of the form column.
- Impact: On a phone, report-card and transcript controls extended past the card edge. Labels and buttons appeared aligned, but the input itself was partly off-screen and difficult to use.
- Reproduction: Open `/dashboard/report-cards` or `/dashboard/transcripts` at a 390px viewport with long learner names or academic-year options.
- Resolution: The report-card and transcript selects now use `w-full min-w-0`, and the shared app style lets every native select shrink within its form column. `tests/Feature/ReportCardSnapshotTest.php` and `tests/Feature/TranscriptSnapshotTest.php` check that the constrained controls render.

## Report and transcript forms led with too much policy copy

- Status: Fixed
- Area: Report cards and transcripts
- Observed: Both official-record screens placed a four-line explanation above their first form control. The text repeated the same immutable-record and revision rule in several clauses.
- Impact: On a phone, the user had to pass a large block of policy text before choosing a learner. The rule was important, but the form hierarchy was harder to scan.
- Reproduction: Open `/dashboard/report-cards` or `/dashboard/transcripts` at a 390px viewport.
- Resolution: Each screen now states the record rule in two short sentences. The full workflow and revision behavior remain unchanged. `tests/Feature/ReportCardSnapshotTest.php` and `tests/Feature/TranscriptSnapshotTest.php` check the concise copy and reject the former wording.

## The facilities catalogue squeezed names into narrow mobile columns

- Status: Fixed
- Area: Facilities catalogue
- Observed: The five-column catalogue used all available phone width, so a facility name such as “Science and Innovation Lab” wrapped into several short lines while the other columns stayed visible.
- Impact: Rows became unnecessarily tall and the name column was difficult to scan. The table was technically in an overflow wrapper, but it had no readable minimum width to use.
- Reproduction: Open `/dashboard/facilities` on a 390px viewport with at least two shared facilities.
- Resolution: The catalogue now keeps a readable 640px minimum and scrolls horizontally on narrow screens. `tests/Feature/FacilityTest.php` checks the table constraint.

## The user factory could generate duplicate test emails

- Status: Fixed
- Area: Automated QA fixtures
- Observed: The attendance simulation intermittently failed before exercising the screen because separate `UserFactory` instances could generate the same Faker email. MySQL correctly rejected the duplicate `users.email` value.
- Impact: A valid feature test could fail during setup, making the result look like an attendance regression and reducing confidence in repeated school simulations.
- Reproduction: Run the attendance feature file repeatedly until two factory-created users receive the same generated email.
- Resolution: Test users now receive a UUID-backed `example.test` address, which is unique across factory instances. The focused screen test and the full attendance file both pass.
- [x] Teacher dashboard was empty apart from the academic-year selector. **Status: Fixed.** Shared daily pulse, agenda, upcoming events, and permission-filtered school snapshot panels now render for school staff; organization and school-management context remains restricted. Added normal staff and no-permission coverage in `PlatformPermissionTest`.
- [x] Calendar-only staff could see today’s agenda but not upcoming events. **Status: Fixed.** The upcoming section now follows Calendar permission independently of school-snapshot permissions, while snapshot content remains hidden without a matching data permission.

## Campus moves did not grant the destination student role

- Status: Fixed
- Area: Campus moves and destination-campus access
- Observed: A moved learner received an active destination-school membership, but the student role remained scoped only to the source campus. The destination profile route therefore returned 404 even though the enrollment had moved successfully.
- Impact: Staff at the receiving campus could not open the learner profile, and student-scoped policies could not identify the learner at the new campus.
- Reproduction: Move an active learner between campuses in one organization, switch to the receiving campus, and open the learner profile.
- Resolution: Campus moves and cross-organization transfers now grant the destination-scoped student role while preserving the source role and membership for historical access. `CampusMoveRequestTest` covers the destination role.

## Families could not read official documents after an internal campus move

- Status: Fixed
- Area: Campus moves and family academic history
- Observed: An internal campus move keeps the same enrollment ID but changes its current `school_id`. Published report cards and transcripts remain labelled with the campus where they were issued, so the family portal's current-campus filter hid them and the download check rejected them.
- Impact: A moved learner's family saw “No report cards yet” and “No transcript yet” even when official history existed for that enrollment. The production QA learner had three report-card revisions and two transcript revisions from Dr Fasheun Campus, but the family portal at West Campus showed neither.
- Reproduction: Publish report-card and transcript snapshots at one campus, move the enrollment to a sibling campus, and open the learner's family documents page.
- Resolution: The family portal now finds and validates official documents by enrollment ID. Internal moves preserve that ID; transfers to another organization create a new enrollment ID, keeping unrelated enrollment history isolated. `PortalTest` covers reading and downloading pre-move documents and denies another learner's document.

## Changing campuses returned staff to the page from the previous campus

- Status: Fixed
- Area: School navigation
- Observed: The school picker posted to `schools.setSchool`, whose redirect used `back()`. Selecting a campus from a deep link therefore sent the user back to that route under the new school context.
- Impact: The route could refer to a record that does not exist at the newly selected campus, resulting in a confusing not-found or authorization screen instead of the new campus home.
- Reproduction: Open a record page, change the Working school selector, and observe that the browser returns to the previous record URL.
- Resolution: Successful school changes now redirect to the dashboard. `SchoolContextTest` verifies the redirect from a deep page and denies switching to a school without membership.

## Fee editing posted to the fee-category route

- Status: Fixed
- Area: Fees
- Observed: The fee edit form submitted to `fee-categories.update` instead of `fees.update`.
- Impact: Editing a fee sent its data to a different resource route and could not complete the intended update.
- Reproduction: Open a fee’s edit screen, change its name, and save.
- Resolution: Fee create and edit now submit through Livewire to `FeeService`; feature coverage verifies a successful update and redirect.

## A fee could reference a category from another school

- Status: Fixed
- Area: Fee validation and school boundaries
- Observed: Fee creation validated `fee_category_id` against all fee categories instead of categories belonging to the working school.
- Impact: A crafted request could associate a school's fee with another school's category.
- Reproduction: Submit a fee with the ID of a fee category owned by another school.
- Resolution: The shared fee request rules now scope the category existence check to the working school. The Livewire create action uses those rules, and `FeeTest` covers the cross-school refusal.

## The year-setup structure step was cluttered and over-explained

- Status: Fixed
- Area: Academic year setup (classes and teachers step), academic levels, school setup
- Observed: The step put cards inside cards and repeated helper text in several places. Each level row had four or five spelled-out buttons, and the up/down arrows looked like expand arrows. Every section row showed "No additional details yet". Every Active row carried a badge in the accent colour.
- Impact: Staff could not see at a glance which classes needed a section or a teacher. The accent colour marked everything, so nothing stood out as the next action.
- Reproduction: Open `/dashboard/academic-years/{id}/setup/structure` for a year with several levels and sections.
- Resolution: The structure now reads as one flat list with dividers. Row actions (including reordering) are in one `⋯` menu. Sections show teacher, room, and capacity in aligned columns, and a missing value reads quietly. Only Draft or Archived statuses show a badge. The header counts sections and sections without a teacher. An empty class shows "No sections this year · Add section". Step descriptions, help tooltips, and the "next step" banner were removed, and Continue moved into the bottom bar. `SetupWizardTest` covers the counts, the single row menu, and the empty-class link.

## Publishing an academic year used a regular form post

- Status: Fixed
- Area: Academic year setup (review step)
- Observed: The review step published through a plain POST form to `academic-years.setup.publish`. The review card also declared two footer slots, so the help tooltip footer was lost.
- Impact: The step did not follow the move to Livewire forms, and a refusal reloaded the whole page to show one message.
- Reproduction: Open `/dashboard/academic-years/{id}/setup/review` and press Publish.
- Resolution: The `PublishAcademicYear` Livewire component now publishes the year. It shows a refusal inline, next to the button. The old route and controller action were removed. `SetupWizardTest` covers success, a refusal, and a user without permission.

## The calendar setup step hid an open year's dates and offered a dead remove button

- Status: Fixed
- Area: Academic year setup (dates and periods), academic year create and edit
- Observed: For an open year, the step showed only a warning. The warning mentioned a "calendar overview" but did not link to it or show the year's dates. The editable form repeated the page title in a card and repeated field labels on every period row. It kept a Remove button on the last period, which the server ignores. It marked the chosen structure with the accent colour. It showed Cancel beside the setup page's own navigation.
- Impact: Staff on an open year met a dead end. The editable form was long and hard to scan.
- Reproduction: Open `/dashboard/academic-years/{id}/setup/calendar` for an open year, then open `/dashboard/academic-years/create`.
- Resolution: An open year now lists its dates and periods, with an "Open calendar" link. The form has no card. Periods read as one table with one header row. Structure is a segmented control. Remove is hidden on the last period. Cancel is hidden inside the setup wizard. Empty dates read "No dates". `AcademicCalendarSetupTest` covers the read-only view and the hidden Remove control.

## The dashboard explained itself instead of showing the day

- Status: Fixed
- Area: Dashboard
- Observed: The dashboard opened with a setup banner in the accent colour. The banner had two sentences, a tooltip, and a separate "next priority" box. Next came a "Your school, ready for the day" card that repeated the school and term from the top bar. Each section had an uppercase eyebrow label, and each card and stat had a description line.
- Impact: The numbers staff come for sat below several screens of prose. The accent colour marked a banner, not an action.
- Reproduction: Open `/dashboard` as a school administrator.
- Resolution: Setup is now one line: a progress bar, "7 of 10", the next step, and one accent button. The repeated context card is removed. Organization facts, today's attendance and agenda, the next 7 days, and the school snapshot read as flat sections with plain headings and no description lines. `PlatformPermissionTest` and `SchoolTest` anchor on the section ids.

## Sidebar header did not line up with the navigation

- Status: Fixed
- Area: Application layout (sidebar)
- Observed: The logo and working-school select started 8px left of the nav items and group labels. The "Working school" label used a third indent in a different type style.
- Impact: The sidebar looked ragged at its most visible edge.
- Reproduction: Open any page with the sidebar expanded and compare the left edges of the logo, the working-school select, and the nav items.
- Resolution: April's sidebar content section adds `p-2` on top of each group's `p-2` (april-ui commit 5f5652f), but the header gets only one `p-2`. Our header now adds the missing inner padding, and the label uses the group-label style. The logo, avatar, and nav icons share one centre line. The select and nav items share one left edge.

## The dashboard mixed flat sections with heavy cards and empty panels
- Status: Fixed
- Area: Dashboard, working school year and term bar
- Observed: The dashboard showed flat sections, then a large "Working school year" card with an accent button, then a notices data table in a second card. The attendance chart drew seven empty bars when no register existed. The "Next 7 days" grid left half the row empty. Each page carried a full-width working-term row with an accent "Set working term" button. The dashboard also repeated the school picker from the sidebar.
- Impact: The page looked like scattered boxes. The accent colour marked settings, not actions. The working year and term were set on different screens through plain POST forms.
- Reproduction: Sign in as an admin and open `/dashboard`. Scroll to the bottom.
- Resolution: The top bar now reads "Working [year] [term]". Both selects are Livewire and apply on change. Draft years are not listed. The POST routes `academic-years.set-academic-year` and `academic-periods.set-academic-period`, the `SetAcademicYear` component, `SetAcademicPeriodRequest` and the controller methods are removed. The dashboard now shows the setup strip, one row of plain stat links, "Attendance today" ("No register taken yet." and no chart without data), "Next 7 days" for calendar readers, and up to five current notices with an "All notices" link. The school and year cards, the notices table and the student profile card are removed. Tests: `DashboardTest`, and the updated `AcademicYearTest`, `AcademicPeriodTest`, `AcademicPeriodContextTest`, `AcademicCalendarSetupTest` and `PlatformPermissionTest`.

## The dashboard showed counts but no sense of direction
- Status: Fixed
- Area: Dashboard
- Observed: The dashboard listed today's totals only. Nobody could see whether attendance was slipping, whether fees came in, or whether incidents were rising.
- Impact: Leaders had to open four separate screens to judge the term.
- Reproduction: Open `/dashboard` as an admin with attendance, fee, incident and student permissions.
- Resolution: A deferred "Trends" section draws four charts with April UI's `chart` component: weekly attendance rate (last 4 weeks against the 4 before), fees billed and collected by month, enrolment by class against section seats, and incidents per week. `App\Services\Dashboard\SchoolTrends` groups the rows in MySQL. Each chart needs its own permission and hides without data. Reversed payments, unrecorded register days, restricted incidents and other schools' rows stay out. Test: `DashboardTrendsTest`.

## The school-year page stacked cards inside cards
- Status: Fixed
- Area: School year overview (`academic-years.show`)
- Observed: The page showed a card with four boxed facts, a second copy of the working-term picker, a five-card "How a school year moves" panel with a tooltip, a card per reporting period with three buttons each, and accent-coloured "Open" badges.
- Impact: Fifteen periods filled four screens. The accent colour marked a state, not an action.
- Reproduction: Open `/dashboard/academic-years/1`.
- Resolution: A one-line status stepper replaces the lifecycle cards. Dates, period count and teaching setup sit in one row with the year's actions. Periods are one row each, with Edit dates and Start closing in a single row menu. The duplicate term picker is removed (the top bar has it). Status badges use quiet variants. Tests: `AcademicYearTest::test_the_calendar_overview_lists_periods_with_their_actions_in_one_menu` and the updated lifecycle test.

## The teaching setup page hid its answer under cards and tooltips
- Status: Fixed
- Area: Teaching setup (`academic-years.instructional-model.edit`)
- Observed: The page repeated the year name as a second heading, showed the answer in a card with pills, nested the question in another card, and carried about twelve help tooltips. The mid-year move filled a red card with a boxed confirm. Four plain POST forms saved the answer, the move and the exceptions.
- Impact: The one question on the page was hard to find. Each save reloaded the page.
- Reproduction: Open `/dashboard/academic-years/1/instructional-model`.
- Resolution: A new `ManageInstructionalModel` Livewire component holds the page. The question sits at the top with one status line. The answers are one radio list, and the chosen answer shows what it allows. The mid-year move opens from a "Move" button. Moves and exceptions are plain row lists, and "Take back" asks by subject name. The tooltips, the `instructional-model-answer` and `instructional-model-choice` components, the update, migrate and exception routes, and their three form requests are removed. Tests: updated `InstructionalModelTest`, `InstructionalModelMigrationTest` and `OfferingExceptionTest`, with new tests for taking back an exception and for a short reason.

## School quick setup wrapped each step in a card with a tooltip
- Status: Fixed
- Area: School quick setup (`schools.setup`)
- Observed: Each step sat in a card with a description and a help tooltip. The classes step showed "Add a class or grade" twice, a tinted "First step" or "Next step" box, and a second heading that repeated the year name. Every step indicator carried a sentence under it.
- Impact: The one task on each step was lost among five buttons and three blocks of text.
- Reproduction: Open `/dashboard/schools/1/setup/classes`.
- Resolution: Each step is now one heading and its actions in a row, with one accent button for the next task. The classes step lists the structure tree under "Classes and sections" with an "All classes" link. The intro line, the cards, the tooltips and the step sentences are removed. Test: `SetupWizardTest::test_each_school_setup_step_names_one_task_and_one_main_action`.

## School settings showed the same setup areas twice, in 17 cards
- Status: Fixed
- Area: School settings (`schools.settings`)
- Observed: The page opened with a tinted hero card, then a checklist card with a coloured warning box and a collapsed list. A second collapsed panel held eight cards for the same setup areas, and a "Run the school" section held seven more cards. Each card had a help tooltip, about 16 in all.
- Impact: Staff met the same task in two places and had to open panels to find it.
- Reproduction: Open `/dashboard/schools/settings`.
- Resolution: The page now shows the guided-setup button, then the checklist as open row lists by group. Each row links to its area and shows a reason only while it is still to do. A plain "Day to day" link list replaces the card grid. The accent button turns quiet once the required steps are done. The duplicate cards, the tooltips and the unused count variables in `SchoolController::settings` are removed. Test: updated `SchoolTest`.

## The case page stacked five cards and reloaded for every note
- Status: Fixed
- Area: Discipline case page (`incidents.show`)
- Observed: The page showed a restriction alert with a paragraph, a summary card of four boxed facts, and four more cards with descriptions under each title. People and actions sat in data tables. Each of the four forms (move, action, mark done, note) posted and reloaded the page.
- Impact: The state and the next step sat far apart. Every small update lost the reader's place.
- Reproduction: Open any case at `/dashboard/incidents/{id}`.
- Resolution: A new `ShowIncident` Livewire component shows the reference line, one row of facts, and the "Move the case" control at the top. People, Actions, Notes and History follow as plain lists. Adding an action or a note and marking one done happen in place. The card descriptions, the alert paragraph, the three form requests and the three POST/PUT routes are removed. Tests: updated `IncidentScreenTest` and `IncidentTest`, plus new tests for readers without update permission and for a blank action.

## A boarding house hid its beds in a dialog and reloaded for every change
- Status: Fixed
- Area: Boarding house page (`dormitories.show`)
- Observed: Occupancy was one long sentence. On-duty staff and learners away sat in separate cards. Rooms were a table whose "View" button opened a dialog with three boxed counts, a second table of beds and up to three hidden forms per bed. Adding a room, adding a bed, editing either, ending a placement and giving a bed each posted and reloaded the page, which closed the dialog.
- Impact: The office lost its place after every change, and a phone showed two sideways-scrolling tables.
- Reproduction: Enable boarding, open a house at `/dashboard/boarding/houses/{id}`, open a room and edit a bed.
- Resolution: A new `ShowDormitory` Livewire component shows occupancy as four numbers, on-duty staff as one line and learners away as a list. Rooms open in place to show their beds, and each bed has one row menu (Edit bed, End placement). All six changes happen in place. `App\Actions\Boarding\ManageBoardingRooms` now holds the room and bed changes and their audit records. `DormitoryRoomController`, `DormitoryBedController`, `BoardingPlaceController`, their five form requests, their six routes and the `boardingRooms` Alpine store are removed. Edit house and Archive house sit in one page menu. Tests: updated `BoardingTest`, with new tests for ending a placement, a room that still has boarders, and a read-only reader.

## Student profile stacks three forms and nested cards, and the print view prints the live screen

- Status: Fixed
- Area: Students, people profiles, account passwords, student print view
- Observed: The student page showed one large card with three always-open forms (status, placement, campus move), each with an explanation paragraph, then two more history cards. Every profile had a "Sign-in access" card with a regular POST form. The print view reused the interactive screen, so it printed the "Set password" section and looked sparse.
- Impact: Staff scrolled past forms they rarely use to read the record. The printed record had no structure a school could file or stamp.
- Reproduction: Open `/dashboard/students/{id}` as an administrator, then open its print view.
- Resolution: The profile is now a flat identity block and a facts list. Enrollment shows its facts, and one ⋯ menu opens the status, placement or campus form in place; a pending campus move shows as one line with "Take the request back". Histories and fee invoices are plain lists, and Print moved to the page actions. Setting a password is now the `ManageAccountPassword` Livewire component; the POST route, controller and request are removed. The print view is a dedicated record sheet with bordered sections for personal details, enrollment, guardians, placement and status history, plus a signature line.

## Printed pages show the admin URL, and Back on print pages goes nowhere

- Status: Fixed
- Area: Print layout (student record, fee invoice, receipt, timetable)
- Observed: Printing added the browser's header and footer, which showed the admin dashboard URL on the paper. The Back link used the previous URL, so it did nothing when the print page was opened directly or in a new tab.
- Impact: Printed records given to families exposed an internal admin address. Staff could not leave the print page.
- Reproduction: Open `/dashboard/students/{id}/print` in a new tab, press Back, then print.
- Resolution: The print layout sets a zero page margin, so the browser prints no header or footer, and pads the page instead. Back returns through history when the visitor came from the app, and otherwise opens a page each print view names (student, fee invoice, timetable), falling back to the dashboard.

## Adding a subject to a year is a long POST form full of tooltips and hints

- Status: Fixed
- Area: Course offerings, add subject
- Observed: The form sat in a card with five help tooltips and seven explanation paragraphs. The period list showed every period of every year. Sections were hidden Alpine templates, one per class. Learners were a long multi-select of the whole school.
- Impact: The form was hard to scan, and staff could pick a period from the wrong year.
- Reproduction: Open `/dashboard/course-offerings/create`.
- Resolution: The form is now the `CreateCourseOffering` Livewire component. Choosing a year limits the periods and sections to that year. Choosing a class lists its sections as checkboxes, or its learners for a named roster. Choosing a group switches to "Everyone in {group}". Validation now refuses a period of another year and asks for sections or learners when the roster needs them. The POST route, the controller `store` action and `StoreCourseOfferingRequest` are removed.

## Support plan page is four cards of explanations and four POST forms

- Status: Fixed
- Area: Wellbeing, support plans
- Observed: The plan page stacked two alerts, four cards with explanatory descriptions, fact boxes with sub-captions, and separate POST forms for steps, notes, completing a step and moving the plan. Every change reloaded the page.
- Impact: The plan's steps sat below a screen of text, and each change lost the reader's place.
- Reproduction: Open `/dashboard/support-plans/{id}` as someone who runs the plan.
- Resolution: The page is now the `ShowSupportPlan` Livewire component, laid out like the case page: a summary line (with a "Confidential" mark), a facts row where an overdue review reads "· due" in red, the move form, and then Steps, Notes and History as plain lists. Four routes and three form requests are removed. New tests cover read-only viewers and a finished plan refusing steps.

## Opening a support plan uses two explained cards and a POST form

- Status: Fixed
- Area: Wellbeing, open a support plan
- Observed: The form was split into two cards, each with a description, plus hint lines under the category and review fields. An error from the action showed as a separate alert at the top of the page.
- Impact: The form read as instructions instead of a form, and the error sat far from the field it was about.
- Reproduction: Open `/dashboard/support-plans/create`.
- Resolution: The form is now the `CreateSupportPlan` Livewire component: one plain grid of fields with a Cancel and an "Open the plan" button. Choosing a health or counselling category shows a small "Confidential" mark. Errors show under their fields. The POST route, the `store` action and `StoreSupportPlanRequest` are removed.

## Recording a case uses explained cards and twenty hidden participant rows

- Status: Fixed
- Area: Discipline, record a case
- Observed: The form sat in two cards with descriptions and a hint under the category field. It rendered twenty participant rows up front and hid eighteen with Alpine, each row boxed in its own bordered card. An action error showed as an alert at the top.
- Impact: The page shipped twenty learner lists at once and read as a wall of boxes.
- Reproduction: Open `/dashboard/incidents/create`.
- Resolution: The form is now the `CreateIncident` Livewire component. It starts with one row in a plain "People" list; "Add a person" adds a row and ✕ removes it, up to twenty. Choosing a safeguarding kind shows a small "Restricted" mark. Errors show under their fields. The POST route, `store` action, the participant parser and `StoreIncidentRequest` are removed.

## Student fee account is stacked cards with an always-open reversal form on every payment

- Status: Fixed
- Area: Fees, student account
- Observed: The account page had cards with explanation lines under each figure, an amber box of explanation for other campuses, and four POST forms. Every payment row carried its own open "Why is it being taken back?" field and button. The refund form sat in its own card at the bottom. At 390px the page scrolled sideways.
- Impact: The page read as a wall of fields beside real money. A reversal field on every row made it easy to reverse the wrong payment.
- Reproduction: Open `/dashboard/fees/accounts/{student_record}` with refund access, then narrow the window to 390px.
- Resolution: The page is now the `ShowStudentAccount` Livewire component. Owed and credit held sit in one facts row, and other campuses show as quiet "Owed at …" lines. "Use credit against fees" and "Give money back" appear only when there is credit; the refund form opens in place and confirms before saving. Each payment has a ⋯ menu with Receipt and Take back; Take back opens one reason field for that payment only. Refund amounts must have at most two decimals. The three POST routes and two form requests are removed. The sideways scroll came from an `sr-only` table header escaping its scroll box; that wrapper and five others now have `relative`, and `.ai/rules/views.md` records the trap.

## Fee invoice page was a legacy card with shouting status banners and printed the same whether paid or not

- Status: Fixed
- Area: Fees, invoice page and print
- Observed: The invoice page used an old card layout with "From/To" boxes, a bordered S/N table, totals printed as raw text, and a full-width coloured banner ("This Invoice Has <strong>Not</strong> been Paid"). The actions sat in a separate bar above it. A paid invoice printed as an invoice, and an invoice with no fees read as paid. The print sheet reused the screen component with hand-written float CSS. On the domains page, the "Main" badge broke out of its line.
- Impact: The page was hard to scan and looked unlike the rest of the app. Families got an "invoice" for money they had already paid, with no proof of the payments.
- Reproduction: Open `/dashboard/fees/fee-invoices/{id}` and its print view, for a part-paid invoice and for a fully paid one.
- Resolution: The page now shows the student line with one state badge (Not paid, Part paid, Overdue, Paid or No fees), a facts row (Issued, Due, Charged, Owed), a fee table with totals, and the payments with receipt links. The primary action is "Take payment" while money is owed, and "Print receipt" once the invoice is settled. Print, Edit and Student account are in the ⋯ menu. An invoice due today is not overdue. The print sheet is now a structured document. While money is owed, it is an invoice with an "Amount due" box. Once settled, it is a receipt with a "Paid in full" stamp and the payments received. An April badge renders a `<div>`, so a `<p>` around one split the line; the invoice and domains pages now use a `<div>`, and `.ai/rules/views.md` records this.

## Invoice and promotion lists had no way to open a row

- Status: Fixed
- Area: Fees invoice list, promotions list
- Observed: On `/dashboard/fees/fee-invoices`, the Actions column was empty on every row, and the invoice name was plain text. Nothing on the list opened an invoice. The promotions list had the same empty column.
- Impact: Staff could not reach an invoice from the finance page, so they could not view, edit or take payment on it.
- Reproduction: Open the finance page and look at the Actions column of the invoice list.
- Resolution: The cause was a Blade trap, not April UI. The `:items="…"` attribute of `<x-table-actions>` held a PHP string in double quotes. The inner `"` ended the attribute, so Blade left `<x-table-actions>` in the HTML uncompiled, and the browser drew nothing. Both views now build the items in `@php` and pass `:items="$rowActions"`. The invoice name now links to the invoice. The invoice list filters are plain selects without the card. `BladeComponentTagTest` compiles every view and fails on any component tag left raw, and `.ai/rules/views.md` records the trap.

## Finance page buried the invoice list under cards, a task panel and POST forms

- Status: Fixed
- Area: Finance overview
- Observed: The finance page opened with an explanation line, four summary cards that each carried a description, and a collapsible "Finance tasks" panel with seven buttons. The invoice list sat in its own card below. The financial periods card showed Close and Reopen to every user and had a three-field POST form. An invoice due today counted as overdue.
- Impact: The invoice list, the reason to open the page, sat below the fold. People without the permission saw Close and Reopen buttons that failed with 403.
- Reproduction: Open `/dashboard/fees/fee-invoices` as a user without `manage financial period`.
- Resolution: The page now shows the period name, one facts row (Owed, Overdue invoices, Received, Spent), the invoice list and the periods list. "Add invoice" stays the one primary action. Record expense and the other finance pages are in the ⋯ menu. Financial periods are the `ManageFinancialPeriods` Livewire component. Adding a period opens in place, and Close and Reopen confirm first and show only with `manage financial period`. The three POST routes, `FinancialPeriodController` and `StoreFinancialPeriodRequest` are removed. Overdue now means due before today. `FinancialPeriodScreenTest` covers adding, validation, closing, reopening, permission and school scope.

## Dashboard trends showed collections but not what was owed or spent

- Status: Fixed
- Area: Dashboard trends
- Observed: The only money trend was "Fees collected" against billed. The dashboard did not show how much families still owed, how late that money was, or how the money received compared with expenses.
- Impact: A bursar had to open the finance page and the reports to see which debts to chase first and whether the school spent more than it took in.
- Reproduction: Open `/dashboard` as a user who can read fee invoices and expenses.
- Resolution: Two trends are added. "Owed" totals what is still owed and splits it into not due yet, 1–30, 31–60, 61–90 and over 90 days late. Each line counts only what is left after its allocations, never below zero, and deleted invoices are left out. "Money in and out" compares standing payments with expenses for the last six months and leads with the net. The first needs `read fee invoice`, the second also needs `read expense`, and each stays hidden when it has nothing to draw. `DashboardTrendsTest` covers the buckets, school scope, month totals and permissions.

## Take-payment page was a long carded POST form full of explanations

- Status: Fixed
- Area: Fees, taking a payment
- Observed: The page opened with a paragraph about how balances are worked out, three summary cards, and a card with two headed sub-sections. Every payment method was a large card with a description. Two more radio cards explained how the money is spread. Amounts with three decimals were rounded silently. An error from the payment action, such as naming more than a fee owes, came back as a server error page.
- Impact: Taking money at the counter needed a lot of scrolling and reading. A split the fees could not take lost everything the office had typed.
- Reproduction: Open `/dashboard/fees/fee-invoices/{id}/pay`, enter 10.005, or split more onto one fee than it owes.
- Resolution: The page is now the `TakeInvoicePayment` Livewire component. It shows one facts row (From, Paid, Owed), then amount and date, the payment methods as compact choices, and reference and note. "Split across fees" is a checkbox that shows one amount field per fee that still owes, and only appears when more than one fee is open. Amounts accept at most two decimals. An error from the action shows next to the amount or the fees, and the typed values stay. The POST route and `PayFeeInvoiceRequest` are removed. The tests now drive the component and cover decimals, a refused split and permission.

## Invoice edit page offered fee changes that posted invoices always refuse

- **Where:** `/dashboard/fees/fee-invoices/{id}/edit`
- **Problem:** The page showed carded POST forms to add, change and remove fees. Every invoice made through the create screen is posted to the ledger, so the service refused each of these changes after the form was sent. The add form also sent the fee through an unnamed hidden input. The invoice pages still pointed back to "Fees › Fee Invoices", but that page is now "Finance".
- **Fix:** The edit page is now one Livewire form (`EditFeeInvoiceForm`). It saves the due date and note. It shows the fees of a posted invoice with a lock and no actions. On an unposted invoice, each fee has a ⋯ menu to change or remove it, and "Add fee" opens an inline form. A paid fee cannot be removed. The details Save button drops to outline while a fee form is open, so only one accent button shows. The invoice update route, the fee-line controller and three form requests are removed. The breadcrumbs on the invoice, payment, create and student account pages now read "Dashboard › Finance".
- **Tests:** `FeeInvoiceRecordTest` and `FeeInvoiceTest` use Livewire for every change. A stale Finance page test from the earlier rebuild is updated.

## Finance menu linked to pages the user cannot open

- **Where:** `/dashboard/fees/fee-invoices`, the ⋯ menu
- **Problem:** The menu always listed Record expense, Expenses, Cash deposits, Budgets and more. A user without those permissions got a 403 page after choosing one. A user with none of them still saw an empty ⋯ button.
- **Fix:** Each link is checked against its policy, and the ⋯ button is hidden when no link is left. The cross-school test no longer sends a PUT to the invoice route that was removed when invoice editing moved to Livewire.
- **Tests:** `FeeInvoiceTest::test_the_finance_menu_lists_only_pages_the_user_can_open`, `CrossSchoolAccessTest`

## Subject setup across levels printed raw component tags

- **Where:** `/dashboard/course-offerings/bulk-create/form`
- **Problem:** The settings of each chosen class printed raw `<april:input-group>` tags instead of inputs. Their `value` attributes held `old("...")` with double quotes, which Blade does not compile inside a component tag. The page was also a card holding a POST form, with long explanations and a read-only "School year" box. The roster list offered "Named learners", but the form had no way to pick learners. The store route had no tests.
- **Fix:** The page is now the Livewire component `SetUpSubjectAcrossLevels`. Classes and groups are chips. Each chosen one gets a flat row with its roster, section chips, periods a week and capacity. Groups are always taught to everyone in them. Named learners is no longer offered here. The POST route and `StoreCourseOfferingsForLevelsRequest` are removed.
- **Tests:** `CourseOfferingTest` covers the page, access, a mixed class and group save, missing choices, a period of another year and a year of another school.

## School features page needed a full form save and left the sidebar stale

- **Where:** `/dashboard/schools/features`
- **Problem:** The page to turn school tools such as Boarding on or off was a stack of cards around one POST form. A change took effect only after "Save feature choices", and the sidebar kept the old links until the next page load. The checkboxes were plain 16px boxes in a label, with On and Off badges that repeated the checkbox state.
- **Fix:** The page is now the Livewire component `ManageSchoolFeatures`. Each tool is a switch in a flat, grouped list, and a switch saves straight away with a toast. The sidebar listens for `school-features-changed` and rebuilds itself. Tools that are off read muted. The PUT route, `FeatureSettingsController` and `UpdateFeatureSettingsRequest` are removed. The global 1rem checkbox size in `app.css` now skips `role="switch"`.
- **Tests:** `FeatureSettingTest` covers turning a tool off, turning Boarding on with the sidebar following, an unknown tool, and a user without access.

## Picking a school language pattern did not change its words

- **Where:** `/dashboard/schools/operating-profile`
- **Problem:** The page listed three language patterns, each showing its own words, but choosing one left the word inputs below unchanged. A school saved the new pattern with the old words. The page was two cards around a POST form, with a help tooltip and a sentence that repeated the heading.
- **Fix:** The page is now the Livewire component `EditSchoolLanguage`. Picking a pattern fills in its words, and each word can still be changed. The patterns are a flat radio list that shows each pattern's words in one line. "Save" is the one accent button, and "Save and continue to classes" sits beside it as outline. The PUT route, `SchoolOperatingProfileController` and `UpdateSchoolOperatingProfileRequest` are removed.
- **Tests:** `SchoolTest` covers saving, the default pattern, a pattern filling its words, empty and long words, the setup redirect, and a user without access.

## Account access menu posted forms and suspended with no confirmation

- **Where:** `/dashboard/admins/{id}`
- **Problem:** The account "Manage" menu was a 28px button holding one POST form per action. "Suspend account" and "Archive account" ran on the first click with no confirmation. The profile below it stacked two cards of uppercase labels, with sentences like "No pending invitation." and "Not recorded" where a value was missing.
- **Fix:** The menu is now the Livewire component `ManageAccountAccess`, behind a 44px ⋯ button. It sends, resends or revokes an invitation and suspends, archives or reinstates the account. Suspend, archive and revoke ask first. The profile shows one flat facts row: account, membership, joined, roles, invitation, and primary school. A missing value reads "—". The account status and invitation POST and DELETE routes, `AccountStatusController`, `ChangeAccountStatusRequest` and the `account-status-control` Blade component are removed.
- **Tests:** `AccountStatusTest` and `AccountInvitationTest` drive the component, and `AdminTest` checks the flat profile.

## Timetable publish and revise sent full page posts, and a clash left the page

- **Where:** `/dashboard/timetables/{id}` and `/dashboard/timetables/{id}/manage`
- **Problem:** Publish and New revision were small POST forms. Publish asked through a browser `onsubmit` confirm, and a clash sent the page back with the whole conflict list in a flash. The status was a badge beside the buttons. Neither route had a test.
- **Fix:** The control is now the Livewire component `TimetableStatusControl`, with 44px buttons. Publish asks with `wire:confirm`. A clash leaves the timetable a draft and shows why in a danger toast, without leaving the page. New revision opens the new draft. The status reads as plain text, with "Period closed" when the period is closed. The two POST routes and their controller methods are removed.
- **Tests:** `TimetableRevisionTest` covers publishing from the screen, a clash, a revision, and a user without update access.

## Class and section status sent full page posts, and archive did not ask first

- **Where:** `/dashboard/academic-levels/{id}`, `/dashboard/academic-cycle-sections/{id}` and `/dashboard/academic-cycle-sections`
- **Problem:** Activate and Archive were PUT forms. Archive ran on the first click. When a class still had running sections, the refusal came back as a flash after a full reload. Both show pages stacked three or four cards of explanations, and empty values read "Not set", "Not chosen" or "None". The sections list had its own small Activate form on each row.
- **Fix:** The control is now the Livewire component `AcademicStructureStatusControl`. It shows the status as text, a 44px Activate button, and Archive behind a ⋯ menu. Archive asks first. A refusal shows in a danger toast and the page stays. The class and section pages are now flat: a facts row, then a plain list of sections. A missing value reads "—". "Add another" and "Roll into another year" go behind the section page's ⋯ menu. The sections list uses the same control on each row. The two status PUT routes, their controller methods and form requests, and the `academic-structure-status-control` Blade component are removed.
- **Tests:** `AcademicStructureScreenTest` and `AcademicCycleSectionTest` drive the component. They cover an archive that is refused, an archive that works, activation, and a reader who cannot change the status.

## Closing and reopening a school year or period sent full page posts, and a blocked close gave no way forward

- **Where:** `/dashboard/academic-years/{id}`
- **Problem:** Start closing, Confirm close and Reopen were POST forms inside `<details>` boxes, with 30px inputs. The "Close despite blocking checklist findings" box showed before anyone knew what was blocking. A refused close came back as a flash after a full reload. The period menu's "Start closing" item was a form inside the menu.
- **Fix:** The control is now the Livewire component `AcademicPeriodStatusControl`, with 44px buttons. Close and Reopen open a small form in place. Reopen needs a reason. A blocked close stays on the page, shows what is outstanding, and only then offers "Close with this work still open". The period menu starts closing through `ShowAcademicYear::beginClosingPeriod`. The six POST routes, their controller methods, `ChangeAcademicPeriodStatusRequest`, the `academic-period-status-control` Blade component and the unused close URLs in the year list are removed. The lifecycle rules in `ChangeAcademicPeriodStatus` are unchanged.
- **Tests:** `AcademicPeriodLifecycleTest` covers start closing, close and reopen, a missing reopen reason, a blocked close that is then accepted, a user without access, another school's year, and the period menu.

## A record sharing request was answered through a status dropdown, and taking permission back did not ask first

- **Where:** `/dashboard/data-sharing-requests/{id}`
- **Problem:** The holding school answered with a dropdown of raw states, including "Handed over", which skipped building the copy. "Taken back" and "Declined" ran on the first click. Hand over and Take in were separate POST forms. The page was three cards of boxed facts with explanations, and missing values read "Unknown", "No end date" or "Not yet".
- **Fix:** The page is now the Livewire component `ShowDataSharingRequest`. The holding school sees Approve, Decline, and a ⋯ menu for "Mark as run out" and "Take permission back". Decline and the menu items ask first. Hand over and Take in are buttons in place. "Handed over" can no longer be picked as a status, so it only happens by building the copy. Facts sit in one flat row, and a missing value reads "—". The three POST and PUT routes, their controller methods and `UpdateDataSharingRequestRequest` are removed.
- **Tests:** `DataSharingScreenTest` covers approving with a note, the asking school being refused, handing over, a refused hand over, taking in, taking permission back, and a forced "Handed over" status.

## A syllabus was only a PDF, and its revisions could lose files or publish twice

- **Where:** `/dashboard/syllabi`, `/dashboard/syllabi/{id}` and `/dashboard/syllabi/{id}/edit`
- **Problem:** A syllabus was one PDF, so students could not see what was taught each week. A revised draft shared the published PDF's file path, so deleting the draft deleted the published file. Two drafts could be opened from one published syllabus, and publishing both left two published revisions. A draft could not be edited, because edit and update returned 404, so a revision was an exact copy. The Download link was a public storage URL that the policy never checked. The list offered Delete on published rows, which the server refused. An archived offering still took new syllabi.
- **Fix:** A syllabus now starts as a draft with weekly topics (`syllabus_topics`: week, title, objectives, content, resources). The PDF is optional. The new Livewire component `SyllabusTopicsEditor` plans the topics on the new edit page, and the details and PDF save through the existing update route. A syllabus cannot be published without a topic. A revision copies the topics and needs a reason (`change_note`). Only one draft revision can be open, and a draft whose parent was already superseded is refused. A stored file is deleted only when no syllabus row still names it. `ShowSyllabus` shows the weekly plan, marks the current teaching week, and downloads the PDF after the view policy passes. The list shows the revision and status, and offers Delete on drafts only. Archived offerings are left out of the create form and refused by `SyllabusService`.
- **Tests:** `SyllabusSchemeOfWorkTest` covers a draft without a PDF, an archived offering, publishing without topics, adding, editing and removing topics, topic validation, a published or closed-period syllabus refusing changes, a reader without update access, replacing the PDF, a revision's topics and reason, a second open draft, a stale draft, deleting a revision without losing the published PDF, the policy-checked download, and the teaching week. `SyllabusTest` is updated for drafts.

## The subjects-being-taught list activated and assigned through full page posts inside open boxes

- **Where:** `/dashboard/course-offerings`
- **Problem:** Each row carried an Activate POST form and an "Assign teacher" `<details>` box with 30px selects. A refused activation, such as one in a period that is not open yet, came back as an unhandled error. The header held four outline buttons. Rows under "All years" did not show their year, so identical subjects looked the same. Empty rosters and missing teachers read "No teachers assigned".
- **Fix:** The list is now the Livewire component `CourseOfferingDirectory`, with year and subject filters kept in the URL. A draft row shows a 44px Activate button. Gradebook, Edit roster and Add teacher sit behind the row's ⋯ menu. Add teacher opens a small form in place and offers only teachers of this school. A refused activation shows why in a danger toast. The header keeps "Add subject" and moves the setup pages behind ⋯. The two POST routes, their controller methods and form requests are removed.
- **Tests:** `CourseOfferingTest` covers activating, a refused activation, adding a teacher, a teacher from another school, a reader without update access, another school's subject, and the subject filter.

## Creating fee invoices was a full page post and the fee picker could read another school's fees

- **Where:** `app/Livewire/CreateFeeInvoiceForm.php`, `resources/views/livewire/create-fee-invoice-form.blade.php`, `routes/web.php`, `FeeInvoiceController`.
- **Problem:** The form posted to a controller. Its fee picker loaded a fee category and its fees by id without a school scope. A bursar could see another school's fee names by changing the id.
- **Fix:** The whole form is now Livewire. A bursar adds a whole class or one student, then adds a whole fee category or one fee. The footer shows "N invoices · X each". Every lookup is scoped to the current school. A student must be active in this school. A waiver cannot be more than its fee. A date outside every open finance period shows an error on the issue date. One key per form stops a double press from making two batches. The store route, controller method and `StoreFeeInvoiceRequest` are removed.
- **Tests:** `FeeInvoiceTest` (whole section, backdated date, waiver, another school's fees and students, withdrawn student, double press) and `ControlNameTest`.

## The organization members screen could name any account and crash on a tampered editor

- **Where:** `app/Livewire/OrganizationMembers.php`.
- **Problem:** `revoke` and `savePermissions` loaded the person by id from every account on the platform. Revoking a stranger changed nothing but said "{name} no longer administers …", so an administrator could read any account's name by id. Saving permissions for a stranger threw an uncaught error (500).
- **Fix:** Both actions now find the person only through this organization's active memberships. Any other id is not found.
- **Tests:** `OrganizationMembersScreenTest` (a stranger from another organization, a past administrator).

## Families could send the same request twice and could not take one back

- **Where:** `app/Livewire/PortalRequests.php`, `resources/views/livewire/portal-requests.blade.php`, `app/Actions/Portal/SubmitPortalRequest.php`, `PortalRequestController`, `routes/web.php`.
- **Problem:** The family requests page was a full page post inside cards. A double press or a resend sent the same open request twice. A family had no way to take back a request, although the status list has "Cancelled". The staff inbox was already Livewire, but an old PUT status route stayed open next to it.
- **Fix:** The family page is now Livewire, with a flat form and a flat list. A calendar "Request this time" link still fills the form, and an unknown kind falls back to a document request. The action refuses a second open request with the same subject from the same person. A family can take back its own open request from a ⋯ menu. Only the person who asked can do this, and never after the school closed it. The school cannot answer a request that was taken back. Access is checked again on every send, so a guardian whose link ended cannot keep asking. The store and PUT status routes and their two form requests are removed.
- **Tests:** `PortalRequestScreenTest` (send, double send, calendar prefill, take back, somebody else's request, ended link, another school, answer after take back).

## Upgraded schools never received 33 permissions, so whole areas were closed to everyone

- **Where:** `database/migrations/2026_09_27_074218_backfill_permissions_added_after_install.php`, `.ai/rules/migrations.md`.
- **Problem:** `PermissionSeeder` runs once, at install. 33 permissions were added to it later with no migration. These include cash deposits, expenses, budgets, financial periods, refunds, the calendar, boarding, library, facilities, grading scales, roles, rankings and campus moves. An install made before they existed never got them, so even the school admin was refused on those screens. The seeder cannot simply run again, because `syncPermissions` would undo a school's own role changes.
- **Fix:** A migration creates each missing permission and grants it to the same built-in roles as the seeder, plus `platform-admin`. It only adds, it skips a permission the install already has, and it can run twice safely. A new rule in `.ai/rules/migrations.md` says every new permission needs such a migration.
- **Tests:** `PermissionBackfillTest` (old install, a school's own role changes kept, runs twice).

## Cash deposits had no tests, could post twice and took any typed amount

- **Where:** `app/Livewire/RecordCashDepositForm.php`, `resources/views/livewire/record-cash-deposit-form.blade.php`, `pages/fee/cash-deposits/*`, `CashDepositController`, `routes/web.php`.
- **Problem:** Recording a cash deposit was a full page post inside a card, with no tests. A double press posted the deposit to the books twice. The form took any amount, so a typo such as 50000 for 5000 moved money the cash box never held. A deposit could be dated in the future, and amounts with more than two decimals were rounded without a word.
- **Fix:** The form is now Livewire. It shows what the cash box holds and what is left after the deposit. An amount above the cash box needs a second, explicit yes, and changing the amount asks again. It is not refused, because a school may hold cash it never entered as an opening balance. One key per form stops a double press. Future dates and more than two decimals are refused. A date outside every open finance period shows its error on the date. The list is a flat list, not a table in a card. The store route and `StoreCashDepositRequest` are removed.
- **Tests:** new `CashDepositTest` (permission, happy path and balances, second yes, changed amount, double press, bad amounts and dates, closed period, another school's deposits).

## A librarian or accountant could open nothing

- **Where:** `database/seeders/PermissionSeeder.php`, `database/migrations/2026_09_27_075808_grant_librarian_and_accountant_their_work.php`.
- **Problem:** The built-in librarian and accountant roles were created with no permissions. The seeder had empty "assign permissions" comments for them. A school that gave a staff member either role found they could open nothing.
- **Fix:** The librarian gets the library and can look up students. The accountant gets day-to-day finance work: fees and fee categories, invoices and their fees, refunds, expenses and cash deposits, and reading budgets, financial periods and reports. Deleting, budgets and closing financial periods stay with the admin. A migration fills these two roles on an existing install only while they still hold nothing, so a school's own setup is kept.
- **Tests:** `PermissionBackfillTest` (new install defaults, only an empty role is filled).

## Expenses took a bank transfer with no reference, could post twice and took any typed amount

- **Where:** `app/Livewire/RecordExpenseForm.php`, `resources/views/livewire/record-expense-form.blade.php`, `pages/fee/expenses/*`, `ExpenseController`, `routes/web.php`.
- **Problem:** Recording an expense was a full page post inside a card. Every payment channel says whether it needs a reference, but the expense form ignored it. A bank transfer, card or cheque could be recorded with nothing to find it by on the statement. A double press posted twice. Any amount was taken, even far above what the cash box or bank held. A future date was allowed.
- **Fix:** The form is now Livewire. It shows what the paid-from account holds and what is left. An amount above that needs a second yes, and changing the amount or the channel asks again. A channel that needs a reference now requires it. One key per form stops a double press. Future dates are refused, and another school's accounts and programmes are refused. The list is a flat list, not a table in a card. The store route and `StoreExpenseRequest` are removed.
- **Tests:** `FinanceExpenseScreenTest` (permission, happy path, transfer reference, second yes, double press, another school, future date and wrong account type).

## The calendar form listed every learner in one box, offered archived groups and let a typo shut the school for years

- **Where:** `app/Livewire/CalendarEventEditor.php`, `resources/views/livewire/calendar-event-editor.blade.php`, `pages/calendar-event/*`, `CalendarEventController`, `routes/web.php`.
- **Problem:** Adding or changing a day was a full page post, and so were publishing and removing it. The people picker listed every member of the school, all learners included, as checkboxes in one box, which a school of 2,000 cannot use. People who had left were still offered. The group picker offered every year's groups, archived ones included. The same group or person could be sent twice. A closure typed as ending in 2062 was accepted, and once published it would shut the school for attendance and the timetable for decades.
- **Fix:** One Livewire editor now adds, changes, publishes and removes a day. Publish is the one accent action on a draft. "Make it a draft again" and "Remove" sit in the ⋯ menu, and Remove asks first. People are found by typing at least two letters, only among active members of this school. Groups come from this year and exclude archived ones, but a group the day already names stays listed. Repeated ids are refused, and a day cannot last more than a year. A holiday or closure says that it shuts the school once published. Switching "All day" keeps the chosen days, and a bad `day` link falls back to today. A reader without edit rights sees a flat facts row with "—" for missing values. The four write routes and both form requests are removed.
- **Tests:** `CalendarEventScreenTest` (27 tests, including the typo year, repeated and foreign groups, people search limits, a person who left, reader view, another school's day, and remove and publish permissions).

## A syllabus could get a second revision while the first waited for review

- **Where:** `app/Models/Syllabus.php`, `app/Actions/Syllabus/ReviseSyllabus.php`, `resources/views/livewire/show-syllabus.blade.php`.
- **Problem:** `Syllabus::openRevision()` counted only draft revisions. When a teacher sent a revision for review, the published syllabus again showed "Create revised draft". A second revision could then open with the same revision number. The two revisions competed, and approving one left the other behind.
- **Fix:** A revision that waits for review now counts as open. `ReviseSyllabus` refuses a new revision until that one is published, sent back or withdrawn. The published syllabus now links to the revision under review, for everyone who can see it.
- **Tests:** `SyllabusTest::test_a_syllabus_cannot_be_revised_again_while_a_revision_waits_for_review`. The syllabus demo seeder found this bug on its second run.
