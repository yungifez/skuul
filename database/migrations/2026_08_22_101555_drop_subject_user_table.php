<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('subject_user');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        throw new LogicException('The retired subject teacher assignments cannot be restored.');
    }
};
