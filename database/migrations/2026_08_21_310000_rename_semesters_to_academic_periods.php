<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * The tables that point at a period, with the rule each one keeps.
     *
     * @var array<string, array{onDelete: string, onUpdate: string}>
     */
    private array $referencingTables = [
        'attendance_records'    => ['onDelete' => 'set null', 'onUpdate' => 'no action'],
        'calendar_events'       => ['onDelete' => 'set null', 'onUpdate' => 'no action'],
        'enrollment_placements' => ['onDelete' => 'set null', 'onUpdate' => 'no action'],
        'exams'                 => ['onDelete' => 'cascade',  'onUpdate' => 'cascade'],
        'grade_categories'      => ['onDelete' => 'set null', 'onUpdate' => 'no action'],
        'grade_items'           => ['onDelete' => 'set null', 'onUpdate' => 'no action'],
        'incidents'             => ['onDelete' => 'set null', 'onUpdate' => 'no action'],
        'report_runs'           => ['onDelete' => 'set null', 'onUpdate' => 'no action'],
        'result_snapshots'      => ['onDelete' => 'set null', 'onUpdate' => 'no action'],
        'schools'               => ['onDelete' => 'set null', 'onUpdate' => 'set null'],
        'syllabi'               => ['onDelete' => 'cascade',  'onUpdate' => 'cascade'],
        'teaching_assignments'  => ['onDelete' => 'set null', 'onUpdate' => 'no action'],
        'timetables'            => ['onDelete' => 'cascade',  'onUpdate' => 'cascade'],
    ];

    /**
     * Run the migrations.
     *
     * A school divides its year into terms, semesters, quarters, exam windows,
     * or holidays. The table held all of those already; only its name still
     * said "semester". Rename it, and rename every column that points at it.
     */
    public function up(): void
    {
        // Drop the foreign keys first. MySQL will not rename a column that a
        // constraint still names.
        foreach (array_keys($this->referencingTables) as $table) {
            Schema::table($table, function (Blueprint $blueprint) use ($table): void {
                $blueprint->dropForeign("{$table}_semester_id_foreign");
            });
        }

        Schema::rename('semesters', 'academic_periods');

        foreach ($this->referencingTables as $table => $rule) {
            Schema::table($table, function (Blueprint $blueprint) use ($table, $rule): void {
                $blueprint->renameColumn('semester_id', 'academic_period_id');

                $blueprint->foreign('academic_period_id', "{$table}_academic_period_id_foreign")
                    ->references('id')
                    ->on('academic_periods')
                    ->onDelete($rule['onDelete'])
                    ->onUpdate($rule['onUpdate']);
            });
        }

        // Morph columns hold the class name, so the rename reaches the data.
        DB::table('academic_period_status_changes')
            ->where('period_type', 'App\\Models\\Semester')
            ->update(['period_type' => 'App\\Models\\AcademicPeriod']);

        DB::table('audit_events')
            ->where('subject_type', 'App\\Models\\Semester')
            ->update(['subject_type' => 'App\\Models\\AcademicPeriod']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (array_keys($this->referencingTables) as $table) {
            Schema::table($table, function (Blueprint $blueprint) use ($table): void {
                $blueprint->dropForeign("{$table}_academic_period_id_foreign");
            });
        }

        Schema::rename('academic_periods', 'semesters');

        foreach ($this->referencingTables as $table => $rule) {
            Schema::table($table, function (Blueprint $blueprint) use ($table, $rule): void {
                $blueprint->renameColumn('academic_period_id', 'semester_id');

                $blueprint->foreign('semester_id', "{$table}_semester_id_foreign")
                    ->references('id')
                    ->on('semesters')
                    ->onDelete($rule['onDelete'])
                    ->onUpdate($rule['onUpdate']);
            });
        }

        DB::table('academic_period_status_changes')
            ->where('period_type', 'App\\Models\\AcademicPeriod')
            ->update(['period_type' => 'App\\Models\\Semester']);

        DB::table('audit_events')
            ->where('subject_type', 'App\\Models\\AcademicPeriod')
            ->update(['subject_type' => 'App\\Models\\Semester']);
    }
};
