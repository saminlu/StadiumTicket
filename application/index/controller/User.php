<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 2018/12/7
 * Time: 21:57
 */

namespace app\index\controller;

use think\Controller;
use think\Db;
use app\index\model\UserModel;

class User extends Controller
{
    public function reg()
    {
        $param = input("post.");
        if ($param && isset($param["username"]) && isset($param["password"])) {
            $data["username"] = $param["username"];
            $data["password"] = md5(md5($param["password"]));
            $userModel = new UserModel();
            $isReg = $userModel->where('username',$data["username"])->find();
            if($isReg){
                $this->error("用户名已被注册");
            }
            $rs = $userModel->save($data);
            if ($rs) {
                $this->success("注册成功", 'user/login');
            } else {
                $this->error("注册失败");
            }
        }
        return $this->fetch();
    }

    public function login()
    {
        $param = input("post.");
        if ($param && isset($param["username"]) && isset($param["password"])) {
            $data["username"] = $param["username"];
            $data["password"] = md5(md5($param["password"]));
            $userModel = new UserModel();
            $where = array();
            $where['username'] = $data["username"];
            $where['password'] = $data["password"];
            $rs = $userModel->where($where)->find();
            if ($rs) {
                session("user",$rs);
                $this->success("登录成功", 'index/index');
            } else {
                $this->error("登录失败");
            }
        }
        return $this->fetch();
    }

    public function logout(){
        session(null);
        $this->redirect("index/index");
    }
}