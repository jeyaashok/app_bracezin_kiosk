<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Notification\Models\Notify;
use Excel;

class NotifySeeder extends Seeder {
	/**
	 * Run the database seeds.
	 * @return void
	 */
	public function run() {
		Notify::factory()->count(15)->create();
	}
}
