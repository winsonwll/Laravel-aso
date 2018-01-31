<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientTable extends Migration
{
    /**
     * 运行数据库迁移
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aso_client', function (Blueprint $table) {
            $table->increments('cid')->comment('客户端的主键id');
            $table->string('idfa', 100)->comment('客户端idfa');
            $table->nullableTimestamps();
            $table->unique('idfa');
        });
    }

    /**
     * 回滚数据库迁移
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aso_client');
    }
}
