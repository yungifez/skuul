<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * An invoice line is kept in minor units. A plain integer column stops at
     * about 21 million in major units, which a year of fees can pass. Payments
     * are kept in big integers already, so the lines now match them.
     */
    public function up(): void
    {
        Schema::table('fee_invoice_records', function (Blueprint $table): void {
            $table->bigInteger('amount')->change();
            $table->bigInteger('waiver')->default(0)->change();
            $table->bigInteger('fine')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * A line above the plain integer limit would not fit back, so this
     * direction only works while every line is below it.
     */
    public function down(): void
    {
        Schema::table('fee_invoice_records', function (Blueprint $table): void {
            $table->integer('amount')->change();
            $table->integer('waiver')->default(0)->change();
            $table->integer('fine')->default(0)->change();
        });
    }
};
