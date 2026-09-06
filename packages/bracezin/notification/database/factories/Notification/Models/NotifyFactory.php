<?php

namespace Database\Factories\Notification\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Faker\Generator as Faker;
use Notification\Models\Notify;
use App\Models\User;

class NotifyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     * @var string
     */
    protected $model = Notify::class;

    /**
     * Define the model's default state.
     * @return array
     */
    public function definition()
    {
    	$userIds = User::get()->pluck('id')->toArray();
		return [
			'user_id' => $this->faker->randomElement($userIds),
			'title' => $this->faker->sentence(5),
			'type' => $this->faker->randomElement(['info', 'warning', 'success', 'danger']),
			'description' => $this->faker->sentence(8),
			'icon' => $this->faker->randomElement(['fa-info-circle',' fa-exclamation-circle','fa-check-circle-o','fa-times-circle',]),
			'color' => $this->faker->hexcolor(),
			'is_notified' => $this->faker->numberBetween(0,1),
			'is_read' => $this->faker->numberBetween(0,1),
			'is_sound_notify' => $this->faker->numberBetween(0,1),
			'is_web_notify' => $this->faker->numberBetween(0,1),
			'is_desktop_notify' => $this->faker->numberBetween(0,1),
		 ];
    }
}
