<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminTable extends Migration
{
    /**
     * 运行数据库迁移
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aso_admin', function (Blueprint $table) {
            $table->increments('id')->comment('管理员的主键id');
            $table->char('name', 12)->comment('管理员名');
            $table->char('pwd', 100)->comment('密码');
            $table->tinyInteger('auth')->unsigned()->default(0)->comment('权限 0：普通管理员  1：超级管理员');
            $table->nullableTimestamps();
            $table->unique('name');
        });
    }

    /**
     * 回滚数据库迁移
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aso_admin');
    }
}
