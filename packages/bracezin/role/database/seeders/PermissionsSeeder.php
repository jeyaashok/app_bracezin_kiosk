<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Role\Imports\PermissionImport;
use Excel;

class PermissionsSeeder extends Seeder {
	/**
	 * Filename
	 *
	 */
	protected function getFileName() {
		return __DIR__ . '/role_files/permissions.csv';
	}
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		Excel::import(new PermissionImport, $this->getFileName());
	}
}
