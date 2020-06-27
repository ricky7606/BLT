<?php
namespace app\mobile\controller;
use think\Controller;
use think\Request;
use think\Cookie;
use app\mobile\model\Users;
use app\mobile\model\UsersTags;
use app\mobile\model\UserTagDetails;
use app\mobile\model\Tags;

class Tag extends Controller
{
    public function index()
    {
		if(!Cookie::has('userid')){
			return $this->redirect('/index/login');
		}
        $this->assign('username',Cookie::get('username'));
        $this->assign('header_type','user');
		$user = new Users;
		$user->chkReminder(Cookie::get('userid'));
		$userinfo = $user->getUserInfo(Cookie::get('userid'));
        $this->assign('userinfo',$userinfo);
		$user_tag = new UserTagDetails;
		$user_tag_list = $user_tag->getTagListByUserId(Cookie::get('userid'));
        $this->assign('user_tag_list',$user_tag_list);
        return $this->fetch();
	}

	public function addCustomTag($text){
		if(!Cookie::has('userid')){
			return "notlogin";
		}
		if(trim($text)==''){
			return "标签不能为空！";
		}
		$level = 2;
		$parentid = '1';
		$userid = Cookie::get('userid');
		$tag = new Tags;
		$tagid = $tag->addTag($level, $parentid, $text);
		if($tagid != ''){
			//不直接添加到用户标签集
			/*
			$usertag = new UsersTags;
			$result = $usertag->addTag($userid, $tagid);
			return $result;
			*/
			return "ok";
		}else{
			return "数据错误！";
		}
	}
	
	public function addTag($parentid, $text, $level){
		if(!Cookie::has('userid')){
			return $this->redirect('/index/login');
		}
		$userid = Cookie::get('userid');
		$tag = new Tags;
		$tagid = $tag->addTag($level, $parentid, $text);
		if($tagid != ''){
			$usertag = new UsersTags;
			$result = $usertag->addTag($userid, $tagid);
			return $result;
		}else{
			return "数据错误！";
		}
	}

}