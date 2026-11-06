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
        // Allow 'waitlist' status alongside standard statuses
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `appointments` MODIFY COLUMN `status` VARCHAR(30) NOT NULL DEFAULT 'pending'");

        Schema::table('appointments', function (Blueprint $table) {
            $table->unsignedInteger('waitlist_position')->nullable()->after('status');
            $table->timestamp('waitlisted_at')->nullable()->after('waitlist_position');
            $table->timestamp('promoted_at')->nullable()->after('waitlisted_at');
            $table->timestamp('rescheduled_at')->nullable()->after('promoted_at');
            $table->string('rescheduled_from_code', 30)->nullable()->after('rescheduled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn([
                'waitlist_position',
                'waitlisted_at',
                'promoted_at',
                'rescheduled_at',
                'rescheduled_from_code',
            ]);
        });
    }
};
