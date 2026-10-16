<?php

namespace App\Services\Demo;

use Database\Seeders\DemoSchoolSeeder;
use Database\Seeders\RunInProductionSeeder;
use Database\Seeders\WorldSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;

/**
 * Reset demo rows without replaying migrations. Preserve world reference tables.
 */
class DemoDataRefresher
{
    public function __construct(private PermissionRegistrar $permissionRegistrar) {}

    /**
     * Empty every application table, load the roles and permissions, and
     * build the demo school again.
     */
    public function refresh(): void
    {
        $preserved = $this->preservedTables();

        Schema::withoutForeignKeyConstraints(function () use ($preserved): void {
            foreach (Schema::getTableListing(schemaQualified: false) as $table) {
                if (!in_array($table, $preserved, true)) {
                    DB::table($table)->truncate();
                }
            }
        });

        $this->permissionRegistrar->forgetCachedPermissions();

        if (DB::table(config('world.migrations.countries.table_name'))->doesntExist()) {
            Artisan::call('db:seed', ['--class' => WorldSeeder::class, '--force' => true]);
        }

        Artisan::call('db:seed', ['--class' => RunInProductionSeeder::class, '--force' => true]);
        Artisan::call('db:seed', ['--class' => DemoSchoolSeeder::class, '--force' => true]);
    }

    /**
     * The tables a reset keeps: the migration history and the world data.
     *
     * @return list<string>
     */
    public function preservedTables(): array
    {
        $worldTables = collect(config('world.migrations'))->pluck('table_name');

        return $worldTables->prepend('migrations')->values()->all();
    }
}
