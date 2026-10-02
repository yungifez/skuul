<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('grade_systems');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        throw new LogicException('The legacy grade-system schema cannot be restored.');
    }
};
