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
     * Self-service registration and the applicant workflow are removed.
     * Administrators now provision accounts and send invitations.
     */
    public function up(): void
    {
        DB::table('statuses')
            ->where('model_type', 'App\\Models\\AccountApplication')
            ->delete();

        Schema::dropIfExists('account_applications');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('account_applications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }
};
