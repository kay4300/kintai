<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\StampCorrectionRequest;
use App\Models\User;
use App\Models\Attendance;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StampCorrectionRequest>
 */
class StampCorrectionRequestFactory extends Factory
{
    protected $model = StampCorrectionRequest::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'attendance_id' => Attendance::factory(),
            'target_date' => now()->toDateString(),
            'status' => 0,
            'start_time' => '09:00',
            'end_time' => '18:00',
            'break_start_1' => null,
            'break_end_1' => null,
            'break_start_2' => null,
            'break_end_2' => null,
            //
        ];
    }
}
