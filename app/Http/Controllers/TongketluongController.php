<?php

namespace App\Http\Controllers;

use App\Chamcong;
use App\Group;
use App\Service\TongketluongService;
use App\Thuongphat;
use App\Tongketluong;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use \DataTables;
use Illuminate\Support\Facades\Log;

class TongketluongController extends Controller
{
    private $service;
    public function __construct()
    {
        $this->service  =   new TongketluongService();
    }
    public function index()
    {
        $data   =   Tongketluong::with('user');

        $startSubMonth  =   Carbon::now()->startOfMonth();
        $endSubMonth    =   Carbon::now()->endOfMonth();
        $data1 = User::with('group','branch')->get();
            foreach ($data1 as $k => $item) {
                if(Tongketluong::where('month','=',$startSubMonth)->where('user_id',$data1[$k]->id)->count() == 0) {
                    $data2 = Group::where('id', $data1[$k]->group_id)->first()->congchuan;

                    $thuong = Thuongphat::where('user_id', $data1[$k]->id)->where('issued_at', '>=', $startSubMonth)->where('issued_at', '<=', $endSubMonth)->where('type', 2)->get();
                    $tongthuong = 0;
                    if (!empty($thuong)) {
                        foreach ($thuong as $k1 => $item1) {
                            $tongthuong = $tongthuong + $thuong[$k1]->amount;
                        }
                    }
                    $tongphat = 0;
                    $phat = Thuongphat::where('user_id', $data1[$k]->id)->where('issued_at', '>=', $startSubMonth)->where('issued_at', '<=', $endSubMonth)->where('type', 1)->get();
                    if (!empty($phat)) {
                        foreach ($phat as $k2 => $item2) {
                            $tongphat = $tongphat + $phat[$k2]->amount;
                        }
                    }

                    $cong = Chamcong::where('user_id', $data1[$k]->id)->where('ngay', '>=', $startSubMonth)->where('ngay', '<=', $endSubMonth)->get();
                    $tongcong = 0;
                    if (!empty($cong)) {
                        foreach ($cong as $k3 => $item3) {
                            $tongcong = $tongcong + $cong[$k3]->cong;
                        }
                    }

                    $data = new Tongketluong();
                    $data->month = $startSubMonth;
                    $data->user_id = $data1[$k]->id;
                    $data->luongcung = $data1[$k]->luongcung;
                    $data->congthucte = $tongcong;
                    $data->congchuan = $data2;
                    $data->thuong = $tongthuong;
                    $data->phat = $tongphat;
                    $data->tongluongnhan = $data1[$k]->luongcung * $tongcong / $data2 + $tongthuong - $tongphat;
                    $data->created_at = Carbon::now();
                    $data->save();
            }
        }

        return v('tongketluong.index', compact('data'));
    }

    public function dataList() {
        if (!function_exists('currency_format')) {
            function currency_format($number, $suffix = 'đ') {
                if (!empty($number)) {
                    return number_format($number, 0, ',', '.') . "{$suffix}";
                }
            }
        }
        $data   =   Tongketluong::with('user');
        if(auth()->user()->group_id >= 4){
            $data   =   $data->where('user_id', auth()->user()->id);
        } else {
            if(auth()->user()->group_id == 3){
                $data   =   $data->whereHas('user', function($q){
                    $q->where('branch_id', auth()->user()->branch_id);
                });
            }
            if(!empty($name = request('name'))){
                $data   =   $data->whereHas('user', function($q) use ($name){
                    $q->where('name', 'LIKE', '%'.$name.'%');
                });
            }
            if(!empty($uid = request('user_id'))){
                $data   =   $data->whereHas('user', function($q) use ($uid){
                    $q->where('uid', $uid);
                });
            }

        }
        $result = Datatables::of($data)
            ->addColumn('user', function(Tongketluong $c){
                return ($c->user?$c->user->uid.'-'.$c->user->name:$c->user_id);
            })->editColumn('month', function(Tongketluong $c){
                return Carbon::parse($c->month)->format('m/Y');
            })->editColumn('luongcung', function(Tongketluong $c) {
                return currency_format($c->luongcung);
            })->editColumn('thuong', function(Tongketluong $c) {
                return currency_format($c->thuong);
            })->editColumn('phat', function(Tongketluong $c) {
                return currency_format($c->phat);
            })->editColumn('tongluongnhan', function(Tongketluong $c) {
                return currency_format($c->tongluongnhan);
            })->addColumn('manage', function(Tongketluong $user) {
                return a('tong-ket-luong/tinhlailuong', 'id='.$user->id,'Tính lại', ['class'=>'btn btn-xs btn-default']);
            })->rawColumns(['manage']);

        return $result->make(true);
    }

