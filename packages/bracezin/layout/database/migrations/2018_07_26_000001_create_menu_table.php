<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuTable extends Migration {
	/**
	 * Schema table name to migrate
	 * @var string
	 */
	public $set_schema_table = 'menu';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		if (Schema::hasTable($this->set_schema_table)) {
			return;
		}

		Schema::create($this->set_schema_table, function (Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->bigIncrements('id');
			$table->unsignedBigInteger('menu_group_id')->unsigned();
			$table->foreign('menu_group_id')->references('id')->on('menu_group');
			$table->string('title');
			$table->string('slug')->unique();
			$table->string('type', 45);
			$table->string('translate', 50)->nullable();
			$table->string('icon')->nullable();
			$table->string('image')->nullable();
			$table->string('url')->nullable();
			$table->string('route')->nullable();
			$table->integer('order')->unsigned();
			$table->string('role')->nullable();
			$table->string('permission')->nullable();
			$table->text('json')->nullable();
			$table->boolean('is_hidden')->default(0);
			$table->boolean('is_open_new_tab')->default(0);
			$table->boolean('is_active')->default(1);
			$table->unsignedBigInteger('created_by')->unsigned()->default(1);
			$table->unsignedBigInteger('updated_by')->unsigned()->default(1);
			$table->timestamps();
			$table->softDeletes();
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists($this->set_schema_table);
	}
}
