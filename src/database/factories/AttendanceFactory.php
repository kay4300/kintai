<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $day = 0;

        $date = now()->subDays($day++);

        return [
            'date' => $date->format('Y-m-d'),
            'start_time' => $date->format('Y-m-d 09:00:00'),
            'end_time' => $date->format('Y-m-d 18:00:00'),
        ];
            //
        
    }
}
