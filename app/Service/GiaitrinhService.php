<?php
/**
 * Created by PhpStorm.
 * User: tuandv
 * Date: 11/3/2020
 * Time: 2:52 PM
 */

namespace App\Service;


use App\Chamcong;
use App\Giaitrinh;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use League\Flysystem\Plugin\ListFiles;

class GiaitrinhService
{
    public function store($input)
    {
        $data   =   new Giaitrinh();
        $data->user_id  =   $input['user_id'];
        $data->chamcong_id  =   $input['chamcong_id'];
        $data->content  =   $input['content'];
        $data->save();
        if(!empty($files = $input['files'])){
            foreach($files as $file){
                $file->move(public_path('uploads/giaitrinh/'.$data->id), $file->getClientOriginalName());
            }
        }
        return [
            'status'    =>  0,
            'message'   =>  'Thêm giải trình thành công!',
            'alert' =>  'success'
        ];
    }

    public function ajax($id)
    {
        $data  =   Giaitrinh::with('chamcong')->where('chamcong_id', $id)->orderBy('id','desc')->get();
        foreach($data as $k=>$item){
            $images =   Storage::disk('uploads')->files('giaitrinh/'.$item->id);
            $data[$k]->images   =   $images;
            $data[$k]->cong =   $item->chamcong?$item->chamcong->cong:0;
        }
        return [
            'status'    =>  0,
            'message'   =>  'Lấy danh sách giải trình thành công!',
            'alert' =>  'success',
            'data'  =>  $data
        ];
    }

    public function approve(Giaitrinh $giaitrinh, $status, $phanhoi)
    {
        $giaitrinh->phanhoi  =   $phanhoi;
        $giaitrinh->status  =   $status;
        $giaitrinh->approved_at =   Carbon::now();
        $giaitrinh->approved_user_id    =   auth()->user()->id;
        $giaitrinh->save();
        return [
            'status'    =>  0,
            'message'   =>  'Duyệt giải trình thành công!',
            'alert' =>  'success',
            'data'  =>  $giaitrinh
        ];
    }
}
