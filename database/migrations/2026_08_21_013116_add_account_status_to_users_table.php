<?php

use App\Enums\AccountStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('account_status', 20)
                ->default(AccountStatus::Active->value)
                ->after('password')
                ->index();
        });

        // A provisioned account has no password until the person accepts an invitation.
        Schema::table('users', function (Blueprint $table): void {
            $table->string('password')->nullable()->change();
        });

        DB::table('users')
            ->where('locked', true)
            ->update(['account_status' => AccountStatus::Suspended->value]);

        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('locked');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->boolean('locked')->default(false)->after('password');
        });

        DB::table('users')->whereNull('password')->update(['password' => '']);

        Schema::table('users', function (Blueprint $table): void {
            $table->string('password')->nullable(false)->change();
        });

        DB::table('users')
            ->where('account_status', AccountStatus::Suspended->value)
            ->update(['locked' => true]);

        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('account_status');
        });
    }
};
