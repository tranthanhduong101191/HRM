<?php
/**
 * Created by PhpStorm.
 * User: tuandv
 * Date: 10/31/2020
 * Time: 10:17 AM
 */

namespace App\Service;


use App\Chamcong;
use App\Checkinout;
use App\Maychamcong;
use App\Saplich;
use App\SapLichSetting;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ChamcongService
{
    protected $user_id;
    protected $date;
    private $lichchung;
    private $model;
    public $user;
    public function __construct($user_id, $date)
    {
        $this->user_id  =   $user_id;
        $this->date =   $date;
        $this->model   =   Chamcong::where('user_id', $user_id)->where('ngay', $this->date)->first();
        $this->lichchung    =   new SapLichSetting();
        $this->user =   User::find($user_id);
    }

    public function findNearest($time, $ca){
        $result =   $ca['vao1'];
        $tenca  =   'vao1';
        $time   =   Carbon::parse($time);
        foreach($ca as $k=>$c){
            if($time->copy()->diffInSeconds(Carbon::parse($time->copy()->toDateString().' '.$c.':00')) < $time->copy()->diffInSeconds(Carbon::parse($time->copy()->toDateString().' '.$result.':00'))){
                $result =   $ca[$k];
                $tenca  =   $k;
            }
        }
        return [
            'tenca' => $tenca,
            'gio' => $result
        ];
    }

    public function ca()
    {
        $ca =   $this->model->ca_name;
		if(empty($ca)){
			return $arr = [
                'vao1'   => '00:00',
                'ra1'   =>  '00:01',
				'vao2' => '00:00',
				'ra2' => '00:01'
            ];
		}
        $saplich_rieng  =   Saplich::where('user_id', $this->user_id)->where('ca', $ca)->first();
        $arr = null;
        if(!empty($saplich_rieng)){
            $arr = [
                'vao1'   => !empty($saplich_rieng->vao1)?$saplich_rieng->vao1:'08:00',
                'ra1'   =>  $saplich_rieng->ra1
            ];
            if(!empty($saplich_rieng->vao2))
                $arr['vao2']    =   $saplich_rieng->vao2;
            if(!empty($saplich_rieng->ra2))
                $arr['ra2']    =   $saplich_rieng->ra2;
        }
        else {
            $lich   =   $this->lichchung->where('ca', $ca)->first();
            if(!empty($lich)){
                $arr = [
                    'vao1'   => !empty($lich->vao1)?$lich->vao1:'08:00',
                    'ra1'   =>  $lich->ra1
                ];
                if(!empty($lich->vao2))
                    $arr['vao2']    =   $lich->vao2;
                if(!empty($lich->ra2))
                    $arr['ra2']    =   $lich->ra2;
            }
        }
        return $arr;
    }

    public function parseCaToHour()
    {
        $ca =   $this->ca();
        $result =   [];
        foreach($ca as $k=>$c){
            $result[$k] =   explode(':', $c);
        }
        return $result;
    }
    public function  chamcong()
    {
        $user    =   User::find($this->user_id);
        if(!empty($user)){
            $uid =$user->uid;
            $data   =   Checkinout::where('MaChamCong', $uid)->where('NgayCham', $this->date);
            if($data->count() > 0){
                return $data->orderBy('GioCham', 'DESC')->get();
            }
        }

        return [];
    }

    public function parseChamcong()
    {
        $cham   =   $this->chamcong();
        $kq =   [
            'vao1'  =>  '',
            'ra1'   =>  '',
            'vao2'  =>  '',
            'ra2'   =>  ''
        ];
        foreach($cham as $c){
            $nearest    =   $this->findNearest($c->GioCham, $this->ca);
            $kq[$nearest['tenca']]  =   $c->GioCham;
        }
        return $kq;
    }
    public function tinhtoan()
    {
        if(!empty($this->chamcong())){
            $ca =   $this->ca();
            $giolam1 =   Carbon::parse('2020-01-01 '.$ca['vao1'].':00')->diffInMinutes(Carbon::parse('2020-01-01 '.$ca['ra1'].':01'));
            $giolam2 =   count($this->ca())>2?Carbon::parse('2020-01-01 '.$ca['vao2'].':00')->diffInMinutes(Carbon::parse('2020-01-01 '.$ca['ra2'].':01')):(-0);
            $tonggiolam =   $giolam1+$giolam2;
            log::info($tonggiolam);
            $solancham  =   $this->chamcong()->count();
            if(count($ca) == 2 && $solancham > 2){
                $caParsed   =   $this->parseCaToHour();
                // $ngay=Carbon::parse($this->chamcong()[0]->NgayCham)->toDateString();
                // $ra1 =   Carbon::parse($ngay .$ca['ra1'].':00.000');
                // $vao12 =   Carbon::parse($ngay .$ca['vao1'].':00.000');

                // $vao1 = $this->chamcong()->where('GioCham','<',$ra1->addMinutes(90))->where('GioCham','>',$vao12)->first();
                // if($vao1 == null){
                //     $vao1 = $this->chamcong()->last();
                // }

                $cham   =   [
                    'vao1'  =>  Carbon::parse($this->chamcong()->last()->GioCham),
                    'ra1'  =>  Carbon::parse($this->chamcong()->first()->GioCham),
                ];
                $ca =   [
                    'vao1'  =>  Carbon::parse($cham['vao1'])->setTime($caParsed['vao1'][0], $caParsed['vao1'][1]),
                    'ra1'  =>  Carbon::parse($cham['ra1'])->setTime($caParsed['ra1'][0], $caParsed['ra1'][1]),
                ];
                if($cham['vao1'] >= $ca['ra1']){
                    $cong = 0;
					$giolamthucte =0;
					$loi = 'Cham Sai Ca';
                }else{
                    $loi[]= 'Chấm quá số lần quy định';
                    if($cham['vao1'] == $cham['ra1']){
                        $loi[]= 'Thiếu chấm ra vào ca 1';
                    }else {
                        if ($cham['vao1']->gt($ca['vao1'])) {
                            $loi[] = 'Chấm đến muộn ' . ($cham['vao1']->diffInMinutes($ca['vao1'])) . ' phút';
                        } else {
                            $cham['vao1'] = $ca['vao1'];
                        }
                        if ($cham['ra1']->lt($ca['ra1'])) {
                            $loi[] = 'Chấm về sớm ' . ($cham['ra1']->diffInMinutes($ca['ra1'])) . ' phút';
                        } else {
                            $cham['ra1'] = $ca['ra1'];
                        }
                    }

                    $giolamthucte   =   $cham['vao1']->diffInMinutes($cham['ra1']);
                    log::info($giolamthucte);
                    if($giolamthucte/$tonggiolam > 1)
                        $cong   =   1;
                    else
                        $cong   =   round($giolamthucte/$tonggiolam, 2);
                }

                // if(($cham['vao1']->diffInMinutes($ca['vao1']) >0) && ($cham['vao1']->diffInMinutes($ca['vao1'])<=30)){
                //     $cong = 0.9;
                // }elseif(($cham['vao1']->diffInMinutes($ca['vao1']) >30) && ($cham['vao1']->diffInMinutes($ca['vao1'])<=60)){
                //     $cong = 0.8;
                // }elseif($cham['vao1']->diffInMinutes($ca['vao1']) > 60){
                //     $cong = 0.5;
                // }
                return [
                    'cong'  =>  $cong,
                    'giolam' => $giolamthucte,
                    'loi'   =>  $loi
                ];
            }
            if($solancham == 1){
                return [
                    'cong'  =>  0,
                    'giolam' => 0,
                    'loi'   =>  [
                        'Chấm 1 lần'
                    ]
                ];
            }
            if($solancham   ==  2){
                if(count($ca) == 2){
                    $caParsed   =   $this->parseCaToHour();
                    // $ngay=Carbon::parse($this->chamcong()[0]->NgayCham)->toDateString();
                    // $ra1 =   Carbon::parse($ngay .$ca['ra1'].':00.000');
                    // $vao12 =   Carbon::parse($ngay .$ca['vao1'].':00.000');

                    // $vao1 = $this->chamcong()->where('GioCham','<',$ra1->addMinutes(90))->where('GioCham','>',$vao12)->first();
                    // if($vao1 == null){
                    //     $vao1 = $this->chamcong()->last();
                    // }
                    
                        $cham   =   [
                            'vao1'  =>  Carbon::parse($this->chamcong()->last()->GioCham),
                            'ra1'  =>  Carbon::parse($this->chamcong()->first()->GioCham),
                        ];
                        $ca =   [
                            'vao1'  =>  Carbon::parse($cham['vao1'])->setTime($caParsed['vao1'][0], $caParsed['vao1'][1]),
                            'ra1'  =>  Carbon::parse($cham['ra1'])->setTime($caParsed['ra1'][0], $caParsed['ra1'][1]),
                        ];

                        if($cham['vao1'] >= $ca['ra1'] || $cham['ra1'] <= $ca['vao1']){
                            $cong = 0;
                            $giolamthucte = 0;
                            $loi[]= 'Chấm sai ca';
                        }else{
                        $loi[]= '';
                        if($cham['vao1'] == $cham['ra1']){
                            $loi[]= 'Thiếu chấm ra vào ca 1';
                        }else {
                            if ($cham['vao1']->gt($ca['vao1'])) {
                                $loi[] = 'Chấm đến muộn ' . ($cham['vao1']->diffInMinutes($ca['vao1'])) . ' phút';
                            } else {
                                $cham['vao1'] = $ca['vao1'];
                            }
                            if ($cham['ra1']->lt($ca['ra1'])) {
                                $loi[] = 'Chấm về sớm ' . ($cham['ra1']->diffInMinutes($ca['ra1'])) . ' phút';
                            } else {
                                $cham['ra1'] = $ca['ra1'];
                            }
                        }

                        $giolamthucte   =   $cham['vao1']->diffInMinutes($cham['ra1']);
                        if($this->user->parttime == 5){
                            $cong   =   round($giolamthucte/60, 2);
                        } else {
                            if($giolamthucte/$tonggiolam > 1)
                                $cong   =   1;
                            else
                                $cong   =   round($giolamthucte/$tonggiolam, 2);
                        }
                    }
                    // if(($cham['vao1']->diffInMinutes($ca['vao1']) >0) && ($cham['vao1']->diffInMinutes($ca['vao1'])<=30)){
                    //     $cong = 0.9;
                    // }elseif(($cham['vao1']->diffInMinutes($ca['vao1']) >30) && ($cham['vao1']->diffInMinutes($ca['vao1'])<=60)){
                    //     $cong = 0.8;
                    // }elseif($cham['vao1']->diffInMinutes($ca['vao1']) > 60){
                    //     $cong = 0.5;
                    // }

                    return [
                        'cong'  =>  $cong,
                        'giolam' => $giolamthucte,
                        'loi'   =>  $loi
                    ];
                }
                else {
                    $giolamthucte   =   Carbon::parse($this->chamcong()[0]->GioCham)->diffInMinutes($this->chamcong()[1]->GioCham);
                    if($giolamthucte/$tonggiolam > 0.5)
                        $cong   =   0.5;
                    else
                        $cong   =   round($giolamthucte/$tonggiolam, 2);
                    return [
                        'cong'  =>  ($cong>1)?1:$cong,
                        'giolam' => $giolamthucte,
                        'loi'   =>  [
                            'Chấm 2 lần'
                        ]
                    ];
                }

            }
            if(count($this->ca()) >2){
//                if($solancham == 3){
//                    $lancham1   =   $this->findNearest($this->chamcong()[0]->GioCham, $this->ca());
//                    $lancham2   =   $this->findNearest($this->chamcong()[1]->GioCham, $this->ca());
//                    $lancham3   =   $this->findNearest($this->chamcong()[2]->GioCham, $this->ca());
//
//                    switch ($lancham1['tenca']){
//                        case 'ra2':
//                            if($lancham2['tenca'] == 'vao2'){
//                                $giolamthucte   =   Carbon::parse($this->chamcong()[0]->GioCham)->diffInMinutes(Carbon::parse($this->chamcong()[1]->GioCham));
//                            } elseif ($lancham3['tenca'] == 'vao2') {
//                                $giolamthucte   =   Carbon::parse($this->chamcong()[0]->GioCham)->diffInMinutes(Carbon::parse($this->chamcong()[2]->GioCham));
//                            } else
//                                $giolamthucte = -1;
//                            break;
//                        case 'vao2':
//                            $giolamthucte   =   Carbon::parse($this->chamcong()[1]->GioCham)->diffInMinutes(Carbon::parse($this->chamcong()[2]->GioCham));
//                            break;
//                        case 'ra1':
//                            if(Carbon::parse($this->chamcong()[1]->GioCham)->lt(Carbon::parse($this->chamcong()[2]->GioCham)))
//                                $timeToCalc =   Carbon::parse($this->chamcong()[1]->GioCham);
//                            else $timeToCalc =   Carbon::parse($this->chamcong()[2]->GioCham);
//                            $giolamthucte   =   Carbon::parse($this->chamcong()[0]->GioCham)->diffInMinutes($timeToCalc);
//                            break;
//                        case 'vao1':
//                            $giolamthucte = 0;
//                            break;
//                        default: $giolamthucte = -1;
//                    }
//                    if($giolamthucte == -1){
//                        return [
//                            'cong'  =>  0.5,
//                            'giolam' => (Carbon::parse($this->chamcong()[0]->GioCham)->diffInMinutes(Carbon::parse($this->chamcong()[1]->GioCham)) > Carbon::parse($this->chamcong()[1]->GioCham)->diffInMinutes(Carbon::parse($this->chamcong()[2]->GioCham)))?Carbon::parse($this->chamcong()[0]->GioCham)->diffInMinutes(Carbon::parse($this->chamcong()[1]->GioCham)): Carbon::parse($this->chamcong()[1]->GioCham)->diffInMinutes(Carbon::parse($this->chamcong()[2]->GioCham)),
//                            'loi'   =>  [
//                                'Chấm thiếu 1 lần'
//                            ]
//                        ];
//                    } else if($giolamthucte == 0) {
//                        return [
//                            'cong'  =>  0.5,
//                            'giolam' => (Carbon::parse($this->chamcong()[0]->GioCham)->diffInMinutes(Carbon::parse($this->chamcong()[1]->GioCham)) > Carbon::parse($this->chamcong()[1]->GioCham)->diffInMinutes(Carbon::parse($this->chamcong()[2]->GioCham)))?Carbon::parse($this->chamcong()[0]->GioCham)->diffInMinutes(Carbon::parse($this->chamcong()[1]->GioCham)): Carbon::parse($this->chamcong()[1]->GioCham)->diffInMinutes(Carbon::parse($this->chamcong()[2]->GioCham)),
//                            'loi'   =>  [
//                                'Quên chấm ra vào'
//                            ]
//                        ];
//                    } else {
//                        $cong_final = round($giolamthucte/$tonggiolam, 2);
//                        return [
//                            'cong'  =>  ($cong_final>1)?1:$cong_final,
//                            'giolam' => (Carbon::parse($this->chamcong()[0]->GioCham)->diffInMinutes(Carbon::parse($this->chamcong()[1]->GioCham)) > Carbon::parse($this->chamcong()[1]->GioCham)->diffInMinutes(Carbon::parse($this->chamcong()[2]->GioCham)))?Carbon::parse($this->chamcong()[0]->GioCham)->diffInMinutes(Carbon::parse($this->chamcong()[1]->GioCham)): Carbon::parse($this->chamcong()[1]->GioCham)->diffInMinutes(Carbon::parse($this->chamcong()[2]->GioCham)),
//                            'loi'   =>  [
//                                'Quên chấm ra vào'
//                            ]
//                        ];
//                    }
//                }
                if($solancham <= 4){
                    $ngay=Carbon::parse($this->chamcong()[0]->NgayCham)->toDateString();

                    $ra1 =   Carbon::parse($ngay .$ca['ra1'].':00.000');
                    $vao12 =   Carbon::parse($ngay .$ca['vao1'].':00.000');

                    $vao1 = $this->chamcong()->where('GioCham','<',$ra1->addMinutes(90))->where('GioCham','>',$vao12)->first();

                    if($vao1 == null){
                        $vao1 = $this->chamcong()->last();
                    }
                    $ra2 =   Carbon::parse($ngay .$ca['ra2'].':00.000');
                    $ra1a =   Carbon::parse($ngay .$ca['ra1'].':00.000');
                    $vao2 = $this->chamcong()->where('GioCham','<',$ra2->addHour(2))->where('GioCham','>',$ra1a->addMinutes(90))->last();
                    log::info($vao2);

                    if($vao2 == null){
                        $vao2 = $this->chamcong()->first();
                    }
                    $caParsed   =   $this->parseCaToHour();
                    $cham   =   [
                        'vao1'  =>  Carbon::parse($this->chamcong()->last()->GioCham),
                        'ra1'   =>  Carbon::parse($vao1->GioCham),
                        'vao2'  =>  Carbon::parse($vao2->GioCham),
                        'ra2'   =>  Carbon::parse($this->chamcong()->first()->GioCham),
                    ];
                    $luotcham1=$cham['vao1']->format('H:i:s');
                    $luotcham2=$cham['ra1']->format('H:i:s');
                    $luotcham3=$cham['vao2']->format('H:i:s');
                    $luotcham4=$cham['ra2']->format('H:i:s');
                    $ca =   [
                        'vao1'  =>  Carbon::parse($cham['vao1'])->setTime($caParsed['vao1'][0], $caParsed['vao1'][1]),
                        'ra1'  =>  Carbon::parse($cham['ra1'])->setTime($caParsed['ra1'][0], $caParsed['ra1'][1]),
                        'vao2'  =>  Carbon::parse($cham['vao2'])->setTime($caParsed['vao2'][0], $caParsed['vao2'][1]),
                        'ra2'  =>  Carbon::parse($cham['ra2'])->setTime($caParsed['ra2'][0], $caParsed['ra2'][1]),
                    ];
                    $loi[]= '';
                    $tong = 0;
                    if($cham['vao1'] == $cham['ra1']){
                        $loi[]= 'Thiếu chấm ra vào ca 1';
                        $luotcham2='';
                    }else {
                        if ($cham['vao1']->gt($ca['vao1'])) {
                            $loi[] = 'Chấm đến muộn ' . ($cham['vao1']->diffInMinutes($ca['vao1'])) . ' phút';
                            $tong = $tong + 1;
                        } else {
                            $cham['vao1'] = $ca['vao1'];
                        }
                        if ($cham['ra1']->lt($ca['ra1'])) {
                            $loi[] = 'Chấm nghỉ giữa giờ sớm ' . ($cham['ra1']->diffInMinutes($ca['ra1'])) . ' phút';
                            $tong = $tong + 1;
                        } else {
                            $cham['ra1'] = $ca['ra1'];
                        }
                    }
                    if($cham['vao2'] == $cham['ra2']){
                        $loi[]= 'Thiếu chấm ra vào ca2';
                        $luotcham3='';
                    }else {
                        if ($cham['vao2']->gt($ca['vao2'])) {
                            $loi[] = 'Chấm vào giữa giờ muộn ' . ($cham['vao2']->diffInMinutes($ca['vao2'])) . ' phút';
                            $tong = $tong + 1;
                        } else {
                            $cham['vao2'] = $ca['vao2'];
                        }
                        if ($cham['ra2']->lt($ca['ra2'])) {
                            $loi[] = 'Chấm về sớm ' . ($cham['ra2']->diffInMinutes($ca['ra2'])) . ' phút';
                            $tong = $tong + 1;
                        } else {
                            $cham['ra2'] = $ca['ra2'];
                        }
                    }

                    $giolam1    =   $cham['vao1']->diffInMinutes($cham['ra1']);
                    $giolam2    =   $cham['vao2']->diffInMinutes($cham['ra2']);
                    if(($giolam1+$giolam2) < $tonggiolam){
                        $loi[] = 'Thiếu '.($tonggiolam-($giolam1+$giolam2)-$tong).' phút!';
                    } else $loi = [];
                    $cong_final = round(($giolam1+$giolam2)/$tonggiolam,2);
                    return [
                        'cong'  =>  ($cong_final>1)?1:$cong_final,
                        'giolam' => $giolam1+$giolam2,
                        'loi'   =>  $loi,
                        'vao1' => $luotcham1,
                        'ra1' => $luotcham2,
                        'vao2' => $luotcham3,
                        'ra2' => $luotcham4,
                    ];
                }

                if($solancham > 4){
                    $ngay=Carbon::parse($this->chamcong()[0]->NgayCham)->toDateString();

                    $ra1 =   Carbon::parse($ngay .$ca['ra1'].':00.000');
                    $vao12 =   Carbon::parse($ngay .$ca['vao1'].':00.000');

                    $vao1 = $this->chamcong()->where('GioCham','<',$ra1->addMinutes(90))->where('GioCham','>',$vao12)->first();

                    if($vao1 == null){
                        $vao1 = $this->chamcong()->last();
                    }
                    $ra2 =   Carbon::parse($ngay .$ca['ra2'].':00.000');
                    $ra1a =   Carbon::parse($ngay .$ca['ra1'].':00.000');
                    $vao2 = $this->chamcong()->where('GioCham','<',$ra2->addHour(2))->where('GioCham','>',$ra1a->addMinutes(90))->last();

                    if($vao2 == null){
                        $vao2 = $this->chamcong()->first();
                    }
                    $caParsed   =   $this->parseCaToHour();
                    $cham   =   [
                        'vao1'  =>  Carbon::parse($this->chamcong()->last()->GioCham),
                        'ra1'   =>  Carbon::parse($vao1->GioCham),
                        'vao2'  =>  Carbon::parse($vao2->GioCham),
                        'ra2'   =>  Carbon::parse($this->chamcong()->first()->GioCham),
                    ];
                    $luotcham1=$cham['vao1']->format('H:i:s');
                    $luotcham2=$cham['ra1']->format('H:i:s');
                    $luotcham3=$cham['vao2']->format('H:i:s');
                    $luotcham4=$cham['ra2']->format('H:i:s');
                    $ca =   [
                        'vao1'  =>  Carbon::parse($cham['vao1'])->setTime($caParsed['vao1'][0], $caParsed['vao1'][1]),
                        'ra1'  =>  Carbon::parse($cham['ra1'])->setTime($caParsed['ra1'][0], $caParsed['ra1'][1]),
                        'vao2'  =>  Carbon::parse($cham['vao2'])->setTime($caParsed['vao2'][0], $caParsed['vao2'][1]),
                        'ra2'  =>  Carbon::parse($cham['ra2'])->setTime($caParsed['ra2'][0], $caParsed['ra2'][1]),
                    ];
                    $loi[] = 'Chấm quá số lần quy định' ;
                    $tong= 0;
                    if($cham['vao1'] == $cham['ra1']){
                        $loi[]= 'Thiếu chấm ra vào ca 1';
                        $luotcham2='';
                    }else {
                        if ($cham['vao1']->gt($ca['vao1'])) {
                            $loi[] = 'Chấm đến muộn ' . ($cham['vao1']->diffInMinutes($ca['vao1'])) . ' phút';
                            $tong = $tong + 1;
                        } else {
                            $cham['vao1'] = $ca['vao1'];
                        }
                        if ($cham['ra1']->lt($ca['ra1'])) {
                            $loi[] = 'Chấm nghỉ giữa giờ sớm ' . ($cham['ra1']->diffInMinutes($ca['ra1'])) . ' phút';
                            $tong = $tong + 1;
                        } else {
                            $cham['ra1'] = $ca['ra1'];
                        }
                    }
                    if($cham['vao2'] == $cham['ra2']){
                        $loi[]= 'Thiếu chấm ra vào ca2';
                        $luotcham3='';
                    }else {
                        if ($cham['vao2']->gt($ca['vao2'])) {
                            $loi[] = 'Chấm vào giữa giờ muộn ' . ($cham['vao2']->diffInMinutes($ca['vao2'])) . ' phút';
                            $tong = $tong + 1;
                        } else {
                            $cham['vao2'] = $ca['vao2'];
                        }
                        if ($cham['ra2']->lt($ca['ra2'])) {
                            $loi[] = 'Chấm về sớm ' . ($cham['ra2']->diffInMinutes($ca['ra2'])) . ' phút';
                            $tong = $tong + 1;
                        } else {
                            $cham['ra2'] = $ca['ra2'];
                        }
                    }
                    $giolam1    =   $cham['vao1']->diffInMinutes($cham['ra1']);
                    $giolam2    =   $cham['vao2']->diffInMinutes($cham['ra2']);
                    if(($giolam1+$giolam2) < $tonggiolam){
                        $loi[] = 'Tổng thiếu '.($tonggiolam-($giolam1+$giolam2)-$tong).' phút!';
                    }
                    $cong_final = round(($giolam1+$giolam2)/$tonggiolam,2);
                    return [
                        'cong'  =>  ($cong_final>1)?1:$cong_final,
                        'giolam' => $giolam1+$giolam2,
                        'loi'   =>  $loi,
                        'vao1' => $luotcham1,
                        'ra1' => $luotcham2,
                        'vao2' => $luotcham3,
                        'ra2' => $luotcham4,
                    ];
                }
            }
        } else {
            return [
                'cong'  =>  0,
                'giolam' => 0,
                'loi'   =>  [
                    'Không chấm công'
                ]
            ];
        }
    }

}
