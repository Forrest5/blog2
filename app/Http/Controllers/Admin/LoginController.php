<?php

namespace App\Http\Controllers\Admin;


use App\Http\Model\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

require_once 'resources/org/Code.class.php';

class LoginController extends CommonController
{
    public function login()
    {

        if ($input = Input::all()) {
            $code = new \Code();
            $_code = $code->get();
            if (strtoupper($input['code']) != strtoupper($_code)) {
                return back()->with('msg', '验证码错误');
            }else {
                $user = Admin::first();
                if ($user->username != $input['username'] || Crypt::decrypt($user->passwd) != $input['password']) {
                    return back()->with('msg', '用户名或密码错误');
                }else {
                    session(['user'=>$user->username]);
                    $user->login_time = time();
                    $user->save();
                    return redirect('admin/index');
                }

            }
        }else {
//            return Crypt::encrypt('admin');
            session(['user'=>null]);
            return view('admin.login');
//            $user = Admin::first();
//            dd($_SERVER);
        }

    }

    public function code()
    {
        $code = new \Code();
        echo $code->make();
    }

    public function quit()
    {
        session(['user'=>null]);
        return redirect('admin/login');
    }



}
