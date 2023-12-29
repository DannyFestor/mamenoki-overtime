<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Overtime;
use App\Models\OvertimeConfirmation;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Danny',
            'email' => 'danny@festor.info',
        ]);

        User::factory(10)->create();

        $users = User::all();

        $faker = Factory::create('ja');

        $months = 50;

        foreach ($users as $user) {
            $overtimeConfirmationDate = now()->subMonths($months);
            $transferRemark = $faker->realText();
            for ($i = 0; $i < $months; $i++) {
                $remark = $transferRemark;
                $transferRemark = $faker->realText();

                $overtimeConfirmation = OvertimeConfirmation::factory()->for($user)->create([
                    'year' => $overtimeConfirmationDate->year,
                    'month' => $overtimeConfirmationDate->month,
                    'remarks' => $remark,
                    'transfer_remarks' => $transferRemark,
                ]);

                for ($j = 0; $j < random_int(0, 10); $j++) {
                    $overtimeDate = Carbon::parse($faker->dateTimeBetween($overtimeConfirmationDate, $overtimeConfirmationDate->clone()->addMonth()));
                    $startTime = Carbon::parse($faker->time());
                    $endTime = $startTime->clone()->addMinutes(random_int(1, 240));

                    $isApplied = $faker->boolean;
                    $appliedDate = $overtimeDate->clone()->addMinutes(random_int(120, 1200));
                    $isApproved = ($isApplied && $faker->boolean);
                    $approvedDate = $appliedDate->clone()->addMinutes(random_int(120, 1200));

                    Overtime::factory()->for($overtimeConfirmation)->create([
                        'date' => $overtimeDate->format('Y-m-d'),
                        'time_from' => $startTime->format('H:i'),
                        'time_until' => $endTime->format('H:i'),
                        'created_at' => $overtimeDate,
                        'created_user_id' => $users->random()->id,
                        'applied_at' => $isApplied ? $appliedDate : null,
                        'applicant_user_id' => $isApplied ? $user->id : null,
                        'approved_at' => $isApproved ? $approvedDate : null,
                        'approval_user_id' => $isApproved ? 1 : null,
                    ]);
                }

                $overtimeConfirmationDate->addMonth();
            }
        }
    }
}
