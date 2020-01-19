<?php namespace Shohabbos\Shopaholicapi\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class Migration102 extends Migration
{
    public function up()
    {
        Schema::table('users', function($table)
        {
            $table->string('device_type')->default('android');
            $table->string('device_id')->default('');
        });
    }

    public function down()
    {
        Schema::table('users', function($table)
        {
            $table->dropColumn('device_type');
            $table->dropColumn('device_id');
        });
    }
}