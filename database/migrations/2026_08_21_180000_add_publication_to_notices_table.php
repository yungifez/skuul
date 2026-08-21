<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * A notice now has a state, an audience, and a list of the people it was
     * sent to, so the school can answer who was told and whether they read it.
     */
    public function up(): void
    {
        Schema::table('notices', function (Blueprint $table): void {
            $table->string('status', 20)->default('draft')->after('content');
            $table->json('audience')->nullable()->after('status');
            $table->boolean('send_email')->default(false)->after('audience');
            $table->timestamp('scheduled_for')->nullable()->after('send_email');
            $table->timestamp('published_at')->nullable()->after('scheduled_for');
            $table->foreignId('published_by')->nullable()->after('published_at')->constrained('users')->nullOnDelete();
            $table->unsignedSmallInteger('revision')->default(1)->after('published_by');
            $table->foreignId('revision_of_id')->nullable()->after('revision')->constrained('notices')->nullOnDelete();
        });

        // A notice that was already on the board stays on the board.
        DB::table('notices')->where('active', 1)->update(['status' => 'published', 'published_at' => now()]);

        Schema::create('notice_recipients', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('notice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('state', 20)->default('pending');
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('dismissed_at')->nullable();
            $table->string('failure_reason', 255)->nullable();
            $table->timestamps();

            $table->unique(['notice_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notice_recipients');

        Schema::table('notices', function (Blueprint $table): void {
            $table->dropForeign(['published_by']);
            $table->dropForeign(['revision_of_id']);
        });

        Schema::table('notices', function (Blueprint $table): void {
            $table->dropColumn([
                'status',
                'audience',
                'send_email',
                'scheduled_for',
                'published_at',
                'published_by',
                'revision',
                'revision_of_id',
            ]);
        });
    }
};
