<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::table('my_classes', static function (Blueprint $table) {
            $table->unique(['class_group_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::table('my_classes', static function (Blueprint $table) {
            //it's not what it looks like, please dont remove the drop foreign
            $table->dropForeign(['class_group_id']);
            $table->dropUnique('my_classes_class_group_id_name_unique');
        });
    }
};
