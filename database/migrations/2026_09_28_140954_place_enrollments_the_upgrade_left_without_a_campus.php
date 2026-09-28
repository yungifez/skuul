<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The upgrade read an enrollment's campus from its class. An enrollment
     * with no class was left without a campus, so no screen showed it. When
     * the learner belongs to exactly one campus, that campus is theirs. An
     * admission number that campus already uses is left for staff to settle.
     */
    public function up(): void
    {
        $unplaced = DB::table('student_records')
            ->whereNull('school_id')
            ->get(['id', 'user_id', 'admission_number']);

        foreach ($unplaced as $enrollment) {
            $schoolIds = DB::table('school_memberships')
                ->where('user_id', $enrollment->user_id)
                ->pluck('school_id')
                ->unique();

            if ($schoolIds->count() !== 1) {
                continue;
            }

            $schoolId = $schoolIds->first();

            $numberTaken = $enrollment->admission_number !== null && DB::table('student_records')
                ->where('school_id', $schoolId)
                ->where('admission_number', $enrollment->admission_number)
                ->exists();

            $alreadyEnrolledThere = DB::table('student_records')
                ->where('school_id', $schoolId)
                ->where('user_id', $enrollment->user_id)
                ->exists();

            if ($numberTaken || $alreadyEnrolledThere) {
                continue;
            }

            DB::table('student_records')->where('id', $enrollment->id)->update(['school_id' => $schoolId]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * The campus given here cannot be told apart from one given later, so it stays.
     */
    public function down(): void {}
};
