<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Directory\Imports\MediaImport;
use Excel;

class MediaSeeder extends Seeder {
	/**
	 * Filename
	 *
	 */
	protected function getFileName() {
		return __DIR__ . '/files/medias.csv';
	}

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		Excel::import(new MediaImport, $this->getFileName());
	}
}
