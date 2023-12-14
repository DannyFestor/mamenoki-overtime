<?php

namespace Database\Factories;

use App\Models\Overtime;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class OvertimeFactory extends Factory
{
    protected $model = Overtime::class;

    public function definition(): array
    {
        return [
            'user_id' => $this->faker->randomNumber(),
            'year' => $this->faker->randomNumber(),
            'month' => $this->faker->randomNumber(),
            'from_hours' => $this->faker->randomNumber(),
            'from_minutes' => $this->faker->randomNumber(),
            'to_hours' => $this->faker->randomNumber(),
            'to_minutes' => $this->faker->randomNumber(),
            'reason' => $this->faker->randomNumber(),
            'remarks' => $this->faker->word(),
            'created_user_id' => $this->faker->randomNumber(),
            'applicant_user_id' => $this->faker->randomNumber(),
            'applied_at' => Carbon::now(),
            'approval_user_id' => $this->faker->randomNumber(),
            'approved_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
