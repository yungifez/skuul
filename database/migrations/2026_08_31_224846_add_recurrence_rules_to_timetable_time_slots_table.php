<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('timetable_time_slots', function (Blueprint $table): void {
            $table->date('starts_on')->nullable()->after('occurs_on');
            $table->unsignedTinyInteger('recurrence_interval')->default(1)->after('starts_on');
            $table->json('recurrence_weekdays')->nullable()->after('recurrence_interval');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('timetable_time_slots', function (Blueprint $table): void {
            $table->dropColumn(['starts_on', 'recurrence_interval', 'recurrence_weekdays']);
        });
    }
};
