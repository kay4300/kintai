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

            $workStart = \Carbon\Carbon::parse($attendance->start_time);
            $workEnd   = \Carbon\Carbon::parse($attendance->end_time);

            // 念のため（未退勤など）
            if (!$workStart || !$workEnd) {
                continue;
            }
            // 休憩時間は勤務時間内でランダムに作る
            $breaks = [];

            // 1日1〜2回ランダムに休憩作成
            $breakCount = rand(1, 2);

            for ($i = 0; $i < $breakCount; $i++) {

                $attempt = 0;

                do {
                    $attempt++;

                    // 勤務時間内で開始
                    $start = $workStart->copy()->addMinutes(
                    rand(60, $workEnd->diffInMinutes($workStart) - 60)
                );

                // 休憩時間（15〜60分）
                $end = $start->copy()->addMinutes(rand(15, 60));

                // 退勤超え防止
                if ($end > $workEnd) {
                    $end = $workEnd->copy();
                }

                    // 休憩1,2の重なりチェック
                    $overlap = false;
                    foreach ($breaks as $b) {
                        if ($start < $b['end'] && $end > $b['start']) {
                            $overlap = true;
                            break;
                        }
                    }
                } while ($overlap && $attempt < 10);

                // OKなら保存
                $breaks[] = [
                    'start' => $start,
                    'end'   => $end,
                ];

                \App\Models\BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'start_time' => $start,
                    'end_time'   => $end,
                ]);
            }
        }
            //     $break = \App\Models\BreakTime::factory()->make([
            //         'attendance_id' => $attendance->id,
            //     ]);

            //     $start = \Carbon\Carbon::parse($attendance->start_time);
            //     $end   = \Carbon\Carbon::parse($attendance->end_time);

            //     // 勤務外ならスキップ
            //     if ($break->start_time < $start || $break->end_time > $end) {
            //         continue;
            //     }

            //     // 保存
            //     $break->save();
            // }
        
        //
    }
}
