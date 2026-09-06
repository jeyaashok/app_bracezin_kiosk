<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use User\Imports\UserImport;
use Excel;

class UsersSeeder extends Seeder {
	/**
	 * Filename
	 *
	 */
	protected function getFileName() {
		return __DIR__ . '/user_files/users.csv';
	}
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		Excel::import(new UserImport, $this->getFileName());
	}
}
