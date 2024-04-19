<?php

namespace App\Http\Controllers;

use App\Chamcong;
use App\Exports\ChamcongExport;
use App\Imports\LichImport;
use App\Saplich;
use App\SapLichSetting;
use App\SapLichThang;
use App\Service\SuacongService;
use App\Suacong;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SapLichController extends Controller
{
    public function saplich()
    {
        $data = Saplich::where('user_id',\request('id'))->get();
        $id = \request('id');

        return v('users.saplich',compact('data','id'));
    }

    public function save(Request $request)
    {
        $ca = $request->cadaluu;
        $vao1 = $request->vao1daluu;
        $ra1 = $request->ra1daluu;
        $vao2 = $request->vao2daluu;
        $ra2 = $request->ra2daluu;

        $data = Saplich::where('user_id',$request->id)->get();
        foreach ($data as $item)
        {
            if(($item->ca != $ca[$item->id]) || ($item->vao1 != $vao1[$item->id]) || ($item->ra1 != $ra1[$item->id]) || ($item->vao2 != $vao2[$item->id]) || ($item->ra2 != $ra2[$item->id]))
            {
                $item->ca = strtoupper($ca[$item->id]);
                $item->vao1 = $vao1[$item->id];
                $item->ra1 = $ra1[$item->id];
                $item->vao2 = $vao2[$item->id];
                $item->ra2 = $ra2[$item->id];
                $item->save();
            }
        }


        $index = 0;
        $ca = $request->ca;
        if (!empty($ca) && $index < count($ca))
            $index = count($ca);
        $vao1 = $request->vao1;
        if (!empty($vao1) && $index < count($vao1))
            $index = count($vao1);
        $ra1 = $request->ra1;
        if (!empty($ra1) && $index < count($ra1))
            $index = count($ra1);
        $vao2 = $request->vao2;
        if (!empty($vao2) && $index < count($vao2))
            $index = count($vao2);
        $ra2 = $request->ra2;
        if (!empty($ra2) && $index < count($ra2))
            $index = count($ra2);
        for ($k = 0; $k < $index; $k++) {
            if(!empty($ca[$k]) || !empty($vao1[$k]) || !empty($ra1[$k]) || !empty($vao2[$k]) || !empty($ra2[$k]) )
            {
                $saplich = new Saplich();
                if (!empty($ca[$k]))
                    $saplich->ca = strtoupper($ca[$k]);
                else
                    $saplich->ca = '';

                if (!empty($vao1[$k]))
                    $saplich->vao1 = $vao1[$k];
                else
                    $saplich->vao1 = '';

                if (!empty($ra1[$k]))
                    $saplich->ra1 = $ra1[$k];
                else
                    $saplich->ra1 = '';

                if (!empty($vao2[$k]))
                    $saplich->vao2 = $vao2[$k];
                else
                    $saplich->vao2 = '';

                if (!empty($ra2[$k]))
                    $saplich->ra2 = $ra2[$k];
                else
                    $saplich->ra2 = '';
                $saplich->user_id = $request->id;
                $saplich->save();
            }
        }
        set_notice('Lưu cài đặt thành công!', 'success');
        return redirect()->back();
    }

    public function saplichSetting()
    {
        $data = SapLichSetting::all();

        return v('users.saplichsetting',compact('data'));
    }

    public function saveSetting(Request $request)
    {
        $ca = $request->cadaluu;
        $vao1 = $request->vao1daluu;
        $ra1 = $request->ra1daluu;
        $vao2 = $request->vao2daluu;
        $ra2 = $request->ra2daluu;
        $parttime   =   $request->parttimedaluu;
        $data = SapLichSetting::all();
        foreach ($data as $item)
        {
            $item->ca = !empty($ca[$item->id])?strtoupper($ca[$item->id]):'';
            $item->vao1 = !empty($vao1[$item->id])?$vao1[$item->id]:'';
            $item->ra1 = !empty($ra1[$item->id])?$ra1[$item->id]:'';
            $item->vao2 = !empty($vao2[$item->id])?$vao2[$item->id]:'';
            $item->ra2 = !empty($ra2[$item->id])?$ra2[$item->id]:'';
            $item->parttime =   !empty($parttime[$item->id])?1:0;
            $item->save();
        }

        $index = 0;
        $ca = $request->ca;
        if (!empty($ca) && $index < count($ca))
            $index = count($ca);
        $vao1 = $request->vao1;
        if (!empty($vao1) && $index < count($vao1))
            $index = count($vao1);
        $ra1 = $request->ra1;
        if (!empty($ra1) && $index < count($ra1))
            $index = count($ra1);
        $vao2 = $request->vao2;
        if (!empty($vao2) && $index < count($vao2))
            $index = count($vao2);
        $ra2 = $request->ra2;
        if (!empty($ra2) && $index < count($ra2))
            $index = count($ra2);
        $parttimenew = $request->parttime;
        if (!empty($parttimenew) && $index < count($parttimenew))
            $index = count($parttimenew);
        for ($k = 0; $k < $index; $k++) {
            if(!empty($ca[$k]) || !empty($vao1[$k]) || !empty($ra1[$k]) || !empty($vao2[$k]) || !empty($ra2[$k]) )
            {
                $saplichSetting = new SapLichSetting();
                if (!empty($ca[$k]))
                    $saplichSetting->ca = strtoupper($ca[$k]);
                else
                    $saplichSetting->ca = '';

                if (!empty($vao1[$k]))
                    $saplichSetting->vao1 = $vao1[$k];
                else
                    $saplichSetting->vao1 = '';

                if (!empty($ra1[$k]))
                    $saplichSetting->ra1 = $ra1[$k];
                else
                    $saplichSetting->ra1 = '';

                if (!empty($vao2[$k]))
                    $saplichSetting->vao2 = $vao2[$k];
                else
                    $saplichSetting->vao2 = '';

                if (!empty($ra2[$k]))
                    $saplichSetting->ra2 = $ra2[$k];
                else
                    $saplichSetting->ra2 = '';

                $saplichSetting->parttime   =   !empty($parttimenew[$k])?1:0;
                $saplichSetting->save();
            }
        }

        set_notice('Lưu cài đặt thành công!', 'success');
        return redirect()->back();
    }

    public function saplichthang($thang, $nam)
    {
        if(empty($thang))
            $thang = Carbon::now()->format('m');
        if(empty($nam))
            $nam = Carbon::now()->format('Y');
        else
            $nam = \request('nam');

        $data = User::query();
        if(!empty(\request('uid')))
            $data = $data->where('uid',\request('uid'));

        if(!empty(\request('branch_id')))
            $data = $data->where('branch_id',\request('branch_id'));

        $data = $data->get();
        foreach($data as $k=>$user){
            $lich   =   Chamcong::where('user_id', $user->id)->where('ngay', '>=', Carbon::parse($nam.'-'.$thang.'-01')->startOfDay())->where('ngay', '<=', Carbon::parse($nam.'-'.$thang.'-01')->endOfMonth()->endOfDay())->get();
            $data_lich  =   [];
            foreach($lich as $l){
                $data_lich[Carbon::parse($l->ngay)->format('j')]    =   $l;
            }
            $data[$k]->lich = $data_lich;
        }

        $dayofweek = Carbon::createFromFormat('d/m/Y','01/'.$thang.'/'.$nam)->dayOfWeek;
        $days = Carbon::createFromFormat('d/m/Y','01/'.$thang.'/'.$nam)->daysInMonth;
        $dayofweek = Carbon::createFromFormat('d/m/Y','01/'.$thang.'/'.$nam)->dayOfWeek;
        $days = Carbon::createFromFormat('d/m/Y','01/'.$thang.'/'.$nam)->daysInMonth;
        return v('users.saplichthang',compact('data','days','dayofweek', 'thang','nam'));
    }

    public function saveshift()
    {
        $shift_setting = SapLichSetting::all();
        $ca = strtoupper(\request('ca'));
        $id = \request('id');
        $day = \request('day');
        $month = \request('month');
        $year = \request('year');
        $shift_user = Saplich::where('user_id',$id)->get();
        $chamcong = Chamcong::where('ngay',$year.'-'.$month.'-'.$day)->where('user_id',$id)->first();
        if(!empty($chamcong))
        {
            $chamcong->ca_name = $ca;
            $chamcong->save();
        }
        else
        {
            $chamcong = new Chamcong();
            $chamcong->ngay = $year.'-'.$month.'-'.$day;
            $chamcong->ca_name = $ca;
            $chamcong->user_id = $id;
            $chamcong->save();
        }
        foreach ($shift_user as $item)
        {
            if($item->ca == $ca)
            {
                $shift = SapLichThang::where('user_id',$id)->where('thang',$month)->first();

                if(!empty($shift))
                {
                    $general_shift = (array)json_decode($shift->general_shift,true);
                    $shift->$day = $item->ca;
                    $general_shift[$day] = 0;
                    $shift->general_shift = json_encode($general_shift);
                    $shift->save();
                }
                else
                {
                    $shift = new SapLichThang();
                    $general_shift = [];
                    $shift->$day = $item->ca;
                    $shift->user_id = $id;
                    $shift->thang = $month;
                    $shift->year = $year;
                    $general_shift[$day] = 0;
                    $shift->general_shift = json_encode($general_shift);
                    $shift->save();
                }
                return response()->json([
                    'success' => true,
                    'message' => 'Thành công!',
                    'data' => null
                ]);
            }
        }

        foreach ($shift_setting as $item)
        {
            if($item->ca == $ca)
            {
                $shift = SapLichThang::where('user_id',$id)->where('thang',$month)->first();

                if(!empty($shift))
                {
                    $general_shift = (array)json_decode($shift->general_shift,true);
                    $shift->$day = $item->ca;
                    $general_shift[$day] = 1;
                    $shift->general_shift = json_encode($general_shift);
                    $shift->save();

                }
                else
                {
                    $shift = new SapLichThang();
                    $general_shift = [];
                    $shift->$day = $item->ca;
                    $shift->user_id = $id;
                    $shift->thang = $month;
                    $shift->nam = $year;
                    $general_shift[$day] = 1;
                    $shift->general_shift = json_encode($general_shift);
                    $shift->save();

                }
                return response()->json([
                    'success' => true,
                    'message' => 'Thành công!',
                    'data' => null
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Không thành công!',
            'data' => null
        ]);
    }
    public function xemlichthang($thang=null, $nam=null)
    {
        if(empty($thang))
            $thang = Carbon::now()->format('m');
        if(empty($nam))
            $nam = Carbon::now()->format('Y');

        $data = User::query();
        if(auth()->user()->group_id >= 4){
            $data   =   $data->where('uid', auth()->user()->uid);
        }

        if(!empty(\request('uid')))
            $data = $data->where('uid',\request('uid'));

        if(!empty(\request('branch_id')))
            $data = $data->where('branch_id',\request('branch_id'));

        $data = $data->get();
        $data_suacong   =   [];
        foreach($data as $k=>$user){
            $lich   =   Chamcong::where('user_id', $user->id)->where('ngay', '>=', Carbon::parse($nam.'-'.$thang.'-01')->startOfDay())->where('ngay', '<=', Carbon::parse($nam.'-'.$thang.'-01')->endOfMonth()->endOfDay())->get();

            $data_lich  =   [];
            foreach($lich as $l){
                $data_lich[Carbon::parse($l->ngay)->format('j')]    =   $l;
            }
            $luot_suacong   =   Suacong::whereIn('chamcong_id', $lich->pluck('id'))->get();
            foreach ($luot_suacong as $s){
                $data_suacong[$s->chamcong_id]  =   [
                    'congcu'    =>  $s->congcu,
                    'congmoi'   =>  $s->congmoi,
                    'nguoisua'  =>  $s->user_id,
                    'reason'    =>  $s->reason
                ];
            }
            $data[$k]->lich = $data_lich;
        }
        $dayofweek = Carbon::createFromFormat('d/m/Y','01/'.$thang.'/'.$nam)->dayOfWeek;
        $days = Carbon::createFromFormat('d/m/Y','01/'.$thang.'/'.$nam)->daysInMonth;
        if(!empty(request('excel', 0))){
            return Excel::download(new ChamcongExport(compact('days','dayofweek','data','thang','nam')), 'TonghopCong.'.$thang.'.'.$nam.'.xlsx');
        }
        return v('users.xemlichthang',compact('days','dayofweek','data','thang','nam','data_suacong'));
    }

    public function uplichExcel()
    {
        $array = Excel::toArray(new LichImport(), request()->file('file'));
        $thang  =   request('thang');
        $nam    =   \request('nam');
        $fullmonth  =   Carbon::createFromFormat('d/m/Y', '1/'.$thang.'/'.$nam);
        if($fullmonth->lt(Carbon::now()->startOfMonth())){
            set_notice('Không thể sắp lịch cho những tháng đã qua', 'error');
            return redirect()->back();
        }
        $data   =   $array[0];
        $overwrite  =   request('overwrite', 0);
        $notice =   '';
        foreach($data as $k=>$item){
            if($k>0 && !empty($item[0])){
                $mnv    =   $item[0];
                $user   =   User::where('uid',$mnv)->first();
                if(!empty($user)){
                    for($i=1; $i<$fullmonth->daysInMonth+1; $i++){
                        $day    =   Carbon::createFromFormat('d/m/Y', $i.'/'.$thang.'/'.$nam);
                        $ca =   $item[$i+1];
                        $tontai    =    Chamcong::where('ngay', $day->toDateString())->where('user_id', $mnv)->count();
                        if(empty($tontai) || $overwrite=='on'){
                            if(empty($tontai)){
                                $sapca  =   new Chamcong();
                                $sapca->ngay    =   $day->toDateString();
                                $sapca->user_id =   $user->id;
                            } else {
                                $sapca  =   Chamcong::where('ngay', $day->toDateString())->where('user_id', $mnv)->first();
                            }
                            $sapca->ca_name =   !empty($ca)?$ca:'';
                            $sapca->save();
                        } else {
                            $notice .= "Ngày $i/$thang/$nam của $mnv - ".$item[1]." đã có và không ghi đè. <br/>";
                        }
                    }
                }
            }
        }
        set_notice('Lưu lịch thành công!<br/>'.($notice!=''?$notice:''), 'success');
//        return response('a');
        return redirect()->back();
    }
}
