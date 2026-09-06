<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Layout\Imports\MenuGroupImport;
use Layout\Models\MenuGroup;
use Excel;

class MenuGroupSeeder extends Seeder {
	/**
	 * Filename
	 *
	 */
	protected function getFileName() {
		return __DIR__ . '/layout_files/menu_group.csv';
	}

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		Excel::import(new MenuGroupImport, $this->getFileName());
	}

}
