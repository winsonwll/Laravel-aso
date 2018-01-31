<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Task;
use Excel;

class ReportController extends Controller
{
    /**
     * 显示财务报表列表页
     */
    public function index(Request $request)
    {
        //读取数据 并且分页
        $list = Task::whereNotNull('first_time')
            ->where(function($query) use ($request){
                if($request->input('keyword')){
                    $query->where('appkey','like','%'.$request->input('keyword').'%');
                }
            })
            ->orderBy('tid', 'desc')
            ->paginate(10);

        return view('admins.report.index',['list'=>$list, 'request'=>$request->all()]);
    }

    /**
     * 导出Excel文件
     */
    public function export($id)
    {
        $cellData = [
            ['AppID','任务名称','投放总量','已投放量','投放单价(元)','设备','苹果账号','开始时间','完成时间','完成状态']
        ];

        $res = Task::find($id);

        //读取数据
        $list = \DB::table('record')
            ->where('tid', $id)
            ->join('account', 'record.aid', '=', 'account.aid')
            ->join('client', 'record.cid', '=', 'client.cid')
            ->get();

        foreach($list as $k => $v) {
            $cellData[] = [
                $res->appid,
                $res->appkey,
                $res->count,
                $res->success_count,
                $res->price,
                $v->idfa,
                $v->apple_id,
                $v->start_time,
                $v->end_time,
                $v->end_time
            ];
        }
        
        $res->last_time = empty($res->last_time) ? '' : $res->last_time;

        Excel::create($res->appkey.'财务报表_'.$res->first_time.'-'.$res->last_time,function($excel) use ($cellData){
            $excel->sheet('report', function($sheet) use ($cellData){
                $sheet->rows($cellData);
            });
        })->export('xls');
    }
}
