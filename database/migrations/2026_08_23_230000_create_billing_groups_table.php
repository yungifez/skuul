<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The campuses that keep one purse.
 *
 * Campuses in one billing group bill a family as one school: a debt follows
 * the learner from one to the other. Campuses in different groups keep
 * separate books, which is what a district with independent campus accounts
 * needs.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('billing_groups', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();

            $table->unique(['organization_id', 'name']);
        });

        Schema::table('schools', function (Blueprint $table): void {
            // A campus with no group bills on its own, which is what every
            // campus did before this table existed.
            $table->foreignId('billing_group_id')->nullable()->after('organization_id')
                ->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('billing_group_id');
        });

        Schema::dropIfExists('billing_groups');
    }
};
