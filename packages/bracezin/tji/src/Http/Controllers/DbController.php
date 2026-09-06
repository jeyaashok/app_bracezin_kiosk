<?php

namespace Tji\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tji\Http\Controllers\PackageController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Core;
use Person;
use Storage;

class DbController extends PackageController {

	public function addColumn() {
		$tables = [
			// 0 => (object)['tableName' => 'setting_field', 'columnName' => 'json', 'dataType' => 'text', 'default' => 'null', 'columnTable' => 'template' ],
		];
		foreach ($tables as $key => $table) {
			$tableName = $table->tableName;
			$columnName = $table->columnName;
			$dataType = $table->dataType;
			$default = $table->default;
			$columnTable = $table->columnTable;

			if(Schema::hasTable($tableName) && !Schema::hasColumn($tableName, $columnName)) {
				Schema::table($tableName, function (Blueprint $table) use($dataType, $columnName, $default, $columnTable) {
					switch ($dataType) {
						case 'string':
							$table->string($columnName)->nullable()->default(($default === 'null') ? null : $default);
							break;
						case 'text':
							$table->text($columnName)->nullable()->default(($default === 'null') ? null : $default);
							break;
						case 'boolean':
							$table->boolean($columnName)->nullable()->default(($default === 'null') ? null : $default);
							break;
						case 'relationInt':
							$table->unsignedBigInteger($columnName)->unsigned()->nullable();
							$table->foreign($columnName)->references('id')->on($columnTable);
							break;
						case 'timestamp':
						case 'date':
							$table->timestamp($columnName)->nullable()->default(($default === 'null') ? null : $default);
							break;
						default:
							$table->string($columnName)->nullable()->default(($default === 'null') ? null : $default);
							break;
					}
				});
			}
		}
		return 'ok';
	}

	public function addColumnOnClientsTables() {
		return $this->addColumn();
	}


	public function dropTable($tableName) {
		Schema::dropIfExists($tableName);
		return 'ok';
	}

}
