<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('timetables', function (Blueprint $table): void {
            $table->string('recurrence', 20)->default('weekly')->after('academic_period_id');
            $table->date('occurs_on')->nullable()->after('recurrence');
        });
    }

    public function down(): void
    {
        Schema::table('timetables', function (Blueprint $table): void {
            $table->dropColumn(['recurrence', 'occurs_on']);
        });
    }
};
