<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Move health data to the restricted health record before removing it from
     * the general person profile.
     */
    public function up(): void
    {
        DB::table('student_records')
            ->join('users', 'users.id', '=', 'student_records.user_id')
            ->whereNotNull('student_records.school_id')
            ->whereNotNull('users.blood_group')
            ->whereNotExists(function ($query): void {
                $query->selectRaw('1')
                    ->from('student_health_records')
                    ->whereColumn('student_health_records.student_record_id', 'student_records.id');
            })
            ->select([
                'student_records.school_id',
                'student_records.id as student_record_id',
                'users.blood_group',
            ])
            ->orderBy('student_records.id')
            ->get()
            ->chunk(500)
            ->each(function ($records): void {
                DB::table('student_health_records')->insert(
                    $records->map(fn ($record): array => [
                        'school_id' => $record->school_id,
                        'student_record_id' => $record->student_record_id,
                        'blood_group' => $record->blood_group,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ])->all(),
                );
            });

        Schema::table('users', function (Blueprint $table): void {
            $table->date('birthday')->nullable()->change();
            $table->string('address')->nullable()->change();
            $table->string('nationality')->nullable()->change();
            $table->string('state')->nullable()->change();
            $table->string('city')->nullable()->change();
            $table->dropColumn(['religion', 'blood_group']);
        });
    }

    /**
     * Restore nullable columns for a safe rollback. Health records remain the
     * authoritative source after the forward migration.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->date('birthday')->nullable()->change();
            $table->string('religion')->nullable();
            $table->string('blood_group')->nullable();
        });

        DB::table('student_health_records')
            ->join('student_records', 'student_records.id', '=', 'student_health_records.student_record_id')
            ->whereNotNull('student_health_records.blood_group')
            ->select([
                'student_records.user_id',
                'student_health_records.blood_group',
            ])
            ->orderBy('student_health_records.id')
            ->get()
            ->each(function ($record): void {
                DB::table('users')
                    ->where('id', $record->user_id)
                    ->whereNull('blood_group')
                    ->update(['blood_group' => $record->blood_group]);
            });
    }
};
