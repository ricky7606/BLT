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
use app\mobile\model\Likes;
use app\mobile\model\Users;
use app\mobile\model\UserTagDetails;
use app\mobile\model\Ads;

class QnaReply extends Controller
{
    public function index()
    {
		$replyid = Request::instance()->get('id');
		if($replyid == ''){
			return $this->redirect('/mobile');
		}
		$reply = new QnasReplyDetails;
		$reply_detail = $reply->getTopReplyDetailsByReplyId($replyid); 
		if(!$reply_detail){
			return $this->redirect('/mobile');
		}
		$att = new Attention;
		$follow = new Follow;
		$likes = new Likes;
		if(Cookie::has('userid')){
			$userid = Cookie::get('userid');
			$qna_pending=new QnasPending;
			$qna_follow = $follow->getFollowByQnaIdUserId($reply_detail->qnaid, $userid);
			$qna_like = $likes->getLikeByQnaIdUserId($reply_detail->qnaid, $userid);
			$reply_like = $likes->getLikeByQnaIdUserId($reply_detail->replyid, $userid);
			$pending_info = $qna_pending->getPendingByUserId($reply_detail->qnaid, $userid );
			if($pending_info){
				$reply_detail['pending_status'] = $pending_info->status;
				$reply_detail['pendingid'] = $pending_info->pendingid;
			}else{
				$reply_detail['pending_status'] = '-1';
				$reply_detail['pendingid'] = '';
			}
			if($qna_follow){
				$reply_detail['follow'] = 1;
			}else{
				$reply_detail['follow'] = -1;
			}
			if($qna_like){
				$reply_detail['qna_like'] = 1;
			}else{
				$reply_detail['qna_like'] = -1;
			}
			if($reply_like){
				$reply_detail['reply_like'] = 1;
			}else{
				$reply_detail['reply_like'] = -1;
			}
			$reply_user_att = $att->getAttentionByUserId($reply_detail->userid, $userid);
			if($reply_user_att){
				$reply_detail['reply_user_att'] = 1;
			}else{
				$reply_detail['reply_user_att'] = -1;
			}
			$qna_user_att = $att->getAttentionByUserId($reply_detail->qna_userid, $userid);
			if($qna_user_att){
				$reply_detail['qna_user_att'] = 1;
			}else{
				$reply_detail['qna_user_att'] = -1;
			}
			$this->assign('header_type','user');
			$user = new Users;
			$user->chkReminder($userid);
			$userinfo = $user->getUserInfo($userid);
			$this->assign('userinfo',$userinfo);
			$this->assign('userid',$userid);
		}else{
			$this->assign('header_type','normal');
			$this->assign('userid','');
		}
		$follow_count = $follow->getFollowCount($reply_detail->qnaid);
		$reply_detail['followCount'] = $follow_count->followCount;
		$like_count = $likes->getLikeCount($reply_detail->qnaid);
		$reply_detail->likeCountQna = $like_count->likeCount;
		$like_count = $likes->getLikeCount($reply_detail->replyid);
		$reply_detail->likeCountReply = $like_count->likeCount;
		$user_tag = new UserTagDetails;
		$qna_tag_list = $user_tag->getTagListByUserId($reply_detail->qna_userid);
		$reply_tag_list = $user_tag->getTagListByUserId($reply_detail->userid);
		$this->assign('qna_tag_list',$qna_tag_list);
		$this->assign('reply_tag_list',$reply_tag_list);
		$userdetail = new Users;
		$qna_userinfo = $userdetail->getUserInfo($reply_detail->qna_userid);
		$reply_userinfo = $userdetail->getUserInfo($reply_detail->userid);
		$this->assign('qna_userinfo',$qna_userinfo);
		$this->assign('reply_userinfo',$reply_userinfo);
		$reply_detail['formatCoins'] = floatval($reply_detail->qna_coins);
		$this->assign('reply_detail',$reply_detail);
		$ads = new Ads;
		$ad1 = $ads->getAdsByPosition(1);
		$ad2 = $ads->getAdsByPosition(2);
		$this->assign('ad1', $ad1); 
		$this->assign('ad2', $ad2); 
        return $this->fetch(); 
	}
	
	public function updateApply(){
		if(!Cookie::has('userid')){
			return $this->redirect('/mobile/login');
		}
		$applyid = Request::instance()->post('applyid');
		$applystatus = Request::instance()->post('applystatus');
		if($applystatus == 1 || $applystatus == 2){
			$apply = new QnasPending;
			return $apply->updatePending($applyid,$applystatus);
		}else{
			return "数据错误！";
		}
	}

	public function updateReply(){
		if(!Cookie::has('userid')){
			return $this->redirect('/mobile/login');
		}
		$replyid = Request::instance()->post('replyid');
		$replystatus = Request::instance()->post('replystatus');
		if($replystatus == 1 || $replystatus == 2){
			$reply = new QnasReply;
			return $reply->updateReply($replyid,$replystatus);
		}else{
			return "数据错误！";
		}
	}
	
	public function attUser(){
		if(!Cookie::has('userid')){
			return $this->redirect('/mobile/login');
		}
		$attention_userid = Request::instance()->post('userid');
		if($attention_userid != ''){
			$attention = new Attention;
			return $attention->saveNewAttention(Cookie::get('userid'),$attention_userid);
		}else{
			return "数据错误";
		}
	}
}