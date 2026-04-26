<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\BreakTime;

class BreakTimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 勤怠レコード取得
        $attendances = Attendance::all();

        foreach ($attendances as $attendance) {

            // 1日1〜3回ランダムに休憩作成
            $breakCount = rand(1, 3);

            for ($i = 0; $i < $breakCount; $i++) {
                $break = \App\Models\BreakTime::factory()->make([
                    'attendance_id' => $attendance->id,
                ]);

                // 保存
                $break->save();
            }
        }
        //
    }
}
