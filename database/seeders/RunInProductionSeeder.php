<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RunInProductionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            WeekdaySeeder::class,
            RoleSeeder::class,
            PermissionSeeder::class,
        ]);
        DB::table('users')->delete();

        $superAdmin = User::firstOrCreate([
            'id'                => 1,
            'name'              => 'John Doe',
            'email'             => 'super@admin.com',
            'password'          => Hash::make('password'),
            'school_id'         => null,
            'address'           => 'super admin street',
            'birthday'          => '22/04/04',
            'nationality'       => 'nigeria',
            'state'             => 'lagos',
            'city'              => 'lagos',
            'blood_group'       => 'B+',
            'email_verified_at' => now(),
            'gender'            => 'male',
        ]);

        $superAdmin->assignRole('super-admin');
    }
}
