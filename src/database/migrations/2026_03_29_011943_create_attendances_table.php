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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->date('date'); // 日付

            $table->dateTime('start_time')->nullable(); // 出勤
            $table->dateTime('end_time')->nullable();   // 退勤

            $table->integer('status')->default(0); // 0:勤務外 1:出勤中 2:休憩中 3:退勤済

            $table->timestamps();

            // インデックス・制約を追加する（カラム定義が終わったあとが定番位置）user_id + date の組み合わせが一意になる。
            $table->unique(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
