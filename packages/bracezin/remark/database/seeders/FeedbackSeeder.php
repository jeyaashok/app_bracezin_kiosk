<?php

namespace Database\Seeders;
use Remark\Models\feedback;
use Illuminate\Database\Seeder;
use Excel;

class FeedbackSeeder extends Seeder {
	/**
	 * Filename
	 *
	 */
	protected function getFileName() 
	{
		return __DIR__ . '/remark_files/feedback.csv';
	}
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		Feedback::factory()->count(10)->create();
	}
}
