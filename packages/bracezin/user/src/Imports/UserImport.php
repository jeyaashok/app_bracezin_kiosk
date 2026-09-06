<?php

namespace User\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Setting;
use Str;
use User\Models\UserDetail;

class UserImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $users)
    {
        $userColumns = array_flip(Schema::getColumnListing('users'));

        foreach ($users as $user) {
            $userTypeSlug = Str::slug(trim($user['user_type']), '-');
            $email = trim($user['email']);
            $name = trim($user['username']);
            $password = trim($user['password']);

            $user_model = User::query()->updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => $password,
                ]
            );

            $extraData = [];
            if (isset($userColumns['type'])) {
                $extraData['type'] = $userTypeSlug;
            }
            if (isset($userColumns['username'])) {
                $extraData['username'] = $name;
            }
            if (isset($userColumns['mobile'])) {
                $extraData['mobile'] = trim($user['mobile']);
            }
            if (isset($userColumns['is_sysAdmin'])) {
                $extraData['is_sysAdmin'] = (int) trim($user['is_sysadmin'] ?? 0);
            }
            if (isset($userColumns['is_active'])) {
                $extraData['is_active'] = 1;
            }
            if (isset($userColumns['default_lang'])) {
                $extraData['default_lang'] = 'en';
            }

            if (! empty($extraData)) {
                DB::table('users')->where('id', $user_model->id)->update($extraData);
            }

            if ($user_model && $user_model->id) {
                $detailData = [
                    'user_id' => $user_model->id,
                    'code' => Setting::getCodeWithTimestamp($userTypeSlug),
                    'mobile' => trim($user['mobile']) || fake()->phoneNumber(),
                    'surname' => trim($user['surname']) || fake()->title(),
                    'initial' => trim($user['initial']) || fake()->randomLetter(),
                    'firstname' => trim($user['first_name']) || fake()->firstName(),
                    'lastname' => trim($user['last_name']) || fake()->lastName(),
                    'father_name' => fake()->name('male'),
                    'mother_name' => fake()->name('female'),
                    'home_phone' => fake()->phoneNumber(),
                    'emergency_phone' => fake()->phoneNumber(),
                    'company_name' => fake()->company(),
                    'company_register_no' => fake()->randomNumber(8, true),
                    'gst_number' => fake()->randomNumber(8, true),
                    'vat_number' => fake()->randomNumber(8, true),
                    'ein_number' => fake()->randomNumber(8, true),
                    'office_phone' => fake()->phoneNumber(),
                    'designation' => fake()->jobTitle(),
                    'department' => fake()->word(),
                    'qualification' => fake()->word(),
                    'business_brand_name' => fake()->company(),
                    'business_category' => fake()->word(),
                    'authority_person' => fake()->name(),
                    'authority_email' => fake()->email(),
                    'authority_mobile' => fake()->phoneNumber(),
                    'gender' => fake()->randomElement(['male', 'female']),
                    'date_of_birth' => fake()->date(),
                    'age' => fake()->numberBetween(18, 65),
                    'blood_group' => fake()->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
                    'marital_status' => fake()->randomElement(['single', 'married', 'divorced', 'widowed']),
                    'address_doorno' => fake()->buildingNumber(),
                    'address_line1' => fake()->streetAddress(),
                    'address_line2' => fake()->secondaryAddress(),
                    'landmark' => fake()->streetName(),
                    'area' => fake()->city(),
                    'city' => fake()->city(),
                    'state' => fake()->state(),
                    'country' => fake()->country(),
                    'zipcode' => fake()->postcode(),
                    'latitude' => fake()->latitude(),
                    'longitude' => fake()->longitude(),
                    'address' => fake()->address(),
                    'geo_location' => json_encode(['lat' => fake()->latitude(), 'lng' => fake()->longitude()]),
                    'image_api' => json_encode(['url' => fake()->imageUrl()]),
                    'json' => json_encode(['key' => 'value']),
                    'short_note' => fake()->sentence(),
                    'detail_about' => fake()->paragraph(),
                ];
                $userDetail = $this->storeUserDetail($detailData);
            }

            $roles = explode('|', trim($user['roles'] ?? ''));
            foreach ($roles as $role) {
                $roleName = trim($role);
                $user_model->assignRole($roleName);
            }
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
