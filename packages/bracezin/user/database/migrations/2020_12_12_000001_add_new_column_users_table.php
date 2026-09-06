<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnUsersTable extends Migration
{
    /**
     * Schema table name to migrate
     *
     * @var string
     */
    public $set_schema_table = 'users';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->set_schema_table, function (Blueprint $table) {
            $table->string('username')->nullable();
            $table->string('type')->nullable()->default('staff');
            $table->string('mobile')->unique()->nullable();
            $table->string('avatar_url')->nullable();
            $table->string('default_lang')->default('en')->nullable();
            $table->string('default_currency')->default('usd')->nullable();
            $table->boolean('is_sysAdmin')->default('0');
            $table->boolean('is_active')->default('1');
            $table->boolean('is_email_verified')->default('0');
            $table->boolean('is_mobile_verified')->default('0');
            $table->boolean('do_change_password')->default('0');
            $table->boolean('do_reset_password')->default('0');
            $table->boolean('is_sound_notify')->default('1');
            $table->boolean('is_desktop_notify')->default('1');
            $table->boolean('is_web_notify')->default('1');
            $table->datetime('mobile_verified_at')->nullable();
            $table->float('commission_ratio')->nullable()->default('0');
            $table->unsignedBigInteger('created_by')->unsigned()->nullable()->default('1');
            $table->unsignedBigInteger('updated_by')->unsigned()->nullable()->default('1');
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
        Schema::table($this->set_schema_table, function (Blueprint $table) {
            $table->dropColumn('module');
            $table->dropSoftDeletes();
        });
    }
}
