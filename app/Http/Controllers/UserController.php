<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditUserRequest;
use App\Http\Requests\FormUserRequest;
use App\Http\Requests\LoginRequest;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Auth\DataTables;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Namshi\JOSE\JWT;
use JWTAuth;
use phpDocumentor\Reflection\PseudoTypes\True_;

class UserController extends Controller
{
    public function getLogin()
    {
        return v('pages.login');
    }

    public function login($username, $password, $remember=false,$api=false)
    {
        $logins =   json_decode(settings('system_loginas', json_encode(['id'])), 'true');
        if(in_array('username',$logins))
            $loginUsername   =   auth()->attempt(['name'=>$username,'password'=>$password], $remember);
    //    if(in_array('email',$logins))
    //        $loginEmail  =   auth()->attempt(['email'=>$username,'password'=>$password], $remember);
        if(in_array('id',$logins))
            $loginId  =   auth()->attempt(['uid'=>$username,'password'=>$password], $remember);
        if(!empty($loginUsername) || !empty($loginId)){
            if($api==true){
                if(!empty($loginUsername))
                    return 'username';
                if(!empty($loginId))
                    return 'uid';
            //    if(!empty($loginEmail))
            //        return 'email';
            }
            return TRUE;
        } else {
            return TRUE;
        }
    }
    public function postLogin(LoginRequest $request)
    {
        if($this->login($request->input('id'),$request->input('password'),$request->has('remember'))){
            Event::fire('event.login', []);
            return redirect()->to(asset('/'));
        } else {
//            return response('a');
             return redirect()->back()->withErrors(trans('auth.failed'));
        }
    }

    public function getLogout()
    {
        auth()->logout();
        return redirect()->to(asset('/'));
    }
    public function getList() {
        return v('users.list');
    }
    public function dataList() {
        if (!function_exists('currency_format')) {
            function currency_format($number, $suffix = 'đ') {
                if (!empty($number)) {
                    return number_format($number, 0, ',', '.') . "{$suffix}";
                }
            }
        }
        $data   =   User::with('group','branch');

        $result = Datatables::of($data)
            ->addColumn('group', function(User $user) {
                return $user->group->name;
            })->addColumn('branch', function(User $user) {
                return $user->branch->name;
            })->addColumn('luongcung', function(User $user) {
                return currency_format($user->luongcung);
            })->addColumn('manage', function($user) {
                return a('config/user/del', 'id='.$user->id,trans('g.delete'), ['class'=>'btn btn-xs btn-danger'],'#',"return bootbox.confirm('".trans('system.delete_confirm')."', function(result){if(result==true){window.location.replace('".asset('config/user/del?id='.$user->id)."')}})").'  '.a('config/user/edit', 'id='.$user->id,trans('g.edit'), ['class'=>'btn btn-xs btn-default']).'  '.a('sap-lich/{id}', 'id='.$user->id,'Sắp ca', ['class'=>'btn btn-xs btn-default']);
            })->rawColumns(['manage']);

        return $result->make(true);
    }

    public function getDeletedList() {
        return v('users.deleted_list');
    }

    public function dataDeletedList() {
        $data   =   User::with('group','branch')->onlyTrashed ();

        $result = Datatables::of($data)
            ->addColumn('group', function(User $user) {
                return $user->group->name;
            })->addColumn('branch', function(User $user) {
                return $user->branch->name;
            })->addColumn('manage', function($user) {
                return a('config/user/recast', 'id='.$user->id,'Khôi phục', ['class'=>'btn btn-xs btn-success'],'#',"return bootbox.confirm('Bạn có chắc chắn muốn khôi phục tài khoản này?', function(result){if(result==true){window.location.replace('".asset('config/user/recast?id='.$user->id)."')}})");
            })->rawColumns(['manage']);

        return $result->make(true);
    }

    public function getCreate()
     {
        return v('users.create');
    }

