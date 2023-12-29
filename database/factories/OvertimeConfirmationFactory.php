<?php

namespace Database\Factories;

use App\Models\OvertimeConfirmation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class OvertimeConfirmationFactory extends Factory
{
    protected $model = OvertimeConfirmation::class;

    public function definition(): array
    {
        return [
            'user_id' => $this->faker->randomNumber(),
            'confirmed_at' => $this->faker->boolean ? Carbon::now() : null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
