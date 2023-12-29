<?php

namespace Database\Factories;

use App\Enums\OvertimeReason;
use App\Models\Overtime;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class OvertimeFactory extends Factory
{
    protected $model = Overtime::class;

    public function definition(): array
    {
        return [
            'reason' => $this->faker->randomElement(array_keys(OvertimeReason::toArray())),
            'remarks' => $this->faker->realText(),
            'updated_at' => Carbon::now(),
        ];
    }
}
