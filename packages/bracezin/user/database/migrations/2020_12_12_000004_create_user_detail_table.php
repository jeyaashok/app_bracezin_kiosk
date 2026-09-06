<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDetailTable extends Migration
{
    /**
     * Schema table name to migrate
     *
     * @var string
     */
    public $set_schema_table = 'user_detail';

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
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('code')->nullable();

            $table->string('initial')->nullable();
            $table->string('surname')->nullable();
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();

            $table->string('mobile')->nullable();
            $table->string('home_phone')->nullable();
            $table->string('emergency_phone')->nullable();

            $table->string('company_name')->nullable();
            $table->string('company_register_no')->nullable();
            $table->string('gst_number')->nullable();
            $table->string('vat_number')->nullable();
            $table->string('ein_number')->nullable();
            $table->string('office_phone')->nullable();
            $table->string('designation')->nullable();
            $table->string('department')->nullable();
            $table->string('qualification')->nullable();
            $table->string('business_brand_name')->nullable();
            $table->string('business_category')->nullable();
            $table->string('authority_person')->nullable();
            $table->string('authority_email')->nullable();
            $table->string('authority_mobile')->nullable();

            $table->string('gender')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->integer('age')->unsigned()->nullable();
            $table->string('blood_group')->nullable();
            $table->string('marital_status')->nullable();

            $table->string('address_doorno')->nullable();
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('landmark')->nullable();
            $table->string('area')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('zipcode')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('address')->nullable();
            $table->longtext('geo_location')->nullable();

            $table->text('image_api')->nullable();
            $table->longtext('json')->nullable();
            $table->longtext('short_note')->nullable();
            $table->longtext('detail_about')->nullable();

            $table->unsignedBigInteger('created_by')->unsigned()->nullable();
            $table->unsignedBigInteger('updated_by')->unsigned()->nullable();
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
