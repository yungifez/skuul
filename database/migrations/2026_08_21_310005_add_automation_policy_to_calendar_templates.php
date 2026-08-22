<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * An organization decides how much of its calendar runs by itself. These
     * two settings are the whole of that decision: whether a scheduled period
     * opens on its own start date, and how far ahead the next cycle is built.
     *
     * Closing stays manual. Nothing here closes a period.
     */
    public function up(): void
    {
        Schema::table('calendar_templates', function (Blueprint $table): void {
            $table->boolean('auto_open')->default(false)->after('cycle_length_days');

            // Zero means the next cycle is never generated automatically.
            $table->unsignedSmallInteger('generate_ahead_weeks')->default(0)->after('auto_open');

            // How many days before a start or an end a reminder is sent.
            $table->unsignedSmallInteger('remind_days_before')->default(14)->after('generate_ahead_weeks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calendar_templates', function (Blueprint $table): void {
            $table->dropColumn(['auto_open', 'generate_ahead_weeks', 'remind_days_before']);
        });
    }
};
