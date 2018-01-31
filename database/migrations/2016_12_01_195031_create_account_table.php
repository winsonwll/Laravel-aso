<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountTable extends Migration
{
    /**
     * 运行数据库迁移
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aso_acount', function (Blueprint $table) {
            $table->increments('aid')->comment('账号的主键id');
            $table->string('apple_id', 100)->comment('苹果账号');
            $table->string('apple_pwd', 100)->comment('苹果账号密码');
            $table->tinyInteger('state')->unsigned()->default(1)->comment('账号状态 1 可用 2 错误账号');
            $table->nullableTimestamps();
            $table->unique('apple_id');
        });
    }

    /**
     * 回滚数据库迁移
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aso_acount');
    }
}
