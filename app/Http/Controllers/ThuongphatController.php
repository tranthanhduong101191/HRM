<?php

namespace App\Http\Controllers;

use App\Service\ThuongphatService;
use App\Thuongphat;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use \DataTables;

class ThuongphatController extends Controller
{
    private $service;
    public function __construct()
    {
        $this->service  =   new ThuongphatService();
    }
    public function index()
    {
        return v('thuongphat.index');
    }

    public function data()
    {
        $data   =   Thuongphat::with('user', 'issue_user');

        if(!empty($userid = request('user_id')))
            $data   =   $data->where('user_id', $userid);
        if(!empty($fromDate =   request('fromDate')))
            $data   =   $data->where('issued_at', '>=', Carbon::createFromFormat('d/m/Y',$fromDate)->startOfDay());
        if(!empty($toDate =   request('toDate')))
            $data   =   $data->where('issued_at', '<=', Carbon::createFromFormat('d/m/Y',$toDate)->endOfDay());
        if(!empty($reason = request('reason')))
            $data   =   $data->where('reason','LIKE', $reason);

        if(auth()->user()->group_id == 3){
            $data    =   $data->whereHas('user',function($q){
                $q->where('branch_id', auth()->user()->branch_id);
            });
        }
        if(auth()->user()->group_id>=4){
            $data   =   $data->where('user_id', auth()->user()->id);
        }

        $result =   DataTables::of($data)
            ->addColumn('user', function(Thuongphat $c){
                return ($c->user?$c->user->uid.'-'.$c->user->name:$c->user_id);
            })
            ->editColumn('issued_at', function(Thuongphat $c){
                return Carbon::parse($c->issued_at)->format('d/m/Y');
            })
            ->editColumn('type', function(Thuongphat $c){
                return $c->type==1?'Phạt':'Thưởng';
            })
            ->editColumn('amount', function(Thuongphat $c){
                return number_format($c->amount);
            })
            ->addColumn('manage', function(Thuongphat $c){
                return a('thuong-phat/del', 'id='.$c->id,trans('g.delete'), ['class'=>'btn btn-xs btn-danger'],'#',"return bootbox.confirm('".trans('system.delete_confirm')."', function(result){if(result==true){window.location.replace('".asset('thuong-phat/del?id='.$c->id)."')}})").' 
                '.a('thuong-phat/edit', 'id='.$c->id,trans('g.edit'), ['class'=>'btn btn-xs btn-default']);
            })->rawColumns(['manage']);
        return $result->make(true);
    }

    public function store()
    {
        $request    =   \request()->only(['uid','issued_at','type','amount','reason']);
        $thuongphat =   $this->service->store($request);
        set_notice($thuongphat['message'], $thuongphat['alert']);
        return redirect()->back();
    }

    public function update()
    {

    }

    public function read()
    {

    }

    public function delete()
    {
        $del    =   $this->service->delete(request('id'));
        if($del['status'] == 0){
            set_notice($del['message'], 'success');
        } else set_notice($del['message'], 'danger');
        return redirect()->back();
    }
}
