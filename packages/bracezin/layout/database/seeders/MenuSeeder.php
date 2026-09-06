<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Layout\Imports\MenuImport;
use Layout\Models\Menu;
use Excel;

class MenuSeeder extends Seeder {
	/**
	 * Filename
	 *
	 */
	protected function getFileName() {
		return __DIR__ . '/layout_files/menu.csv';
	}

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		Excel::import(new MenuImport, $this->getFileName());
	}
}
