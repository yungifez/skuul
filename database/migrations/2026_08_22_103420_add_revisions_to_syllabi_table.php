<?php

use App\Enums\SyllabusStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('syllabi', function (Blueprint $table): void {
            $table->string('status')->default(SyllabusStatus::Draft->value)->after('file');
            $table->unsignedInteger('revision')->default(1)->after('status');
            $table->foreignId('revision_of_id')->nullable()->after('revision')->constrained('syllabi')->nullOnDelete();
            $table->timestamp('published_at')->nullable()->after('revision_of_id');
            $table->foreignId('published_by')->nullable()->after('published_at')->constrained('users')->nullOnDelete();
        });

        DB::table('syllabi')->update(['status' => SyllabusStatus::Published->value]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('syllabi', function (Blueprint $table): void {
            $table->dropForeign(['revision_of_id']);
            $table->dropForeign(['published_by']);
            $table->dropColumn(['status', 'revision', 'revision_of_id', 'published_at', 'published_by']);
        });
    }
};
