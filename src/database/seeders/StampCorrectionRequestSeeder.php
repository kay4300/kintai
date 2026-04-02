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
        $attendance = Attendance::first();

        if (!$attendance) {
            return;
        }

        StampCorrectionRequest::create([
            'user_id' => 1,
            'attendance_id' => $attendance->id,
            'target_date' => now(),
            'reason' => '打刻修正のため',
            'status' => 1,
        ]);

        StampCorrectionRequest::create([
            'user_id' => 1,
            'attendance_id' => $attendance->id,
            'target_date' => now()->subDay(),
            'reason' => '退勤漏れ',
            'status' => 2,
        ]);

        StampCorrectionRequest::create([
            'user_id' => 1,
            'attendance_id' => $attendance->id,
            'target_date' => now()->subDays(2),
            'reason' => '休憩時間の修正',
            'status' => 1,
        ]);
    }
    //

}
