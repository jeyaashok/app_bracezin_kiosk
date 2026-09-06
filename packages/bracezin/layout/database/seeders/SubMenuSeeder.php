<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Layout\Imports\SubMenuImport;
use Layout\Models\SubMenu;
use Excel;

class SubMenuSeeder extends Seeder {
	/**
	 * Filename
	 *
	 */
	protected function getFileName() {
		return __DIR__ . '/layout_files/sub_menu.csv';
	}

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		Excel::import(new SubMenuImport, $this->getFileName());
	}
}
