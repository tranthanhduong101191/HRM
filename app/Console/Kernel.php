<?php

namespace App\Console;

use App\Chamcong;
use App\Group;
use App\Nhanvien;
use App\SapLichSetting;
use App\Service\ChamcongService;
use App\Thuongphat;
use App\Tongketluong;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function() {
            $data   =   new Chamcong();
            $data   =   $data->with('user');

            $data   =   $data->has('user');
            $data   =   $data->where('ngay','<',Carbon::now()->toDateString());
            $data   =   $data->orderBy('ngay', 'DESC');
            $data   =   $data->paginate(2000);
            foreach($data as $k=>$item){
                if(is_null($item->congmaycham)){
                    if(SapLichSetting::where('ca', $item->ca_name)->count()>0){
                        $calc   =   new ChamcongService($item->user_id, $item->ngay);
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
        })->dailyAt('00:00')->when(function(){
            if(Carbon::now()->day < 32){
                return true;
            } else
                return false;
        });

        $schedule->call(function() {
            if(Carbon::now()->day <= 1){
                $startSubMonth  =   Carbon::now()->subMonth()->startOfMonth();
                $endSubMonth    =   Carbon::now()->subMonth()->endOfMonth();
            }else {
                $startSubMonth = Carbon::now()->startOfMonth();
                $endSubMonth = Carbon::now()->endOfMonth();
            }
            $auto = Tongketluong::where('month', '=', $startSubMonth)->get();
            foreach ($auto as $a => $item1) {
                $cong = Chamcong::where('user_id', $auto[$a]->user_id)->where('ngay', '>=', $startSubMonth)->where('ngay', '<=', $endSubMonth)->get();
                $tongcong = 0;
                if (!empty($cong)) {
                    foreach ($cong as $k3 => $item3) {
                        $tongcong = $tongcong + $cong[$k3]->cong;
                    }
                }

                $thuong = Thuongphat::where('user_id', $auto[$a]->user_id)->where('issued_at', '>=', $startSubMonth)->where('issued_at', '<=', $endSubMonth)->where('type', 2)->get();
                $tongthuong = 0;
                if (!empty($thuong)) {
                    foreach ($thuong as $k1 => $itemm) {
                        $tongthuong = $tongthuong + $thuong[$k1]->amount;
                    }
                }
                $tongphat = 0;
                $phat = Thuongphat::where('user_id', $auto[$a]->user_id)->where('issued_at', '>=', $startSubMonth)->where('issued_at', '<=', $endSubMonth)->where('type', 1)->get();
                if (!empty($phat)) {
                    foreach ($phat as $k2 => $item2) {
                        $tongphat = $tongphat + $phat[$k2]->amount;
                    }
                }
                $group = User::where('id',$auto[$a]->user_id)->first()->group_id;
                $data2 = Group::where('id', $group)->first()->congchuan;
                $auto[$a]->luongcung = User::where('id', '=', $auto[$a]->user_id)->first()->luongcung;
                $auto[$a]->congthucte = $tongcong;
                $auto[$a]->congchuan = $data2;
                $auto[$a]->thuong = $tongthuong;
                $auto[$a]->phat = $tongphat;
                $auto[$a]->tongluongnhan = User::where('id', '=', $auto[$a]->user_id)->first()->luongcung * $tongcong / $data2 + $tongthuong - $tongphat;
                $auto[$a]->updated_at = Carbon::now();
                $auto[$a]->save();
            }
        })->dailyAt('01:00')->when(function(){
            if(Carbon::now()->day < 32){
                return true;
            } else
                return false;
        });

        $schedule->call(function(){
            $data = Nhanvien::get();
            foreach($data as $data1){
                $user = User::withTrashed()->where('uid',$data1->MaChamCong)
                    ->doesntExist();

                if ($user){
                    $data2 = new User();
                    $data2->name = $data1->TenNhanVien;
                    $data2->password = Hash::make('123456');
                    $data2->uid = $data1->MaChamCong;
                    $data2->email = $data1->MaChamCong.'@tanque.vn';
                    $data2->birthday = $data1->NgaySinh;
                    $data2->group_id = '4';
                    $data2->branch_id = '1';
                    $data2->created_at   =   Carbon::now();
                    $data2->save();
                }
            }
        })->everyMinute()->when(function(){
            if(Carbon::now()->day < 32){
                return true;
            } else
                return false;
        });
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
