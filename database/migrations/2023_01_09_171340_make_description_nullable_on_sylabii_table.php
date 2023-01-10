<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::table('syllabi', function ($table) {
            $table->longText('description')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('syllabi', function ($table) {
            $table->longText('description')->notNullable()->default('No description')->change();
        });
    }
};