    public function postCreate(FormUserRequest $request)
    {
        $data   =   new User();
        $data->name   =   $request->name;
        $data->email    =   $request->uid.'@tanque.vn';
        $data->password =   Hash::make($request->password);
        $data->branch_id    =   $request->branch_id;
        $data->group_id =   $request->group_id;
        if(!empty($request->birthday))
            $data->birthday =   Carbon::createFromFormat('d/m/Y', $request->birthday);
        $data->address =   $request->address;
        $data->phone =   $request->phone;
        $data->cmnd =   $request->cmnd;
        $data->created_at   =   Carbon::now();
        $data->uid  =   $request->uid;
        $data->parttime = !empty($request->parttime)?1:0;
        if(!is_numeric($request->luongcung)){
            return 'Trường Lương cứng chỉ được phép nhập số!';
        }else{
        $data->luongcung = $request->luongcung;
        }
        $data->save();
        set_notice(trans('users.add_success'), 'success');
        return redirect()->back();
    }
    public function getEdit()
    {
        $data   =   User::find(request('id'));
        if(!empty($data)){
            return v('users.edit', compact('data'));
        }else{
            set_notice(trans('system.not_exist'), 'warning');
            return redirect()->back();
        }
    }
    public function postEdit(EditUserRequest $request)
    {
        $data   =   User::find($request->id);
        if(!empty($data)){
            $data->name   =   $request->name;
            if(!empty($request->password))
            $data->password =   Hash::make($request->password);
            $data->branch_id    =   $request->branch_id;
            $data->group_id =   $request->group_id;
            if(!empty($request->birthday))
                $data->birthday =   Carbon::createFromFormat('d/m/Y', $request->birthday);
            $data->address =   $request->address;
            $data->phone =   $request->phone;
            $data->cmnd =   $request->cmnd;
            $data->uid  =   $request->uid;
            $data->parttime = !empty($request->parttime)?1:0;
            if(!empty($request->luongcung)){
                if (!is_numeric($request->luongcung)) {
                    set_notice(trans('Trường Lương cứng chỉ được phép nhập số!'), 'warning');
                    return redirect()->back();
                } else {
                    $data->luongcung = $request->luongcung;
                }
            }
            $data->save();
            set_notice(trans('system.edit_success'), 'success');
        }else
            set_notice(trans('system.not_exist'), 'warning');
        return redirect()->back();
    }
    public function getDelete()
    {
        $data   =   User::find(request('id'));
        if(!empty($data)){
            $data->delete();
            set_notice(trans('system.delete_success'), 'success');
        }else
            set_notice(trans('system.not_exist'), 'warning');
        return redirect()->back();
    }

    public function getReCast()
    {
        $data   =   User::withTrashed()->where('id',request('id'))->first();
        if(!empty($data)){
            $data->restore();
            set_notice('Khôi phục tài khoản '.$data->name. ' thành công!', 'success');
        }else
            set_notice(trans('system.not_exist'), 'warning');
        return redirect()->back();
    }

    public function apiLogin(LoginRequest $request)
    {
       print_r($request->input());
        if($info = $this->login($request->input('id'),$request->input('password'),$request->has('remember'), true)){
            $api_token  =   str_random(60);
            User::where('id',auth()->user()->id)->update(['api_token'=>$api_token]);
            return response()->json(['status'=>'success', 'token'=>$api_token]);
        } else {
            return response()->json(['status'=>'wrong'],422);
        }
    }

    public function getChangepassword()
    {
        if (auth()->user()->id != request('id')) {
            set_notice(trans('Bạn không có quyền đổi mật khẩu id này!'), 'warning');
            return redirect()->back();
        } else {
            $data = User::find(request('id'));
            if (!empty($data)) {
                return v('users.changepassword', compact('data'));
            } else {
                set_notice(trans('system.not_exist'), 'warning');
                return redirect()->back();
            }
        }
    }
    public function postChangepassword()
    {
        if (auth()->user()->id != request('id')) {
            set_notice(trans('Bạn không có quyền đổi mật khẩu id này!'), 'warning');
            return redirect()->back();
        } else {
            $data = User::find(request('id'));
            if (!empty($data)) {
                if (!empty(request('old_password')) && !empty(request('new_password')) && !empty(request('confirm_password'))) {
                    if(auth()->attempt(['id'=>auth()->user()->id,'password'=>request('old_password')])) {
                        if(request('new_password')==request('confirm_password')) {
                            $data->password = Hash::make(request('new_password'));
                            $data->save();
                            set_notice(trans('Đổi mật khẩu thành công!'), 'success');
                            return redirect()->to(asset(''));
                        }
                        else{
                            set_notice(trans('Xác nhận mật khẩu mới chưa trùng khớp, vui lòng kiểm tra lại'), 'warning');
                            return redirect()->to('config/user/changepassword?id='.auth()->user()->id);
                        }
                    }
                    else{
                        set_notice(trans('Mật khẩu cũ chưa đúng, vui lòng kiểm tra lại'), 'warning');
                        return redirect()->to('config/user/changepassword?id='.auth()->user()->id);
                    }
                }
                else{
                    set_notice(trans('Vui lòng nhập đầy đủ thông tin'), 'warning');
                    return redirect()->to('config/user/changepassword?id='.auth()->user()->id);
                }
            } else
                set_notice(trans('system.not_exist'), 'warning');
        }
        return redirect()->back();
    }
}
