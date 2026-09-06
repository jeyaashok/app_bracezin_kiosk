<?php

namespace Database\Seeders;

use Excel;
use Illuminate\Database\Seeder;
use Setting\Imports\SettingImport;
use Setting\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Filename
     */
    protected function getFileName()
    {
        return __DIR__.'/setting_files/setting.csv';
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Excel::import(new SettingImport, $this->getFileName());

        // for ($i = 0; $i <= 25; $i++) {
        // 	$setting = Setting::factory()->create();
        // }
    }
}
