<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->constrained()->nullOnDelete()->onUpdate('cascade');
            $table->string('gender')->nullable();
            $table->date('birthday');
            $table->string('nationality');
            $table->string('state');
            $table->string('city');
            $table->string('religion')->nullable();
            $table->string('blood_group');
            $table->string('phone')->nullable();
            $table->string('address');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropColumn('school_id');
            $table->dropColumn('birthday');
            $table->dropColumn('gender');
            $table->dropColumn('address');
        });
    }
};
