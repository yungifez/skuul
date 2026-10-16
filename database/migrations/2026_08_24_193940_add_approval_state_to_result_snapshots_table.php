<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('result_snapshots', function (Blueprint $table): void {
            $table->string('approval_status', 20)->default('approved')->after('published_by');
            $table->timestamp('approved_at')->nullable()->after('approval_status');
            $table->foreignId('approved_by')->nullable()->after('approved_at')->constrained('users')->nullOnDelete();
            $table->string('approval_reason', 500)->nullable()->after('approved_by');
            $table->index(['school_id', 'approval_status', 'course_offering_id', 'student_record_id'], 'result_approval_lookup_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('result_snapshots', function (Blueprint $table): void {
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['school_id']);
            $table->dropIndex('result_approval_lookup_index');
            $table->dropColumn(['approval_status', 'approved_at', 'approved_by', 'approval_reason']);
            $table->foreign('school_id')->references('id')->on('schools')->cascadeOnDelete();
        });
    }
};
