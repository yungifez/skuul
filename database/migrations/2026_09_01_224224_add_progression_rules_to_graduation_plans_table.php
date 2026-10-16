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
        Schema::table('graduation_plans', function (Blueprint $table): void {
            $table->foreignId('parent_id')
                ->nullable()
                ->after('id')
                ->constrained('graduation_plans')
                ->nullOnDelete();
            $table->string('completion_operator', 10)->default('all')->after('description');
            $table->unsignedSmallInteger('position')->default(0)->after('completion_operator');
            $table->boolean('is_negated')->default(false)->after('position');

            $table->index(['parent_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('graduation_plans', function (Blueprint $table): void {
            $table->dropForeign(['parent_id']);
            $table->dropIndex(['parent_id', 'position']);
            $table->dropColumn(['parent_id', 'completion_operator', 'position', 'is_negated']);
        });
    }
};
