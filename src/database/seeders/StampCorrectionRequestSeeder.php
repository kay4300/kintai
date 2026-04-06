<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StampCorrectionRequest;
use App\Models\Attendance;

class StampCorrectionRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ランダムに5件だけ取得
        $attendances = Attendance::inRandomOrder()->take(5)->get();

        if ($attendances->isEmpty()) {
            return;
        }

        foreach ($attendances as $index => $attendance) {

            StampCorrectionRequest::create([
                'user_id' => $attendance->user_id,
                'attendance_id' => $attendance->id,
                'start_time' => $attendance->start_time,
                'end_time' => $attendance->end_time,
                'target_date' => $attendance->date,
                'reason' => '打刻間違い' . $index,
                'status' => rand(0, 1), // 0:承認待ち / 1:承認済み
            ]);
        }
    }
    //

}
