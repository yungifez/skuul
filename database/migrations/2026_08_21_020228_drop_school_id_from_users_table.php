<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * School access now lives in `school_memberships`, and the school a person
     * is working in lives in the request context. Neither belongs on the user
     * record, so switching school never writes to the user again.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropForeign(['school_id']);
            $table->dropColumn('school_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->foreignId('school_id')->nullable()->constrained()->nullOnDelete();
        });

        DB::table('users')->update([
            'school_id' => DB::raw('(select school_id from school_memberships where school_memberships.user_id = users.id and school_memberships.is_primary = 1 limit 1)'),
        ]);
    }
};
