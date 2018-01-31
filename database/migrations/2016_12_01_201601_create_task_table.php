<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskTable extends Migration
{
    /**
     * 运行数据库迁移
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aso_task', function (Blueprint $table) {
            $table->increments('tid')->comment('任务的主键id');
            $table->string('appid', 100)->comment('广告id');
            $table->string('appkey', 100)->comment('关键词');
            $table->tinyInteger('state')->unsigned()->default(0)->comment('投放状态 0 未投放 1 已上线 2 已下线 3 已结束');
            $table->integer('count')->unsigned()->comment('投放总量');
            $table->integer('success_count')->unsigned()->default(0)->comment('成功数');
            $table->nullableTimestamps();
        });
    }

    /**
     * 回滚数据库迁移
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aso_task');
    }
}
