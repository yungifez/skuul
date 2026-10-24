# Changelog

All notable changes to `skuul` will be documented in this file.

## [3.1.1](https://github.com/yungifez/skuul/compare/v3.1.0...v3.1.1) (2026-10-24)


### Miscellaneous Chores

* set up Release Please and a security policy ([#439](https://github.com/yungifez/skuul/issues/439)) ([358e0e8](https://github.com/yungifez/skuul/commit/358e0e8266e3d06fccdbb8b52a67b9c064959f95))

## [3.1.0](https://github.com/yungifez/skuul/compare/v3.0.2...v3.1.0) (2026-10-24)

Skuul now supports both ways of teaching: learners who stay in one class all day, and learners who move between subjects.

### Features

* A level group, such as "Upper school", can name learners from all its levels.
* Subject setup starts from the roster mode of the campus's teaching model. A subject with named learners checks its capacity.
* A subject can have its own timetable and its own name, such as "Algebra B · Mathematics".
* Learners and teachers see a personal week that combines every timetable that applies to them.
* The timetable clash check warns when learners of two subjects are placed at the same time.
* Each subject has a lesson register. It lists the learners on the subject's roster, from any section. Only a current teacher of the subject can take it.
* A notice can go to the learners of chosen subjects.
* Rankings can compare the learners of one subject with named learners, by their result in that subject.
* A campus with no daily register counts lesson registers on the dashboard and in the portal.

### Bug Fixes

* The portal timetable stays on the learner's own campus.
* Roll-forward copies each subject with named learners once.
* A teacher of one section of a class can record syllabus coverage for that class.
* On a whole-level subject, a teacher of some sections sees and marks only their own learners in the gradebook.
* Update Livewire to 4.4.8 and Vite to 6.4.5 for security advisories.

### Upgrade notes

This release has two migrations. They add subject timetables and lesson registers. Back up the database, then update with `php artisan skuul:update` or check out the `v3.1.0` tag.

## Earlier releases

See the [GitHub releases](https://github.com/yungifez/skuul/releases) for v3.0.2 and older.
