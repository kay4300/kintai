<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BreakTime>
 */
class BreakTimeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = now()->setTime(rand(9, 17), rand(0, 59));

        return [
            'start_time' => $start,
            'end_time'   => (clone $start)->addMinutes(rand(15, 60)),
        
           
        ];
            //

    }
}
