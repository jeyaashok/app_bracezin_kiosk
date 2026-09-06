<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Role\Imports\RoleImport;
use Excel;

class RolesSeeder extends Seeder {
	/**
	 * Filename
	 *
	 */
	protected function getFileName() {
		return __DIR__ . '/role_files/roles.csv';
	}
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		Excel::import(new RoleImport, $this->getFileName());
	}
}
