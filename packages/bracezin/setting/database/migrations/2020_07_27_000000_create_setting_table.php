<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingTable extends Migration
{
    /**
     * Schema table name to migrate
     *
     * @var string
     */
    public $set_schema_table = 'setting';

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
            $table->bigIncrements('id');
            $table->string('title')->unique();
            $table->string('slug')->unique();
            $table->string('value');
            $table->integer('type')->unsigned()->default(1);
            $table->string('category')->nullable();
            $table->boolean('is_editable')->default(1);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by')->unsigned()->default('1');
            $table->unsignedBigInteger('updated_by')->unsigned()->default('1');
            $table->timestamps();
            $table->softDeletes();
        });

        // DB::connection()->getPdo()->exec("
        //     CREATE TRIGGER setting_slug BEFORE INSERT ON `setting` FOR EACH ROW
        //     BEGIN
        //        SET NEW.slug = REPLACE(LOWER(NEW.title), ' ', '-');
        //        SET NEW.slug = REPLACE(NEW.slug, '&', '');
        //         SET NEW.slug = REPLACE(NEW.slug, ':', '');
        //         SET NEW.slug = REPLACE(NEW.slug, ')', '');
        //         SET NEW.slug = REPLACE(NEW.slug, '(', '');
        //         SET NEW.slug = REPLACE(NEW.slug, ',', '-');
        //         SET NEW.slug = REPLACE(NEW.slug, '/', '-');
        //         SET NEW.slug = REPLACE(NEW.slug, '&#39;', '');
        //         SET NEW.slug = REPLACE(NEW.slug, '!', '');
        //         SET NEW.slug = REPLACE(NEW.slug, '.', '');
        //         SET NEW.slug = REPLACE(NEW.slug, '--', '-');
        //         SET NEW.slug = REPLACE(NEW.slug, '--', '-');
        //     END;
        // ");

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
