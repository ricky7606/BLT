<?php
namespace app\mobile\controller;
use think\Controller;
use think\Request;
use think\Db;
use think\Cookie;
use app\mobile\model\Users;
use app\mobile\model\Follow;
use app\mobile\model\ArticleFollow;
use app\mobile\model\UserTagDetails;

class Userqnafollow extends Controller
{
    public function index()
    {
		if(!Cookie::has('userid')){
			return $this->redirect('/mobile/login');
		}
		$follow = new Follow;
		$follow_info = $follow->getFollowDetailsByUserId(Cookie::get('userid'));
		$article_follow = new ArticleFollow;
		$article_follow_count = $article_follow->getUserFollowCount(Cookie::get('userid'));
        $this->assign('follow_article_count',$article_follow_count);
        $this->assign('follow_list',$follow_info);
        $this->assign('follow_qna_count',count($follow_info));
        $this->assign('username',Cookie::get('username'));
        $this->assign('header_type','user');
		$user = new Users;
		$user->chkReminder(Cookie::get('userid'));
		$userinfo = $user->getUserInfo(Cookie::get('userid'));
        $this->assign('userinfo',$userinfo);
		$user_tag = new UserTagDetails;
		$user_tag_list = $user_tag->getTagListByUserId(Cookie::get('userid'),6);
        $this->assign('user_tag_list',$user_tag_list);
        return $this->fetch();
	}

	public function delFollow(){
		if(!Cookie::has('userid')){
			return $this->redirect('/mobile/login');
		}
		$followid = Request::instance()->post('followid');
		if($followid != ''){
			$follow = new Follow;
			$follow_result = $follow->deleteFollow($followid);
			if($follow_result){
				return "ok";
			}else{
				return "发生错误，取消收藏可能失败";
			}
		}else{
			return "数据错误";
		}
	}
}