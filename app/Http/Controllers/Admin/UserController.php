<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * 登录
     */
    public function login(Request $request)
    {
        $type = 'A1001';
        $post = $request->all();
        $this->apiPreTreat($type, $post, false);
        //查看有没有该用户
        //查看密码对不对
        $username = $post['username'];
        $password = $post['password'];
        $Admin = DB::table('admin');
        $row = $Admin->where('username', $username)->first();
        //用户是否存在
        if ($row) {
            //密码是否正确
            if ($row->password == md5($password)) {
                //个人id存进session
                $_SESSION['username'] = $username;
                //记录登陆情况
                $time = date('Y-m-d H:i:s');
                DB::table('admin')
                    ->where('username', $username)
                    ->update([
                        'last_login_time' => $time
                    ]);
                $msg['username']=$_SESSION['username'];
                return $this->response_treatment(0, $type,$msg);
            } else {
                return $this->response_treatment(5, $type);
            }
        } else {
            return $this->response_treatment(4, $type);
        }
    }

    /**登出
     * @param Request $request
     * @return mixed
     */
    public function logOut(Request $request)
    {
        $type = 'A1002';
        $post = $request->all();
        $this->apiPreTreat($type, $post);
        if(isset($_SESSION['username'])){
            $_SESSION['username']=null;
        }
        return $this->response_treatment(0, $type);
    }
}
