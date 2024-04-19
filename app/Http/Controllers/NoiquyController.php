<?php

namespace App\Http\Controllers;
use App\Http\Requests\NoiquyRequest;
use App\Noiquy;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class NoiquyController extends Controller
{
    public function getList()
    {
        return v('noiquy.noiquy');
    }

    public function dataList(){
        $data   =   Noiquy::get();
        $result = Datatables::of($data)
            ->addColumn('manage', function(Noiquy $noiquy) {
                return a('config/noiquy/del', 'id='.$noiquy->id,trans('g.delete'), ['class'=>'btn btn-xs btn-danger'],'#',"return bootbox.confirm('".trans('system.delete_confirm')."', function(result){if(result==true){window.location.replace('".asset('config/noiquy/del?id='.$noiquy->id)."')}})").'  '.a('config/noiquy/edit', 'id='.$noiquy->id,trans('g.edit'), ['class'=>'btn btn-xs btn-default']);
            })->rawColumns(['manage']);

        return $result->make(true);
    }

    public function getCreate()
    {
        return v('noiquy.create');
    }

    public function postCreate(NoiquyRequest $request)
    {
        $data   =   new Noiquy();
        $data->title   =   $request->title;
        $data->description   =   $request->description;        
        $data->created_at   =   Carbon::now();
        $data->save();
        set_notice(trans('Thêm nội quy mới thành công'), 'success');
        return redirect()->back();
    }

    public function getEdit()
    {
        $data   =   Noiquy::find(request('id'));
        if(!empty($data)){
            return v('noiquy.edit', compact('data'));
        }else{
            set_notice(trans('system.not_exist'), 'warning');
            return redirect()->back();
        }
    }
    public function postEdit(NoiquyRequest $request)
    {
        $data   =   Noiquy::find($request->id);
        if(!empty($data)){
            $data->title   =   $request->title;
            $data->description   =   $request->description;
            $data->save();
            set_notice(trans('Sửa thành công!'), 'success');
        }else
            set_notice(trans('system.not_exist'), 'warning');
        return redirect()->back();
    }
    public function getDelete()
    {
        $data   =   Noiquy::find(request('id'));
        if(!empty($data)){
            $data->delete();
            set_notice(trans('Xóa thành công'), 'success');
        }else
            set_notice(trans('system.not_exist'), 'warning');
        return redirect()->back();
    }
}
