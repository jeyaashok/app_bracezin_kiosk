<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaTable extends Migration
{
    /**
     * Schema table name to migrate
     *
     * @var string
     */
    public $set_schema_table = 'media';

    /**
     * Run the migrations.
     *
     * @table company
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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('resource_id')->nullable();
            $table->string('resource_type')->nullable();
            $table->index(['resource_id', 'resource_type']);
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('model_type')->nullable();
            $table->index(['model_id', 'model_type']);
            $table->unsignedBigInteger('shared_by')->nullable();
            $table->foreign('shared_by')->references('id')->on('users');
            $table->string('document_type')->nullable();
            $table->string('name')->nullable();
            $table->string('filename')->nullable();
            $table->text('location')->nullable();
            $table->text('url')->nullable();
            $table->string('type')->default('document');
            $table->string('mime')->nullable();
            $table->string('disk')->nullable();
            $table->string('etag')->nullable();
            $table->string('extension')->nullable();
            $table->unsignedInteger('size')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(1);
            $table->boolean('is_primary')->default(0);
            $table->boolean('is_favorite')->default(0);
            $table->boolean('is_local_server')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
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
