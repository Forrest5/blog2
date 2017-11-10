<?php

namespace App\Http\Controllers\Admin;

use App\Http\Model\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class IndexController extends CommonController
{
    public function index()
    {
        return view('admin.index');
    }

    public function info()
    {
        return view('admin.info');
    }

    public function changePasswd()
    {

        if ($input = Input::all()) {
            $username = session('user');
            $info = Admin::where('username', '=', $username)->first();
            $_passwd = Crypt::decrypt($info->passwd);
            $input['passwd_u'] = $_passwd;
//                dd($_passwd, $input['password_o']);die;
                $rules = [
                    'password_o'=>"same:passwd_u",
                    'password'=>'required|between:6,20|same:password_confirmation',
                ];
                $msg = [
                    'password_o.same'=>'原始密码错误',
                    'password.required'=>'新密码不能为空',
                    'password.between'=>'新密码必须在6-20位之间',
                    'password.same'=>'两次输入的密码不一致',
                ];
                $validate = Validator::make($input, $rules, $msg);
                if ($validate->fails()) {
                    return back()->withErrors($validate);
                }else {
                    $info->passwd = Crypt::encrypt($input['password']);
                    $info->save();
                    return redirect('admin/info');
                }
        }else {
            return view('admin.changePasswd');
        }
    }
}
