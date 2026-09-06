<?php

namespace Database\Factories\Setting\Models;

use Setting\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<Setting>
 */
class SettingFactory extends Factory
{

	protected $model = Setting::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $count = Setting::count() + 1;
        return [
					'title'=> $this->faker->str_random(45),
					'slug'=> $this->faker->str_random(45),
					'value'=> $this->faker->str_random(45),
					'type'=> $this->faker->str_random(45),
					'category'=> $this->faker->str_random(45),
					'is_editable'=> $this->faker->numberBetween(0,1),
					'description'=> $this->faker->realtext($maxNbChars = 150)
			];
    }

}
