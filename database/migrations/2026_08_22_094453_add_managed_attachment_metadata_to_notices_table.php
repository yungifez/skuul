<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('notices', function (Blueprint $table): void {
            $table->string('attachment_disk', 30)->nullable()->after('attachment');
            $table->string('attachment_name')->nullable()->after('attachment_disk');
            $table->string('attachment_mime_type', 127)->nullable()->after('attachment_name');
            $table->unsignedInteger('attachment_size')->nullable()->after('attachment_mime_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notices', function (Blueprint $table): void {
            $table->dropColumn([
                'attachment_disk',
                'attachment_name',
                'attachment_mime_type',
                'attachment_size',
            ]);
        });
    }
};
