<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('notices', static function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('attachment')->nullable();
            $table->date('start_date');
            $table->date('stop_date');
            $table->boolean('active')->default(true);
            $table->foreignId('school_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notices');
    }
};
