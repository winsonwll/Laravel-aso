<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Baseconfig extends Model
{
    protected $table = 'baseconfig';
    protected $primaryKey = 'bid';

    //该模型是否被自动维护时间戳
    public $timestamps = false;

    //不可被批量赋值的属性。即所有的属性都可以被批量赋值
    protected $guarded = [];
}
