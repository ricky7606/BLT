<?php
namespace app\mobile\controller;
use think\Controller;
use think\Request;
use think\Cookie;
use think\Session;
use think\captcha\Captcha;
use app\mobile\model\Users;

class Mobileregister extends Controller
{
    public function index()
    {
		//开启SESSION
		session_start();
		//生成注册页面随机数作为token
		$register_token = random(6,1);
		$_SESSION['register_token'] = $register_token;
		
        $this->assign('register_token',$register_token);
        return $this->fetch(); 
	}
	
	public function regOK(){
		session_start();
		$str = udate('Y-m-d H:i:s.u');
        $this->assign('str',$str);
        $mobile = $_SESSION['mobile_user_id'];
		$password = $_SESSION['mobile_password'];
        $this->assign('userid', $mobile);

        $user = new Users();
			//登录验证
		$user->getLogin($mobile, $password, "yes");
        return $this->fetch('tag'); 
	}

	public function paper(){
		return $this->fetch('paper');
	}

	public function paperSubmit(){
		return $this->fetch('paperSubmit');
	}
}