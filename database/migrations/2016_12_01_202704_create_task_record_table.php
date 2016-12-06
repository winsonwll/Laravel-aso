<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskRecordTable extends Migration
{
    /**
     * 运行数据库迁移
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aso_task_record', function (Blueprint $table) {
            $table->increments('rid')->comment('任务记录的主键id');
            $table->integer('tid')->unsigned()->comment('广告id');
            $table->string('appid', 100)->comment('appid');
            $table->integer('aid')->unsigned()->comment('账号id');
            $table->integer('cid')->unsigned()->comment('设备id');
            $table->tinyInteger('status')->unsigned()->default(0)->comment('任务完成状态 0 未开始 1 已开始 2 成功 3 失败');
            $table->tinyInteger('code')->unsigned()->nullable()->comment('失败状态码  204 苹果账号或密码错误 205 账号禁用 206 ip错误 207 搜索错误 208 购买错误 209 超时');
            $table->timestamp('start_time')->nullable()->comment('开始任务时间');
            $table->timestamp('end_time')->nullable()->comment('完成任务时间');
        });
    }

    /**
     * 回滚数据库迁移
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aso_task_record');
    }
}
