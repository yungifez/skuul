<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * An enrollment now names its own school, so one person can hold several
     * enrollments at the same time. One of them is marked primary for the
     * screens that show a single record.
     */
    public function up(): void
    {
        Schema::table('student_records', function (Blueprint $table): void {
            $table->foreignId('school_id')->nullable()->after('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_primary')->default(true)->after('status');
            $table->foreignId('transferred_from_id')->nullable()->after('is_primary')
                ->constrained('student_records')->nullOnDelete();
        });

        // The school was only reachable through the class. Write it down.
        DB::table('student_records')
            ->join('my_classes', 'student_records.my_class_id', '=', 'my_classes.id')
            ->join('class_groups', 'my_classes.class_group_id', '=', 'class_groups.id')
            ->update(['student_records.school_id' => DB::raw('class_groups.school_id')]);

        Schema::table('student_records', function (Blueprint $table): void {
            // An admission number identifies a student inside one school.
            $table->dropUnique('student_records_admission_number_unique');
            $table->unique(['school_id', 'admission_number']);
            $table->index(['school_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_records', function (Blueprint $table): void {
            $table->dropForeign(['transferred_from_id']);
            $table->dropForeign(['school_id']);
        });

        Schema::table('student_records', function (Blueprint $table): void {
            $table->dropIndex(['school_id', 'status']);
            $table->dropUnique(['school_id', 'admission_number']);
            $table->dropColumn(['school_id', 'is_primary', 'transferred_from_id']);
        });

        Schema::table('student_records', function (Blueprint $table): void {
            $table->unique('admission_number');
        });
    }
};
