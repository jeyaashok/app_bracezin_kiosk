<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Role\Imports\RolePermissionImport;
use Excel;

class RolePermissionsSeeder extends Seeder {
	/**
	 * Filename
	 *
	 */
	protected function getFileName() {
		return __DIR__ . '/role_files/role-permissions.csv';
	}
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		Excel::import(new RolePermissionImport, $this->getFileName());
	}
}
