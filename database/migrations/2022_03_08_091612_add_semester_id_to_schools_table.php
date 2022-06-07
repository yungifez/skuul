<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::table('schools', static function (Blueprint $table) {
            $table->foreignId('semester_id')->nullable()->constrained()->onDelete('set null')->onUpdate('set null');
        });
    }

    public function down(): void
    {
        Schema::table('schools', static function (Blueprint $table) {
            $table->dropForeign('schools_semester_id_foreign');
            $table->dropColumn('semester_id');
        });
    }
};
