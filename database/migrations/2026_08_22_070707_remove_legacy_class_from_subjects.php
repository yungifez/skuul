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
        Schema::table('subjects', function (Blueprint $table): void {
            $table->dropForeign(['my_class_id']);
            $table->dropUnique('subjects_name_my_class_id_unique');
            $table->dropColumn('my_class_id');
            $table->unique(['school_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        throw new LogicException('This destructive migration cannot be rolled back.');
    }
};
