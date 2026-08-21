<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fee_invoice_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fee_invoice_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('fee_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->integer('amount');
            $table->integer('waiver')->default(0);
            $table->integer('fine')->default(0);
            $table->integer('paid')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_invoice_records');
    }
};
