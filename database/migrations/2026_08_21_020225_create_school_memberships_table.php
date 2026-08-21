<?php

use App\Enums\SchoolMembershipStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * A membership record connects one person to one school. It replaces the
     * single `users.school_id` column, so one person can work in several
     * schools without a second login.
     */
    public function up(): void
    {
        Schema::create('school_memberships', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default(SchoolMembershipStatus::Active->value);
            $table->boolean('is_primary')->default(false);
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'school_id']);
            $table->index(['school_id', 'status']);
        });

        // Every existing user belongs to exactly one school. Carry that over.
        DB::table('users')
            ->whereNotNull('school_id')
            ->orderBy('id')
            ->chunkById(500, function ($users): void {
                $rows = [];

                foreach ($users as $user) {
                    $rows[] = [
                        'user_id' => $user->id,
                        'school_id' => $user->school_id,
                        'status' => SchoolMembershipStatus::Active->value,
                        'is_primary' => true,
                        'joined_at' => $user->created_at,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                DB::table('school_memberships')->insert($rows);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_memberships');
    }
};
