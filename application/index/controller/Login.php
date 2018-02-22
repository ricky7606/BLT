<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Db;
use think\Session;
use think\Cookie;
use app\index\model\Users;
use think\captcha\Captcha;

class Login extends Controller
{
    public function index()
    {
        $this->assign('header_type','login');
        return $this->fetch();
	}

	public function getLogin(){
		$mobile = Request::instance()->post('mobile');
		$password = Request::instance()->post('pwd');
		$imgcode = Request::instance()->post('imgcode');
		$isonemonth = Request::instance()->post('isonemonth');
		$id = "";
		$captcha = new Captcha();
		$captcha->reset = false;
		if(!$captcha->check($imgcode, $id)){
			return "验证码错误或者请求超时";
		}
		if($mobile!="" && $password!="" && $imgcode!=""){
			//实例化模型后调用查询
			$user = new Users();
			//登录验证
			return $user->getLogin($mobile, $password, $isonemonth);
		}else{
			return "数据有误，请检查后重试";
		}
	}
	
	public function Logout(){
		Cookie::delete('userid');
		Cookie::delete('mobile');
		Cookie::delete('username');
		return $this->redirect('/index');
	}
	
	public function forgetpassword(){
		//开启SESSION
		session_start();
		//生成注册页面随机数作为token
		$register_token = random(6,1);
		$_SESSION['register_token'] = $register_token;
		
        $this->assign('header_type','login');
        $this->assign('register_token',$register_token);
        return $this->fetch();
	}

	public function forgetpassword2(){
		//开启SESSION
		session_start();
		if(empty($_SESSION['register_token']) || empty($_SESSION['mobile'])){
			$this->forgetpassword();
		}
        $this->assign('header_type','login');
        $this->assign('register_token',$_SESSION['register_token']);
        return $this->fetch('resetpassword');
	}
	
	public function checkReset(){
		session_start();
		$imgcode = Request::instance()->post('imgcode');
		$register_token = Request::instance()->post('register_token');
		$smscode = Request::instance()->post('smscode');
		$mobile = Request::instance()->post('mobile');
		if(empty($_SESSION['register_token']) or $register_token != $_SESSION['register_token']){
			return '请求超时，请刷新页面后重试';
		}
		$id = "";
		$captcha = new Captcha();
		$captcha->reset = false;
		if(!$captcha->check($imgcode, $id)){
			return "图形验证码错误或者请求超时";
		}
		if($_SESSION['mobile_code'] && $_SESSION['mobile']){
			if($smscode != $_SESSION['mobile_code'] || $mobile != $_SESSION['mobile']){
				return "短信证码错误或者请求超时";
			}
		}else{
			return "请求超时，请刷新页面后重试";
		}
		return "ok";
	}
	
	public function resetPassword(){
		session_start();
		if(empty($_SESSION['mobile']) || empty($_SESSION['register_token'])){
			return "请求超时，请刷新页面后重试";
		}
		$password = Request::instance()->post('password');
		$password = password_hash($password,PASSWORD_BCRYPT);
		$register_token = Request::instance()->post('register_token');
		if(empty($_SESSION['register_token']) or $register_token != $_SESSION['register_token']){
			return "请求超时，请刷新页面后重试";
		}
		$user = new Users();
		return $user->resetPassword($_SESSION['mobile'], $password);
	}
}
