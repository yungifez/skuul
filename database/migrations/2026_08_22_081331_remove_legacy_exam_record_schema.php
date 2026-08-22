<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('exam_records');

        Schema::table('exams', function (Blueprint $table): void {
            $table->dropColumn('publish_result');
        });
    }

    public function down(): void
    {
        throw new LogicException('The legacy exam schema cannot be restored.');
    }
};
