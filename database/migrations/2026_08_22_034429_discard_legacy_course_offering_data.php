<?php

use App\Services\Backup\LegacyDataArchive;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Discard offerings that use the removed class and section identities.
     */
    public function up(): void
    {
        LegacyDataArchive::keep('course_offering_sections', DB::table('course_offering_sections'));
        LegacyDataArchive::keep('course_offering_student_records', DB::table('course_offering_student_records'));
        LegacyDataArchive::keep('course_offerings', DB::table('course_offerings'));

        DB::table('course_offerings')->delete();
    }

    /**
     * Discarded course offerings cannot be restored.
     */
    public function down(): void
    {
        throw new LogicException('This destructive migration cannot be rolled back.');
    }
};
