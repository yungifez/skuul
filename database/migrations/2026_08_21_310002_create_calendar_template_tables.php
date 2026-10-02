<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * An organization describes its calendar once. A campus generates a cycle
     * from that description, or overrides it with a template of its own.
     *
     * A template period holds offsets, not dates. "Term 1 starts on day 0 and
     * runs 84 days" generates any cycle from the day the campus opens, so the
     * same template serves every year.
     */
    public function up(): void
    {
        Schema::create('calendar_templates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('description', 500)->nullable();

            // The template a campus uses when it has not chosen one.
            $table->boolean('is_default')->default(false);

            // How long a whole cycle runs, so the generator can date the year.
            $table->unsignedSmallInteger('cycle_length_days')->default(365);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['organization_id', 'is_default']);
        });

        Schema::create('calendar_template_periods', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('calendar_template_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('calendar_template_periods')->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('label', 100)->nullable();
            $table->string('type', 30);
            $table->unsignedSmallInteger('position')->default(1);

            // Days from the first day of the cycle, and how long this runs.
            $table->unsignedSmallInteger('start_offset_days')->default(0);
            $table->unsignedSmallInteger('length_days');
            $table->timestamps();

            $table->index(['calendar_template_id', 'position']);
        });

        Schema::table('schools', function (Blueprint $table): void {
            // Null means the campus follows its organization's default.
            $table->foreignId('calendar_template_id')
                ->nullable()
                ->after('academic_period_id')
                ->constrained()
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table): void {
            $table->dropForeign(['calendar_template_id']);
            $table->dropColumn('calendar_template_id');
        });

        Schema::dropIfExists('calendar_template_periods');
        Schema::dropIfExists('calendar_templates');
    }
};
