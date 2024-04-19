<?php
/**
 * Created by PhpStorm.
 * User: tuandv
 * Date: 11/3/2020
 * Time: 10:22 AM
 */

namespace App\Service;


use App\Chamcong;
use App\Thuongphat;
use App\User;
use Carbon\Carbon;

class ThuongphatService
{
    public function store($input)
    {
        $user   =   User::where('uid', $input['uid'])->first();
//                echo '<pre>';
//        print_r($data);
//        exit();
        if(!empty($user)){
            $data   =   new Thuongphat();
            $data->user_id  =   $user->id;
            $data->issued_at  =   Carbon::createFromFormat('d/m/Y', $input['issued_at']);
            $data->type  =   $input['type'];
            $data->amount  =   $input['amount'];
            $data->reason  =   $input['reason'];
            $data->issue_user_id  =   auth()->user()->id;
            $data->save();
            return [
                'status'    =>  0,
                'message'   =>  'Thêm phiếu thưởng phạt thành công!',
                'alert' =>  'success'
            ];
        }
        return [
            'status'    =>  1,
            'message'   =>  'Mã nhân viên không tồn tại!',
            'alert' =>  'danger'
        ];
    }

    public function update($id, $data)
    {

    }

    public function delete($id)
    {
        $thuongphat =   Thuongphat::find($id);
        if(!empty($thuongphat)){
            $thuongphat->delete();
            return [
                'status'    =>  0,
                'message'   =>  'Xoá lịch sử thưởng phạt thành công!'
            ];
        } else
            return [
                'status'    =>  1,
                'message'   =>  'Bản ghi không tồn tại!'
            ];
    }
    public function getByChamcongId($id)
    {
        $chamcong   =   Chamcong::find($id);
        if(!empty($chamcong)){
            $suacong    =   Thuongphat::where('user_id', $chamcong->user_id)->where('issued_at', $chamcong->ngay)->get();
            return [
                'status'    =>  0,
                'message'   =>  'Lấy danh sách thưởng phạt thành công!',
                'alert' =>  'success',
                'data'  =>  $suacong
            ];
        } else {
            return [
                'status'    =>  0,
                'message'   =>  'Lấy danh sách thưởng phạt không thành công!',
                'alert' =>  'warning',
                'data'  =>  []
            ];
        }

    }
}
