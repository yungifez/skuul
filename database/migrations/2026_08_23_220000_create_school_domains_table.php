<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The web addresses an organization answers on.
 *
 * A domain says which organization, and sometimes which campus, a visitor
 * meant. It never says who the visitor is: membership still decides that.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('school_domains', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();

            // A domain may name one campus, or the organization as a whole.
            $table->foreignId('school_id')->nullable()->constrained()->nullOnDelete();

            $table->string('host')->unique();
            $table->boolean('is_primary')->default(false);

            /*
             * An address is only followed once the organization has proved it
             * owns it. Until then it is a claim, and claims are ignored.
             */
            $table->string('verification_token', 64);
            $table->timestamp('verified_at')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['organization_id', 'is_primary']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_domains');
    }
};
