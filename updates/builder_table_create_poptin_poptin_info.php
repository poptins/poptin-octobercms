<?php namespace Poptin\Poptin\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreatePoptinPoptinInfo extends Migration
{
    public function up()
    {
        Schema::create('poptin_poptin_info', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('user_id', 150)->nullable();
            $table->string('client_id', 150)->nullable();
            $table->string('token', 150)->nullable();
            $table->string('login_url', 150)->nullable();
            $table->string('account_email', 100)->nullable();
            $table->string('reg_date', 100)->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('poptin_poptin_info');
    }
}
