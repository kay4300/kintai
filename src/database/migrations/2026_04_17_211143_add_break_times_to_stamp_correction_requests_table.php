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
        Schema::table('stamp_correction_requests', function (Blueprint $table) {
            //
            Schema::table('stamp_correction_requests', function (Blueprint $table) {
                // 休憩1
                $table->time('break_start_1')->nullable();
                $table->time('break_end_1')->nullable();

                // 休憩2
                $table->time('break_start_2')->nullable();
                $table->time('break_end_2')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stamp_correction_requests', function (Blueprint $table) {
            //
        });
    }
};
