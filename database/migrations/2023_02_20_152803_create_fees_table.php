<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('fees', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('fee_category_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('name', 1024);
            $table->longText('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fees');
    }
};
