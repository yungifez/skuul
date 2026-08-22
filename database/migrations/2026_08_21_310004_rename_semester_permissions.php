<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The permissions the rename touches, old name to new name.
     *
     * @var array<string, string>
     */
    private array $renames = [
        'create semester' => 'create academic period',
        'read semester' => 'read academic period',
        'update semester' => 'update academic period',
        'delete semester' => 'delete academic period',
        'set semester' => 'set academic period',
        'menu-semester' => 'menu-academic-period',
    ];

    /**
     * Run the migrations.
     *
     * Permissions are rows, not code. Renaming the model without renaming them
     * would leave every role holding a permission no policy asks for any more.
     * The row keeps its id, so the roles that hold it keep it.
     */
    public function up(): void
    {
        $this->apply($this->renames);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->apply(array_flip($this->renames));
    }

    /**
     * Rename each permission that is present, skipping a name already taken.
     *
     * @param  array<string, string>  $map
     */
    private function apply(array $map): void
    {
        foreach ($map as $from => $to) {
            $taken = DB::table('permissions')->where('name', $to)->exists();

            if ($taken) {
                continue;
            }

            DB::table('permissions')->where('name', $from)->update(['name' => $to]);
        }
    }
};
