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
        $start = fake()->dateTimeBetween('-1 month', 'now');
        $end = (clone $start)->modify('+1 hour');

        return [
            'start_time' => $start,
            'end_time' => $end,
        ];
            //

    }
}
