<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use User\Models\UserDetail;

class AgentSeeder extends Seeder
{
    /**
     * Filename
     */
    protected function getFileName()
    {
        return __DIR__.'/user_files/users.csv';
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $commissionRatio = fake()->randomFloat(2, 0.2, 0.5); // Set the commission ratio to a random value between 0.2 and 0.5
        $users = User::factory()->count(50)->create(['type' => 'agent', 'is_active' => 0, 'commission_ratio' => $commissionRatio]);
        $userCount = User::count() + 1;

        foreach ($users as $user) {
            $detailData = [
                'code' => 'AGT-'.(string) $userCount,
                'user_id' => $user->id,
                'mobile' => $user->mobile,
                'initial' => fake()->randomLetter(),
                'surname' => fake()->title(),
                'firstname' => fake()->firstName(),
                'lastname' => fake()->lastName(),
                'company_name' => fake()->company(),
                'address_doorno' => fake()->buildingNumber(),
                'address_line1' => fake()->streetName(),
                'address_line1' => fake()->secondaryAddress(),
                'city' => fake()->city(),
                'state' => fake()->state(),
                'country' => fake()->country(),
                'zipcode' => fake()->postcode(),
            ];
            $userDetail = $this->storeUserDetail($detailData);
            $user->assignRole('agent');
            $user->creditWallet($user, fake()->numberBetween(100, 1000), $user, 'Credited with initial amount');
        }
    }

    public function storeUserDetail($data)
    {
        $userDetail = UserDetail::query()->updateOrCreate(
            ['user_id' => $data['user_id']],
            $data
        );

        return $userDetail;
    }
}
