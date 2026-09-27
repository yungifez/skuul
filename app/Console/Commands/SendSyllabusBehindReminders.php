<?php

namespace App\Console\Commands;

use App\Enums\CourseOfferingStatus;
use App\Enums\SyllabusStatus;
use App\Models\School;
use App\Models\Syllabus;
use App\Models\TeachingAssignment;
use App\Models\User;
use App\Notifications\SyllabusWorkNotification;
use App\Services\Syllabus\SyllabusCoverageService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

/**
 * Tell each teacher which of their classes are behind the published plan.
 */
class SendSyllabusBehindReminders extends Command
{
    protected $signature = 'skuul:send-syllabus-behind-reminders {--dry-run : List reminders without sending them}';

    protected $description = 'Send teachers a weekly list of the classes that are behind their syllabus';

    /**
     * Execute the console command.
     */
    public function handle(SyllabusCoverageService $coverage): int
    {
        $sent = 0;

        foreach (School::query()->get() as $school) {
            school_context()->set($school, remember: false);

            /** @var array<int, list<string>> $behindByTeacher */
            $behindByTeacher = [];

            $syllabi = Syllabus::query()
                ->inSchool($school->id)
                ->where('status', SyllabusStatus::Published)
                ->whereHas('courseOffering', fn (Builder $offerings) => $offerings->where('status', CourseOfferingStatus::Active))
                ->with(['courseOffering.subject:id,name', 'courseOffering.academicPeriod', 'courseOffering.academicLevel'])
                ->get()
                ->filter(fn (Syllabus $syllabus): bool => $syllabus->teachingWeekOn() !== null);

            foreach ($syllabi as $syllabus) {
                foreach ($coverage->summary($syllabus) as $track) {
                    if ($track['behind'] === 0) {
                        continue;
                    }

                    $teacherIds = TeachingAssignment::query()
                        ->where('course_offering_id', $syllabus->course_offering_id)
                        ->runningOn()
                        ->where(function (Builder $assignments) use ($track): void {
                            $assignments->whereNull('academic_cycle_section_id')->orWhere('academic_cycle_section_id', $track['id']);
                        })
                        ->pluck('user_id');

                    foreach ($teacherIds as $teacherId) {
                        $behindByTeacher[$teacherId][] = "{$syllabus->courseOffering->subject->name}, {$track['label']}: {$track['behind']} ".($track['behind'] === 1 ? 'topic' : 'topics').' behind';
                    }
                }
            }

            foreach (User::query()->whereKey(array_keys($behindByTeacher))->get() as $teacher) {
                $lines = $behindByTeacher[$teacher->id];

                if ($this->option('dry-run')) {
                    $this->line("would remind {$teacher->name} about ".count($lines).' class(es)');
                } else {
                    $teacher->notify(new SyllabusWorkNotification(
                        "{$school->name}: classes behind the syllabus",
                        ['These classes have topics from past weeks that are not marked as covered or skipped:', ...$lines, 'Record what you taught, or mark a topic as skipped if you left it out on purpose.'],
                        'Open syllabi',
                        route('syllabi.index'),
                    ));
                }

                $sent++;
            }
        }

        $this->info($this->option('dry-run') ? "{$sent} reminder(s) would be sent." : "{$sent} reminder(s) sent.");

        return self::SUCCESS;
    }
}
