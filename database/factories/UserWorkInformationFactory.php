<?php

namespace Database\Factories;

use App\Enums\WorkingSystem;
use App\Models\UserWorkInformation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class UserWorkInformationFactory extends Factory
{
    protected $model = UserWorkInformation::class;

    public function definition(): array
    {
        $workingHours = ceil((random_int(30, 80) / 5)) * 5 / 10;
        $usedWorkingHours = ceil($workingHours);

        return [
            'employed_at' => $this->faker->dateTimeBetween('-10 years', '-2 years'),
            'working_hours' => $workingHours,
            'used_working_hours' => $usedWorkingHours,
            'working_system' => $this->faker->randomElement(array_keys(WorkingSystem::toArray())),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
