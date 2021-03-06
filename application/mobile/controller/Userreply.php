<?php
namespace app\mobile\controller;
use think\Controller;
use think\Request;
use think\Db;
use think\Cookie;
use app\mobile\model\QnasReplyDetails;
use app\mobile\model\Users;
use app\mobile\model\UserTagDetails;
use app\mobile\model\ReplyAdditionDetails;

class Userreply extends Controller
{
    public function index()
    {
		if(!Cookie::has('userid')){
			return $this->redirect('/mobile/login');
		}
		$reply = new QnasReplyDetails;
		$reply_list = $reply->getReplyDetailsByUserId(Cookie::get('userid'));
		if($reply_list){
			$addition = new ReplyAdditionDetails;
			foreach($reply_list as $n=>$reply){
				$reply->formatCoins = floatval($reply->qna_coins);
				$reply->arbitrateCoins = floatval($reply->arbitrate_coins);
				$reply->addition = $addition->getReplyAdditions($reply->replyid);
			}
		}
        $this->assign('reply_list',$reply_list);
        $this->assign('username',Cookie::get('username'));
        $this->assign('header_type','user');
		$user = new Users;
		$user->chkReminder(Cookie::get('userid'));
		$userinfo = $user->getUserInfo(Cookie::get('userid'));
        $this->assign('userinfo',$userinfo);
		$user->cleanAnswerReminder(Cookie::get('userid'));
		$user_tag = new UserTagDetails;
		$user_tag_list = $user_tag->getTagListByUserId(Cookie::get('userid'),6);
        $this->assign('user_tag_list',$user_tag_list);
        return $this->fetch();
	}
}