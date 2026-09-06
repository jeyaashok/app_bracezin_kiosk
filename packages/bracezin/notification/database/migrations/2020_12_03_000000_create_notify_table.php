<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotifyTable extends Migration
{
     /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'notify';

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
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->string('type', 250)->nullable();
            $table->string('title', 250);
            $table->string('description')->nullable();
            $table->string('icon',250)->nullable();
            $table->string('color',250)->nullable();
            $table->boolean('is_notified')->default(0);
            $table->boolean('is_read')->default(0);
            $table->boolean('is_sound_notify')->default(0);
            $table->boolean('is_web_notify')->default(0);
            $table->boolean('is_desktop_notify')->default(0);     
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
