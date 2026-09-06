<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        Model::unguard();

        $isProduction = env('IS_PRODUCTION', false);

        if ($isProduction) {
            // Production Seed
            $this->call([
                SettingSeeder::class,

                PermissionsSeeder::class,
                RolesSeeder::class,
                RolePermissionsSeeder::class,
                UsersSeeder::class,

                MediaSeeder::class,

                MenuGroupSeeder::class,
                MenuSeeder::class,
                SubMenuSeeder::class,

            ]);
        } else {
            // // Test Seed
            $this->call([

                SettingSeeder::class,

                PermissionsSeeder::class,
                RolesSeeder::class,
                RolePermissionsSeeder::class,
                UsersSeeder::class,

                MediaSeeder::class,

                MenuGroupSeeder::class,
                MenuSeeder::class,
                SubMenuSeeder::class,

                CommentSeeder::class,
                FeedbackSeeder::class,
            ]);
        }

        Model::reguard();

        // // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
