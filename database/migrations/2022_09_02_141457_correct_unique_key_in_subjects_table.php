<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::table('subjects', static function (Blueprint $table) {
            $table->dropIndex('subjects_name_school_id_unique');
            $table->unique(['name', 'my_class_id']);
        });
    }

    public function down(): void
    {
        Schema::table('subjects', static function (Blueprint $table) {
            $table->dropIndex('subjects_name_my_class_id_unique');
            $table->unique(['name', 'school_id']);
        });
    }
};
