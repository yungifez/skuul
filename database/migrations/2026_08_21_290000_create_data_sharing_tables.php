<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Asking for records is not the same as reading them. A request names the
     * categories, the reason, and the day the permission ends. A package is
     * the copy that was actually handed over, labelled with the school it came
     * from.
     */
    public function up(): void
    {
        Schema::create('data_sharing_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('requesting_school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('holding_school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('student_record_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default('requested');
            $table->json('categories');
            $table->string('purpose', 500);
            $table->date('expires_on')->nullable();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('decided_at')->nullable();
            $table->string('decision_note', 500)->nullable();
            $table->timestamps();

            $table->index(['holding_school_id', 'status']);
        });

        Schema::create('transfer_packages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('data_sharing_request_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('source_school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('destination_school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('student_record_id')->constrained()->cascadeOnDelete();
            $table->json('categories');
            $table->json('payload');
            $table->foreignId('built_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('received_at')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('received_student_record_id')->nullable()->constrained('student_records')->nullOnDelete();
            $table->timestamps();

            $table->index(['destination_school_id', 'received_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_packages');
        Schema::dropIfExists('data_sharing_requests');
    }
};