    public function tinhlaitheothang(){
        $fromDate = request('fromDate1');
        log::info($fromDate);
        $startSubMonth  =   Carbon::createFromFormat('d/m/Y',$fromDate)->startOfMonth();
        $endSubMonth    =   Carbon::createFromFormat('d/m/Y',$fromDate)->endOfMonth();
        $data   =   Tongketluong::with('user')->where('month','=',$startSubMonth)->get();
        foreach ($data as $k0 => $item0){
            $data[$k0]->delete();
        }

        $data1 = User::with('group','branch')->get();
        foreach ($data1 as $k => $item) {
            if(Tongketluong::where('month','=',$startSubMonth)->where('user_id',$data1[$k]->id)->count() == 0) {
                $data2 = Group::where('id', $data1[$k]->group_id)->first()->congchuan;

                $thuong = Thuongphat::where('user_id', $data1[$k]->id)->where('issued_at', '>=', $startSubMonth)->where('issued_at', '<=', $endSubMonth)->where('type', 2)->get();
                $tongthuong = 0;
                if (!empty($thuong)) {
                    foreach ($thuong as $k1 => $item1) {
                        $tongthuong = $tongthuong + $thuong[$k1]->amount;
                    }
                }
                $tongphat = 0;
                $phat = Thuongphat::where('user_id', $data1[$k]->id)->where('issued_at', '>=', $startSubMonth)->where('issued_at', '<=', $endSubMonth)->where('type', 1)->get();
                if (!empty($phat)) {
                    foreach ($phat as $k2 => $item2) {
                        $tongphat = $tongphat + $phat[$k2]->amount;
                    }
                }

                $cong = Chamcong::where('user_id', $data1[$k]->id)->where('ngay', '>=', $startSubMonth)->where('ngay', '<=', $endSubMonth)->get();
                $tongcong = 0;
                if (!empty($cong)) {
                    foreach ($cong as $k3 => $item3) {
                        $tongcong = $tongcong + $cong[$k3]->cong;
                    }
                }

                $data = new Tongketluong();
                $data->month = $startSubMonth;
                $data->user_id = $data1[$k]->id;
                $data->luongcung = $data1[$k]->luongcung;
                $data->congthucte = $tongcong;
                $data->congchuan = $data2;
                $data->thuong = $tongthuong;
                $data->phat = $tongphat;
                $data->tongluongnhan = $data1[$k]->luongcung * $tongcong / $data2 + $tongthuong - $tongphat;
                $data->created_at = Carbon::now();
                $data->save();
            }
        }

        return redirect()->back();
    }

    public function tinhlailuong(){
        $auto   =   Tongketluong::find(request('id'));
        $startSubMonth  =   Carbon::parse($auto->month)->startOfMonth();
        $endSubMonth    =   Carbon::parse($auto->month)->endOfMonth();
            log::info($startSubMonth);
        log::info($endSubMonth);
            $cong = Chamcong::where('user_id', $auto->user_id)->where('ngay', '>=', $startSubMonth)->where('ngay', '<=', $endSubMonth)->get();

            $tongcong = 0;
            if (!empty($cong)) {
                foreach ($cong as $k3 => $item3) {
                    $tongcong = $tongcong + $cong[$k3]->cong;
                }
            }
        log::info($tongcong);

            $thuong = Thuongphat::where('user_id', $auto->user_id)->where('issued_at', '>=', $startSubMonth)->where('issued_at', '<=', $endSubMonth)->where('type', 2)->get();
            $tongthuong = 0;
            if (!empty($thuong)) {
                foreach ($thuong as $k1 => $itemm) {
                    $tongthuong = $tongthuong + $thuong[$k1]->amount;
                }
            }
            $tongphat = 0;
            $phat = Thuongphat::where('user_id', $auto->user_id)->where('issued_at', '>=', $startSubMonth)->where('issued_at', '<=', $endSubMonth)->where('type', 1)->get();
            if (!empty($phat)) {
                foreach ($phat as $k2 => $item2) {
                    $tongphat = $tongphat + $phat[$k2]->amount;
                }
            }
            $group = User::where('id',$auto->user_id)->first()->group_id;
            $data2 = Group::where('id', $group)->first()->congchuan;
            $auto->luongcung = User::where('id', '=', $auto->user_id)->first()->luongcung;
            $auto->congthucte = $tongcong;
            $auto->congchuan = $data2;
            $auto->thuong = $tongthuong;
            $auto->phat = $tongphat;
            $auto->tongluongnhan = User::where('id', '=', $auto->user_id)->first()->luongcung * $tongcong / $data2 + $tongthuong - $tongphat;
            $auto->updated_at = Carbon::now();
            $auto->save();
        return redirect()->back();
        }
}
