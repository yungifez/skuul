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
 * Put the demo school back to its seeded state.
 *
 * The schema stays as the last deployment left it. Only the rows go, so the
 * reset is quick and never replays a migration. The country, state and city
 * reference tables are kept: they never change, and loading them again takes
 * more memory than a small server has.
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
