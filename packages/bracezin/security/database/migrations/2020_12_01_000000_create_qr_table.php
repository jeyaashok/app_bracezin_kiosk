<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQrTable extends Migration
{
     /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'qr';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->set_schema_table)) {
            return;
        }

        Schema::create($this->set_schema_table, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id')->unsigned();
            $table->string('code', 250)->unique();
            $table->string('qr_code', 250)->unique();
            $table->unsignedBigInteger('resource_id')->nullable();
			$table->string('resource_type')->nullable();
			$table->index(['resource_id', 'resource_type']);
            $table->boolean('is_refreshable')->default(0);
            $table->boolean('is_used')->default(0);
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
    public function down()
    {
        Schema::dropIfExists($this->set_schema_table);
    }
}
