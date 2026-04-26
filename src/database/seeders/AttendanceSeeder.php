<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\User;
use App\Models\BreakTime;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $startDate = now()->subMonth()->startOfMonth(); // ← 前月から
        $endDate = now();

        $users = User::all();
  
        foreach ($users as $user) {

            $date = $startDate->copy();

            while ($date <= $endDate) {

                // 土日 or 20%で休み
                if ($date->isWeekend() || rand(1, 100) <= 20) {
                    $date->addDay();
                    continue;
                }

                $attendance = Attendance::factory()->create([
                    'user_id' => $user->id,
                    'date' => $date->format('Y-m-d'),
                    'start_time' => $date->copy()->setTime(9, 0),
                    'end_time' => $date->copy()->setTime(18, 0),
                ]);

                //休憩追加
                BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'start_time' => $date->copy()->setTime(12, 0),
                    'end_time' => $date->copy()->setTime(13, 0),
                ]);

                if (rand(1, 100) <= 70) {
                    $start = $date->copy()->setTime(rand(14, 16), 0);

                    BreakTime::create([
                        'attendance_id' => $attendance->id,
                        'start_time' => $start,
                        'end_time' => $start->copy()->addMinutes(30),
                    ]);
                }

                $date->addDay();
            }
        }
    }
        //
}

