<?php
namespace app\mobile\controller;
use think\Controller;
use think\Request;
use think\Db;
use think\Cookie;
use app\mobile\model\QnasUser;
use app\mobile\model\QnasReply;
use app\mobile\model\QnasPending;
use app\mobile\model\QnasReplyDetails;
use app\mobile\model\Attention;
use app\mobile\model\Follow;
use app\mobile\model\Users;
use app\mobile\model\UserTagDetails;
use app\mobile\model\ReplyAdditionDetails;
use app\mobile\model\Ads;

class UserReplyDetail extends Controller
{
    public function index()
    {
		$userid = Request::instance()->get('userid');
		if($userid == ''){
			return $this->redirect('/mobile');
		}

		$user = new Users;
		$userdetail = $user->getUserInfo($userid);
		if(!$userdetail){
			return $this->redirect('/mobile');
		}
		$user_att = "";
		if(Cookie::has('userid')){
			$login_userid = Cookie::get('userid');
			$att = new Attention;
			$user_att = $att->getAttentionByUserId($userdetail->userid, $login_userid);
			if($user_att){
				$user_att = 1;
			}else{
				$user_att = -1;
			}
		}
		$this->assign('userdetail',$userdetail);
		$this->assign('user_att',$user_att);

		$user_tag = new UserTagDetails;
		$user_tag_list = $user_tag->getTagListByUserId($userid);
		$this->assign('user_tag_list',$user_tag_list);
		
		$qna = new QnasUser;
		$qna_list = $qna->getQnasByUserId($userid);
		foreach($qna_list as $n=>$qna){
			$qna_list[$n]['shortTitle'] = getContentText($qna->title,35);
		}
		$this->assign('qna_list',$qna_list);

		$reply = new QnasReplyDetails;
		$reply_list = $reply->getReplyDetailsByUserId($userid);
		if($reply_list){
			$addition = new ReplyAdditionDetails;
			foreach($reply_list as $n=>$reply){
				if(Cookie::has('userid')){
					$follow = new Follow;
					$qna_pending=new QnasPending;
					$qna_follow = $follow->getFollowByQnaIdUserId($reply->qnaid, $login_userid);
					$pending_info = $qna_pending->getPendingByUserId($reply->qnaid, $login_userid );
					if($pending_info){
						$reply_list[$n]['pending_status'] = $pending_info->status;
						$reply_list[$n]['pendingid'] = $pending_info->pendingid;
					}else{
						$reply_list[$n]['pending_status'] = '-1';
						$reply_list[$n]['pendingid'] = '';
					}
					if($qna_follow){
						$reply_list[$n]['follow'] = 1;
					}else{
						$reply_list[$n]['follow'] = -1;
					}
				}
				$reply->formatCoins = floatval($reply->qna_coins);
				$reply->addition = $addition->getReplyAdditions($reply->replyid);
			}
		}
		$this->assign('reply_list',$reply_list);

		if(Cookie::has('userid')){
			$this->assign('header_type','user');
			$user->chkReminder($login_userid);
			$userinfo = $user->getUserInfo($login_userid);
			$this->assign('userinfo',$userinfo);
			$this->assign('login_userid',$login_userid);
		}else{
			$this->assign('login_userid','');
			$this->assign('header_type','normal');
		}
		$ads = new Ads;
		$ad1 = $ads->getAdsByPosition(1);
		$ad2 = $ads->getAdsByPosition(2);
		$this->assign('ad1', $ad1); 
		$this->assign('ad2', $ad2); 
        return $this->fetch(); 
	}
}