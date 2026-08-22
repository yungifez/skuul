<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('schools')
            ->whereNull('organization_id')
            ->orderBy('id')
            ->chunkById(200, function ($schools): void {
                foreach ($schools as $school) {
                    $organizationId = DB::table('organizations')->insertGetId([
                        'name'       => $school->name,
                        'code'       => 'legacy-school-'.$school->id,
                        'address'    => $school->address,
                        'email'      => $school->email,
                        'phone'      => $school->phone,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    DB::table('schools')
                        ->where('id', $school->id)
                        ->update(['organization_id' => $organizationId]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Do not remove organizations on rollback: an administrator may have
        // already grouped campuses or changed organization details.
    }
};
