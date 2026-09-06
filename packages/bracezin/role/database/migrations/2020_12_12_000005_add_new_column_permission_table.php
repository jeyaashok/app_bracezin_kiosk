<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnPermissionTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		if (!Schema::hasTable('permissions')) {
			return;
		}

		Schema::table("permissions", function (Blueprint $table) {
			if (!Schema::hasColumn('permissions', 'deleted_at')) {
				$table->softDeletes();
			}
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		if (!Schema::hasTable('permissions')) {
			return;
		}

		Schema::table("permissions", function (Blueprint $table) {
			if (Schema::hasColumn('permissions', 'module')) {
				$table->dropColumn('module');
			}
			if (Schema::hasColumn('permissions', 'deleted_at')) {
				$table->dropSoftDeletes();
			}
		});
	}
}
