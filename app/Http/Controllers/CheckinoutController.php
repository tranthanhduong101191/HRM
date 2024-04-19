<?php

namespace App\Http\Controllers;

use App\Checkinout;
use App\DataTables\CheckinHistoryDatatable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use \DataTables;
use Maatwebsite\Excel\Cell;

class CheckinoutController extends Controller
{
    public function index()
    {
        return v('checkinout.index');
    }

    public function checkinoutData()
    {
//        return DataTables::of(Checkinout::all())->make('true');
        $data   =   Checkinout::with('user');

        if(auth()->user()->group_id >= 4){
            $data   =   $data->where('MaChamCong', auth()->user()->uid);
        }

        if(!empty($userid = request('user_id')))
            $data   =   $data->where('MaChamCong', $userid);
        if(!empty($fromDate =   request('fromDate')))
            $data   =   $data->where('GioCham', '>=', Carbon::createFromFormat('d/m/Y',$fromDate)->startOfDay());
        if(!empty($toDate =   request('toDate')))
            $data   =   $data->where('GioCham', '<=', Carbon::createFromFormat('d/m/Y',$toDate)->endOfDay());

        // if(auth()->user()->group_id == 3){
        //     $data    =   $data->whereHas('user',function($q){
        //         $q->where('branch_id', auth()->user()->branch_id);
        //     });
        //     log::info($data);
        // }

        $result =   DataTables::of($data)
            ->addColumn('username', function(Checkinout $c){
                return $c->user?$c->user->name:$c->MaChamCong;
            })
            ->addColumn('machinename', function(Checkinout $c){
                return $c->maychamcong?$c->maychamcong->TenMCC:$c->MaSoMay;
            })
            ->editColumn('NgayCham', function(Checkinout $c){
                return Carbon::parse($c->NgayCham)->format('d/m');
            })
            ->editColumn('GioCham', function(Checkinout $c){
                return Carbon::parse($c->GioCham)->format('H:i:s');
            })->order(function ($query) {
                    $query->orderBy('GioCham', 'desc');
            });
        return $result->make(true);

    }
}
