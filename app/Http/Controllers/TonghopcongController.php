<?php

namespace App\Http\Controllers;

use App\Chamcong;
use App\Giaitrinh;
use App\SapLichSetting;
use App\Service\ChamcongService;
use App\Service\GiaitrinhService;
use App\Service\SuacongService;
use App\Service\ThuongphatService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TonghopcongController extends Controller
{
    private $suacongService;

    public function __construct()
    {
        $this->suacongService   =   new SuacongService();
    }
    public function index()
    {
        $data   =   new Chamcong();
        $data   =   $data->with('user');

        $data   =   $data->has('user');

        if(auth()->user()->group_id >= 4){
            $data   =   $data->where('user_id', auth()->user()->id);
        } else {
            // if(auth()->user()->group_id == 3){
            //     $data   =   $data->whereHas('user', function($q){
            //         $q->where('branch_id', auth()->user()->branch_id);
            //     });
            // }
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
        if(!empty($fromDate = request('fromDate'))){
            $data   =   $data->where('ngay', '>=', Carbon::createFromFormat('d/m/Y',$fromDate)->startOfDay());
        }
        if(!empty($toDate = request('toDate'))){
            $data   =   $data->where('ngay', '<=', Carbon::createFromFormat('d/m/Y',$toDate)->endOfDay());
        }
        if(!empty(request('loi'))){
            $data   =   $data->whereNotNull('loi');
        }
        $data   =   $data->where('ngay','<',Carbon::now()->toDateString());
        $data   =   $data->orderBy('ngay', 'DESC');
        $data   =   $data->paginate(20);
//        echo '<pre>';
//        print_r($data);
//        foreach($data as $item){
//            echo $item->user_id;
//            print_r($item);
//        }
        foreach($data as $k=>$item){
            if(is_null($item->congmaycham)){
                if(SapLichSetting::where('ca', $item->ca_name)->count()>0){
                    $calc   =   new ChamcongService($item->user_id, $item->ngay);
                    if(auth()->user()->parttime==1){
                        $item->parttime == 1;
                    }
                    $congmaycham    =   $calc->tinhtoan()['cong'];
                    $loi    =   $calc->tinhtoan()['loi'];
                    $lichsucham =   $calc->chamcong();
                    if(!empty($calc->tinhtoan()['vao1'])){
                        $vao1 = $calc->tinhtoan()['vao1'];
                        $ra1 = $calc->tinhtoan()['ra1'];
                        $vao2 = $calc->tinhtoan()['vao2'];
                        $ra2 = $calc->tinhtoan()['ra2'];
                    }

                    $luotcham   =   [
                        'vao1'  =>  '',
                        'ra1'   =>  '',
                        'vao2'  =>  '',
                        'ra2'   =>  ''
                    ];
                    if(!empty($lichsucham)){
                        if(count($lichsucham) < count($calc->ca())){
                            foreach($lichsucham as $cham){
                                $nearest    =   $calc->findNearest($cham->GioCham, $calc->ca());
                                $luotcham[$nearest['tenca']]    =   Carbon::parse($cham->GioCham)->format('H:i:s');
                            }
                        } else {
                            $lscham =   [];
                            foreach($lichsucham as $kls=>$ls){
                                $lscham[$kls]   =   Carbon::parse($ls->GioCham)->format('H:i:s');
                            }
                            if(count($calc->ca()) == 2){
                                $luotcham   =   [
                                    'vao1'  =>  $lscham[$kls],
                                    'ra1'   =>  $lscham[0],
                                    'vao2'  =>  '',
                                    'ra2'   =>  ''
                                ];
                            } else {
                                $luotcham   =   [
                                    'vao1'  =>  $vao1,
                                    'ra1'   =>  $ra1,
                                    'vao2'  =>  $vao2,
                                    'ra2'   =>  $ra2
                                ];
                            }

                        }

                    }
                    if($congmaycham>1)
                        $congmaycham  =   1;

                        $parttime = SapLichSetting::where('ca', $item->ca_name)->first()->parttime;
                        if($parttime == 1){
                            $congmaycham = $congmaycham/2;
                        }
                        $item->congmaycham  =   $congmaycham;
                        $item->cong  =   $congmaycham;
                        $item->data_cham    =   json_encode($luotcham);
                        $item->loi  =   json_encode($loi);
                        $item->partime = $parttime;
                        $item->save();
                }

            }
        }

        return v('chamcong.index', compact('data'));
    }

    public function updateCong()
    {
        $input  =   request()->only(['chamcong_id', 'congmoi', 'reason']);
        if(!empty($chamcong = Chamcong::with('user')->find($input['chamcong_id']))){
            if(auth()->user()->branch_id == 1 || auth()->user()->branch_id == 2 || (auth()->user()->branch_id == 3 && auth()->user()->branch_id == $chamcong->user->branch_id)){
                $sua = $this->suacongService->suacong($chamcong, $input['congmoi'], $input['reason']);
                set_notice($sua['message'], $sua['alert']);
            }
            else {
                set_notice('Bạn không có quyền với nhân viên này!', 'danger');
            }
        } else {
            set_notice('Không tìm thấy ngày chấm công này hoặc bạn không có quyền với nhân viên này!', 'danger');
        }
        return redirect()->back();
    }

    public function chitietcong()
    {
        $id =   \request('id');
        $data   =   Chamcong::find($id);
        $giaitrinhService   =   new GiaitrinhService();
        $suacongService =   new SuacongService();
        $thuongphatService =   new ThuongphatService();
        if(!empty($data)){
            $giaitrinh  =   $giaitrinhService->ajax($id);
            $suacong    =   $suacongService->getByChamcongId($id);
            $thuongphat =   $thuongphatService->getByChamcongId($id);
            return response()->json([
                'status'    =>  0,
                'message'   =>  'Lấy dữ liệu thành công!',
                'alert' =>  'success',
                'data'  =>  [
                    'cong'  =>  $data,
                    'giaitrinh' =>  $giaitrinh,
                    'suacong'   =>  $suacong,
                    'thuongphat'    =>  $thuongphat
                ]
            ]);
        }
    }

    public function tinhlai()
    {
        $item   =   Chamcong::find(request('id'));
        $data123 = Chamcong::where('user_id',$item->user_id)->where('ngay',$item->ngay)->get();
        foreach($data123 as $k1=>$item1){
            if($data123[$k1]!=$data123[0]){
                $data123[$k1]->delete();
            }
        }
        if(SapLichSetting::where('ca', $item->ca_name)->count()>0){
            $calc   =   new ChamcongService($item->user_id, $item->ngay);
            if(auth()->user()->parttime==1){
                $item->parttime == 1;
            }
            $congmaycham    =   $calc->tinhtoan()['cong'];
            $loi    =   $calc->tinhtoan()['loi'];
            $lichsucham =   $calc->chamcong();
            if(!empty($calc->tinhtoan()['vao1'])){
            $vao1 = $calc->tinhtoan()['vao1'];
            $ra1 = $calc->tinhtoan()['ra1'];
            $vao2 = $calc->tinhtoan()['vao2'];
            $ra2 = $calc->tinhtoan()['ra2'];
            }
            $luotcham   =   [
                'vao1'  =>  '',
                'ra1'   =>  '',
                'vao2'  =>  '',
                'ra2'   =>  ''
            ];
            if(!empty($lichsucham)){
                if(count($lichsucham) < count($calc->ca())){
                    foreach($lichsucham as $cham){
                        $nearest    =   $calc->findNearest($cham->GioCham, $calc->ca());
                        $luotcham[$nearest['tenca']]    =   Carbon::parse($cham->GioCham)->format('H:i:s');
                    }
                } else {
                    $lscham =   [];
                    foreach($lichsucham as $kls=>$ls){
                        $lscham[$kls]   =   Carbon::parse($ls->GioCham)->format('H:i:s');
                    }
                    if(count($calc->ca()) == 2){
                        $luotcham   =   [
                            'vao1'  =>  $lscham[$kls],
                            'ra1'   =>  $lscham[0],
                            'vao2'  =>  '',
                            'ra2'   =>  ''
                        ];
                    } else {
                        $luotcham   =   [
                            'vao1'  =>  $vao1,
                            'ra1'   =>  $ra1,
                            'vao2'  =>  $vao2,
                            'ra2'   =>  $ra2
                        ];
                    }

                }

            }
            if($congmaycham>1)
                $congmaycham  =   1;

                $parttime = SapLichSetting::where('ca', $item->ca_name)->first()->parttime;
                if($parttime == 1){
                    $congmaycham = $congmaycham/2;
                }
                $item->congmaycham  =   $congmaycham;
                $item->cong  =   $congmaycham;
                $item->data_cham    =   json_encode($luotcham);
                $item->loi  =   json_encode($loi);
                $item->partime = $parttime;
                $item->save();
        }
    }

    public function tinhlaitheongay()
    {
        $data   =   new Chamcong();
        $data   =   $data->with('user');

        $data   =   $data->has('user');
        $fromDate = request('fromDate1');
        $data   =   $data->where('ngay', '=', Carbon::createFromFormat('d/m/Y',$fromDate)->startOfDay());
        if(!empty(request('loi'))){
            $data   =   $data->whereNotNull('loi');
        }
        $data   =   $data->orderBy('ngay', 'DESC');
        $data   =   $data->paginate(2000);

        foreach($data as $k=>$item){
            if(SapLichSetting::where('ca', $item->ca_name)->count()>0){
                $calc   =   new ChamcongService($item->user_id, $item->ngay);
                if(auth()->user()->parttime==1){
                    $item->parttime == 1;
                }
                $congmaycham    =   $calc->tinhtoan()['cong'];
                $loi    =   $calc->tinhtoan()['loi'];
                $lichsucham =   $calc->chamcong();
                if(!empty($calc->tinhtoan()['vao1'])){
                    $vao1 = $calc->tinhtoan()['vao1'];
                    $ra1 = $calc->tinhtoan()['ra1'];
                    $vao2 = $calc->tinhtoan()['vao2'];
                    $ra2 = $calc->tinhtoan()['ra2'];
                }
                $luotcham   =   [
                    'vao1'  =>  '',
                    'ra1'   =>  '',
                    'vao2'  =>  '',
                    'ra2'   =>  ''
                ];
                if(!empty($lichsucham)){
                    if(count($lichsucham) < count($calc->ca())){
                        foreach($lichsucham as $cham){
                            $nearest    =   $calc->findNearest($cham->GioCham, $calc->ca());
                            $luotcham[$nearest['tenca']]    =   Carbon::parse($cham->GioCham)->format('H:i:s');
                        }
                    } else {
                        $lscham =   [];
                        foreach($lichsucham as $kls=>$ls){
                            $lscham[$kls]   =   Carbon::parse($ls->GioCham)->format('H:i:s');
                        }
                        if(count($calc->ca()) == 2){
                            $luotcham   =   [
                                'vao1'  =>  $lscham[$kls],
                                'ra1'   =>  $lscham[0],
                                'vao2'  =>  '',
                                'ra2'   =>  ''
                            ];
                        } else {
                            $luotcham   =   [
                                'vao1'  =>  $vao1,
                                'ra1'   =>  $ra1,
                                'vao2'  =>  $vao2,
                                'ra2'   =>  $ra2
                            ];
                        }

                    }

                }
                if($congmaycham>1)
                    $congmaycham  =   1;

                    $parttime = SapLichSetting::where('ca', $item->ca_name)->first()->parttime;
                    if($parttime == 1){
                        $congmaycham = $congmaycham/2;
                    }
                    $item->congmaycham  =   $congmaycham;
                    $item->cong  =   $congmaycham;
                    $item->data_cham    =   json_encode($luotcham);
                    $item->loi  =   json_encode($loi);
                    $item->partime = $parttime;
                    $item->save();
            }
        }

        return redirect()->back();
    }
}
