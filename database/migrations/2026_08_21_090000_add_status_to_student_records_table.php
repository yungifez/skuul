<?php

use App\Enums\EnrollmentStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * An enrollment holds an explicit state. The `is_graduated` boolean could
     * only say two things and kept no history, so it is replaced.
     */
    public function up(): void
    {
        Schema::table('student_records', function (Blueprint $table): void {
            $table->string('status', 20)->default(EnrollmentStatus::Active->value)->after('user_id');
        });

        DB::table('student_records')
            ->where('is_graduated', true)
            ->update(['status' => EnrollmentStatus::Graduated->value]);

        Schema::table('student_records', function (Blueprint $table): void {
            $table->dropColumn('is_graduated');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_records', function (Blueprint $table): void {
            $table->boolean('is_graduated')->default(false)->after('user_id');
        });

        DB::table('student_records')
            ->where('status', EnrollmentStatus::Graduated->value)
            ->update(['is_graduated' => true]);

        Schema::table('student_records', function (Blueprint $table): void {
            $table->dropIndex(['status']);
            $table->dropColumn('status');
        });
    }
};
