<?php

namespace App\Http\Controllers;

use App\Giaitrinh;
use App\Service\GiaitrinhService;
use App\Service\SuacongService;
use Carbon\Carbon;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use \DataTables;
class GiaitrinhController extends Controller
{
    private $service;
    private $suacongService;
    public function __construct()
    {
        $this->service  =   new GiaitrinhService();
        $this->suacongService  =   new SuacongService();
    }
    public function index()
    {
        return v('giaitrinh.index');
    }

    public function data()
    {
        $data   =   Giaitrinh::with('user');
        $data   =   $data->orderBy('id', 'DESC');
        if(!empty($userid = request('user_id')))
            $data   =   $data->where('user_id', $userid);
        if(!empty($fromDate =   request('fromDate')))
            $data   =   $data->where('issued_at', '>=', Carbon::createFromFormat('d/m/Y',$fromDate)->startOfDay());
        if(!empty($toDate =   request('toDate')))
            $data   =   $data->where('issued_at', '<=', Carbon::createFromFormat('d/m/Y',$toDate)->endOfDay());
        if(!empty($status = request('status')))
            $data   =   $data->where('status', $status);

        if(auth()->user()->group_id == 3){
            $data    =   $data->whereHas('user',function($q){
                $q->where('branch_id', auth()->user()->branch_id);
            });
        }
        if(auth()->user()->group_id>=4){
            $data   =   $data->where('user_id', auth()->user()->id);
        }

        $result =   DataTables::of($data)
            ->addColumn('user', function(Giaitrinh $c){
                return ($c->user?$c->user->uid.'-'.$c->user->name:$c->user_id);
            })
            ->addColumn('chamcong', function(Giaitrinh $c){
                return ($c->chamcong?"Ngày: ".(Carbon::parse($c->chamcong->ngay)->format('d/m/Y')).". Lỗi: ".(!empty($c->chamcong->loi)?implode(', ', json_decode($c->chamcong->loi)):''):$c->chamcong_id);
            })
            ->addColumn('hinhanh', function(Giaitrinh $c){
                $hinhanh = Storage::disk('uploads')->files('giaitrinh/'.$c->id);
                $result =   '';
                foreach($hinhanh as $h){
                    $result.=   '<img src="/uploads/'.$h.'" style="width: 100px" />';
                }
                return $result;
            })
            ->editColumn('status', function(Giaitrinh $c){
                return $c->status ==1?trans('Đã Duyệt'):($c->status ==0?trans('Chưa Duyệt'):trans('Đã Hủy'));
            })
            ->addColumn('manage', function(Giaitrinh $c){
                return a('giai-trinh/approve', 'id='.$c->id,'Xem giải trình',[
                        'class'=>'btn btn-info xemGiaitrinh btn-xs',
                        'data-id'=> $c->chamcong_id, 'data-phanhoi'=> $c->phanhoi],'#a').'
                         '.a('giai-trinh/del', 'id='.$c->id,trans('g.delete'), ['class'=>'btn btn-xs btn-danger'],'#',"return bootbox.confirm('".trans('system.delete_confirm')."', function(result){if(result==true){window.location.replace('".asset('giai-trinh/del?id='.$c->id)."')}})")
                ;
            })->rawColumns(['manage', 'hinhanh']);
        return $result->make(true);
    }

    public function store()
    {
        $input  =   \request()->only(['chamcong_id', 'content', 'files']);
        $store = $this->service->store(array_merge($input, ['user_id'=>auth()->user()->id]));
        set_notice($store['message'], $store['alert']);
        return redirect()->back();
    }

    public function ajax()
    {
        return $this->service->ajax(request('id'));
    }

    public function approve()
    {
        $giaitrinh  =   Giaitrinh::with('chamcong')->find(request('id'));
        $duyet = $this->service->approve($giaitrinh, request('status'),request('phanhoi'));
        log::info(request('status'));
        log::info(request('phanhoi'));
        log::info(request('cong'));
        if(request('status') == 1)
            $this->suacongService->suacong($giaitrinh->chamcong()->first(), request('cong'), 'Duyệt giải trình: '.$giaitrinh->reason);
        set_notice('Duyệt giải trình thành công!', 'success');
        return $duyet;
    }

    public function delete(){
        $giaitrinh  =   Giaitrinh::find(request('id'));
        $giaitrinh->delete();
        set_notice('Xóa giải trình thành công!', 'success');
        return redirect()->back();
    }
}
