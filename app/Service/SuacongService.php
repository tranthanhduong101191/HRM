<?php
/**
 * Created by PhpStorm.
 * User: tuandv
 * Date: 11/4/2020
 * Time: 12:59 AM
 */

namespace App\Service;


use App\Chamcong;
use App\Suacong;

class SuacongService
{
    public function suacong(Chamcong $chamcong, $congmoi, $reason)
    {
        $suacong    =   new Suacong();
        $suacong->user_id   =   auth()->user()->id;
        $suacong->chamcong_id   =   $chamcong->id;
        $suacong->congcu    =   $chamcong->cong;
        $suacong->congmoi   =   $congmoi;
        $suacong->reason    =   $reason;
        $suacong->save();

        $chamcong->cong =   $congmoi;
        $chamcong->congnguoicham    =   $congmoi;
        $chamcong->nguoicham_id =   auth()->user()->id;
        $chamcong->save();

        return [
            'status'    =>  0,
            'message'   =>  'Sửa công thành công!',
            'data'  =>  $chamcong,
            'alert' =>  'success'
        ];
    }

    public function getByChamcongId($id)
    {
        $suacong    =   Suacong::where('chamcong_id', $id)->get();
        return [
            'status'    =>  0,
            'message'   =>  'Lấy danh sách sửa công thành công!',
            'alert' =>  'success',
            'data'  =>  $suacong
        ];
    }
}
