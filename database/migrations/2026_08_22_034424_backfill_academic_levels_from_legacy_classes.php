<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('my_classes')
            ->join('class_groups', 'class_groups.id', '=', 'my_classes.class_group_id')
            ->orderBy('my_classes.id')
            ->select([
                'my_classes.id as legacy_my_class_id',
                'my_classes.name',
                'class_groups.school_id',
            ])
            ->eachById(function (object $legacyClass): void {
                DB::table('academic_levels')->insertOrIgnore([
                    'school_id'          => $legacyClass->school_id,
                    'legacy_my_class_id' => $legacyClass->legacy_my_class_id,
                    'name'               => $legacyClass->name,
                    'status'             => 'active',
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);
            }, 100, 'my_classes.id', 'legacy_my_class_id');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // The structural table is removed by the earlier migration. Do not
        // remove levels separately: a new cycle section may now reference one.
    }
};
