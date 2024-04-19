<?php
/**
 * Created by PhpStorm.
 * User: tuandv
 * Date: 10/29/2020
 * Time: 11:18 AM
 */

namespace App\Service;


use App\Checkinout;

class CheckinoutService
{
    public function getPaged($userid=null, $page=1, $perpage = 100)
    {
        $data   =   new Checkinout();
        if(!empty($userid))
            $data   =   $data->where('MaChamCong', $userid);
        if(!empty($page))
            $data   =   $data->skip($page*$perpage);
        $data   =   $data->get();
    }
}
