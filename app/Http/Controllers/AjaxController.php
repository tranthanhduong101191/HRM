<?php
namespace App\Http\Controllers;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use Carbon\Carbon;
class AjaxController extends Controller {
    public function postInfofromcode() {
        $code   = substr(Input::get('code'), 0,10);
        $data   = Brand::where('code','=',$code)->get()->toArray();
        if(!empty($data)){
            return [
                'brand' =>  $data[0]['brand'],
                'category'  =>  $data[0]['category']
            ];
        }
        else
            return 'no data';
    }
    public function postNamefromuid() {
        $uid    =   Input::get('uid');
        if(!empty($uid) && empty(Input::get('allinfo'))){
            return infofromuid($uid);
        }
        elseif(!empty(Input::get('allinfo'))){
            $data   =   User::where('uid','=',$uid)->get()->toArray();
            return !empty($data)?$data[0]:0;
        }
    }
    public function getBrand() {
        $q  =   Input::get('q');
        $data   =   DB::table('brands')->where('name','LIKE',"%{$q}%");
        $data   =   $data->get();
        $result =   [];
        foreach($data as $item){
            $result[]   =   [
                'id'    =>  $item->name,
                'name'  =>  $item->name
            ];
        }
        return Response::json($result);
    }
    public function getCategory() {
        $q  =   Input::get('q');
        $data   =   DB::table('category')->where('name','LIKE',"%{$q}%");
        $data   =   $data->get();
        $result =   [];
        foreach($data as $item){
            $result[]   =   [
                'id'    =>  $item->name,
                'name'  =>  $item->name
            ];
        }
        return Response::json($result);
    }
    public function getLinhkien() {
        $data   =   new Kholinhkien();

        $query  =   Input::get('q');

        $data   =   $data->where('linhkien_id','=',NULL);
        $data   =   $data->where(function($q) use ($query){
            $q->where('product_name','LIKE', "%{$query}%")->orWhere('product_imei', 'LIKE', "%{$query}%");
        });
        $data   =   $data->get();
        $result =   [];
        foreach($data as $item){
            $result[] = [
                'id'    =>  $item->id,
                'name' => $item->product_name,
                'code' => $item->product_code,
                'importPrice' => $item->importPrice,
                'price' => $item->price,
                'level' =>  $item->level,
                'imei'  =>  $item->product_imei,
                'ttbh'  =>  $item->ttbh,
                'idNhanh'   =>  $item->idNhanh
            ];
        }
        return Response::json($result);
    }
    public function postLogin() {
        if (Auth::attempt(array('username' => Input::get('username'), 'password' => Input::get('password')))) {
            return Response::json(['status' => 'ok', 'message' => 'Đăng nhập thành công']);
        } else {
            return Response::json(['status' => 'false', 'message' => 'Kiểm tra lại tài khoản hoặc mật khẩu', 'result' => Auth::attempt(array('username' => Input::get('username'), 'password' => Input::get('password')))]);
        }
    }
    public function postInfofromcodeLk() {
        $code   =   Input::get('code');
        $data   =   new Kholinhkien();
        $data   =   $data->where('product_code','=',$code)->orderBy('id','DESC')->first();
        if(!empty($data)){
            return Response::json([
                'status'    =>  1,
                'data'  =>  [
                    'name'  =>  $data->product_name,
                    'importPrice'   =>  $data->importPrice,
                    'price' =>  $data->price,
                    'level' =>  $data->level
                ]
            ]);
        }else{
            return Response::json(['status'=>0]);
        }
    }
    public function postAddlk() {
        $input  =   Input::only('product_code','product_name','product_imei','importPrice','price','level','ttbh','idNhanh');
        Kholinhkien::insert($input);
        return Redirect::back()->with('message','Thêm linh kiện thành công!');
    }
    public function postInfoCode() {
        $code   =   Input::get('code');
        $data   = Kholinhkien::where('product_imei','=',$code)->orderBy('id','DESC')->first();

        if (!empty($data)) {
            return Response::json([
                'status' => 0,
                'data' => [
                    'code'  =>  $data->product_code,
                    'name' => $data->product_name,
                    'price' => $data->price,
                ]
            ]);
        } else {
            return Response::json([
                'status' => 1,
                'data' => [
                ]
            ]);
        }
    }
    public function postBh() {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            header("HTTP/1.1 200 OK");
            exit;
        }
        $data   =   new Baohanh();
        $data   =   $data->where('kho','<',201);
        if(!empty($imei =   Input::get('imei'))){
            $data   =   $data->where('product_imei','=',$imei);
        }
        if(!empty($idbh =   Input::get('idbh'))){
            $data   =   $data->where('id','=',$idbh);
        }
        if($data->count()==0){
            $data = new Baohanh();
            if (!empty($imei = Input::get('imei'))) {
                $data = $data->where('product_imei', '=', $imei);
            }
            if (!empty($idbh = Input::get('idbh'))) {
                $data = $data->where('id', '=', $idbh);
            }
        }
        $data   =   $data->orderBy('id','DESC')->first()->toArray();
        if($data['status']<100)
            $ht     =   'Siêu thị';
        elseif($data['status']<200)
            $ht     =   'Trung tâm bảo hành';
        else
            $ht     =   'Đã trả khách';
        $return =   [
            'product_name'  =>  $data['product_name'],
            'product_imei'  =>  $data['product_imei'],
            'ktv'  =>  $data['ktv'],
            'created_at'  =>  $data['created_at'],
            'tinhtrang'  =>  $data['status']==2?'Đã xong':'Chưa xong',
            'hientrang' =>  $ht

        ];
        return Response::json($return);
    }

    public function getMenu()
    {
        $menu   =   [
            [
                'name'  =>  'Trang chủ',
                'icon'  =>  'fa fa-home',
                'child' =>  [],
                'url'   =>  ''
            ],
            menu('Hệ thống', 'fa fa-dashboard', [
                [
                    'name' => 'QUẢN TRỊ',
                    'path' => 'caret'
                ],
                [
                    'name' => 'IP cho phép đăng nhập',
                    'path' => 'admin/iplist'
                ],
                [
                    'name' => 'Event Log',
                    'path' => 'admin/log'
                ],
                [
                    'name' => 'Thông báo',
                    'path' => 'thong-bao'
                ],
                [
                    'name' => 'THÀNH VIÊN',
                    'path' => 'caret'
                ],
                [
                    'name' => 'Phân quyền',
                    'path' => 'permission'
                ],
                [
                    'name' => 'Các quyền',
                    'path' => 'UserAddon'
                ],
            ],'#', TRUE),
            [
                'name'  =>  'Nhân sự',
                'icon'  =>  'fa fa-book',
                'child' => [
                    [
                        'name' => 'Lịch làm việc',
                        'path' => 'cham-cong/lich'
                    ],
                    [
                        'name' => 'Tổng hợp công',
                        'path' => 'lich-lam-viec'
                    ],
                    [
                        'name' => 'Làm thêm/Nghỉ sớm',
                        'path' => 'lich-lam-viec/xacnhanca/quanly'
                    ],
                    [
                        'name' => 'Thưởng/phạt',
                        'path' => 'thuongphat'
                    ],
                    [
                        'name' => 'Lịch sử công',
                        'path' => 'cham-cong'
                    ],

                    [
                        'name' => 'Danh sách NV',
                        'path' => 'user/list'
                    ],
                ],
                'url'   =>  '#a'
            ],
            menu('Tài sản CĐ', 'fa fa-desktop',[
                [
                    'name' => 'Danh sách TSCĐ',
                    'path' => 'tai-san-co-dinh'
                ],
                [
                    'name' => 'Danh sách điều chuyển',
                    'path' => 'tai-san-co-dinh/danh-sach-dieu-chuyen'
                ],
            ], '#',TRUE),
            menu('Tổng đài', 'fa fa-phone',[
                [
                    'name' => 'Danh sách cuộc gọi',
                    'path' => 'tong-dai'
                ],
                [
                    'name' => 'Thống kê tổng đài',
                    'path' => 'tong-dai/report'
                ]
            ], '#',TRUE),
            [
                'name'  =>  'Công cụ',
                'icon'  =>  'fa fa-briefcase',
                'child' => [
                    [
                        'name' => 'In bảng giá',
                        'path' => 'cong-cu/in-bang-gia'
                    ]

                ],
                'url'   =>  '#a'
            ]

        ];
        foreach ($menu as $k=>$v){
            if($v==[]){
                unset($menu[$k]);
            }
        };
        return Response::json($menu);
    }

    public function getLeftside()
    {
        $max_id = Maychamcong::where('MaChamCong', '=', Auth::user()->uid)->max('NgayCham');
        $chamcong = Maychamcong::where('MaChamCong', '=', Auth::user()->uid)->where('NgayCham', '=', $max_id)->get()->toArray();
        if (!empty($chamcong)) {
            $and = '';
            $times = [];
            foreach ($chamcong as $item) {
                $times[] = Carbon::createFromFormat('Y-m-d H:i:s.u', $item['GioCham'])->format('H:i');
            }
        }
        $camai=Chamcong::where('date','=',Carbon::now()->addDay()->toDateString())->where('uid','=',Auth::user()->uid)->get();
        $ca =   0;
        if(!empty($camai[0]))
            $ca = $camai['0']->ca;
        $note   =   'Ghi chú nhanh tại đây';
        $quicknote = Auth::user()->note()->first();
        if(!empty($quicknote))
            $note   =   $quicknote->content;
        $tongcong   =   Chamcong::where('uid','=',Auth::user()->uid)->where('date','>=',Carbon::now()->startOfMonth())->sum('cong');
        return Response::json([
            'ngaycham'  =>  Carbon::parse($max_id)->format('d/m/Y'),
            'chamcong'  =>  $times,
            'ca'    =>  $ca,
            'quicknote'  =>  $note,
            'tongcong'  =>  $tongcong
        ]);
    }

    public function  postSaveNote()
    {
        $note   =   Input::get('note');
        $currNote   =   Quicknote::where('user_id','=',Auth::user()->id)->first();
        echo $note;
        if(!empty($currNote)){
            $currNote->content  =   $note;
            $currNote->save();
        }
        else
            Quicknote::insert(['content'=>$note,'user_id'=>Auth::user()->id]);
    }

    public function getRoutelist()
    {
        $addition   =   !empty(request('type',''))?request('type','').'/':'';
        $hideinfo =   request('hideinfo', 0);
        $data   =   \Route::getRoutes();
        $result =   [];
        $string = request()->input('term');
        $method =   request('method','');
        foreach($data as $item){
            if(strstr($item->uri, $addition.$string) &&($method=='' || strtolower($item->methods()[0])==$method)) {
                $result[] = [
                    'id' => $item->uri,
                    'name' => $hideinfo==0?$item->methods()[0].'- '.$item->uri:$item->uri,
                    'method'    => strtolower($item->methods()[0])
                ];
            }
        }
        $result[]   =   [
            'id'    =>  $string,
            'name'  =>  trans('permissions.spec').': '.$string,
            'method'    =>  'get'
        ];
        return response()->json($result);
    }
}