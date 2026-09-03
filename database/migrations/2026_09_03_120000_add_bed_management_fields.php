<?php

use App\Enums\DormitoryBedStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add the operational state a boarding office needs for each bed.
     */
    public function up(): void
    {
        Schema::table('dormitory_beds', function (Blueprint $table): void {
            $table->string('status', 20)
                ->default(DormitoryBedStatus::Available->value)
                ->after('is_active');
            $table->text('status_reason')->nullable()->after('status');
        });
    }

    /**
     * Remove the bed state fields.
     */
    public function down(): void
    {
        Schema::table('dormitory_beds', function (Blueprint $table): void {
            $table->dropColumn(['status', 'status_reason']);
        });
    }
};
