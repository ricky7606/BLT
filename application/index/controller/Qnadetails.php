<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Db;
use think\Cookie;
use app\index\model\Qnas;
use app\index\model\QnasReply;
use app\index\model\QnasUser;
use app\index\model\QnasPending;
use app\index\model\QnasReplyDetails;
use app\index\model\Attention;
use app\index\model\Follow;
use app\index\model\Likes;
use app\index\model\Users;
use app\index\model\UserTagDetails;
use app\index\model\ReplyAdditionDetails;
use app\index\model\Ads;

class QnaDetails extends Controller
{
    public function index()
    {
		$qnaid = Request::instance()->get('id');
		if($qnaid == ''){
			return $this->redirect('/');
		}
		
		$qna = new Qnas; 
		$qna_detail = $qna->getQnaDetailsByQnaId($qnaid);
		if(!$qna_detail){
			return $this->redirect('/index');
		}
		$userid = Cookie::get('userid');
		$this->assign('userid',$userid);
		$att = new Attention;
		$follow = new Follow;
		$qna_pending=new QnasPending;
		$likes = new Likes;
		if(Cookie::has('userid')){
			$qna_follow = $follow->getFollowByQnaIdUserId($qna_detail->qnaid, $userid);
			$qna_like = $likes->getLikeByQnaIdUserId($qna_detail->qnaid, $userid);
			$pending_info = $qna_pending->getPendingByUserId($qna_detail->qnaid, $userid );
			if($pending_info){
				$qna_detail['pending_status'] = $pending_info->status;
				$qna_detail['pendingid'] = $pending_info->pendingid;
			}else{
				$qna_detail['pending_status'] = '-1';
				$qna_detail['pendingid'] = '';
			}
			if($qna_follow){
				$qna_detail['follow'] = 1;
			}else{
				$qna_detail['follow'] = -1;
			}
			if($qna_like){
				$qna_detail['qna_like'] = 1;
			}else{
				$qna_detail['qna_like'] = -1;
			}
			$qna_user_att = $att->getAttentionByUserId($qna_detail->userid, $userid);
			if($qna_user_att){
				$qna_detail['qna_user_att'] = 1;
			}else{
				$qna_detail['qna_user_att'] = -1;
			}
		}
		$qna_detail['formatCoins'] = floatval($qna_detail->coins);
		$follow_count = $follow->getFollowCount($qna_detail->qnaid);
		$qna_detail['followCount'] = $follow_count->followCount;
		$like_count = $likes->getLikeCount($qna_detail->qnaid);
		$qna_detail->likeCountQna = $like_count->likeCount;
		$this->assign('qnainfo',$qna_detail);
		$user = new Users;
		$userinfo = $user->getUserInfo($qna_detail->userid);
		$this->assign('qna_userinfo',$userinfo);
		
		$reply = new QnasReplyDetails;
		$reply_list = $reply->getReplyDetailsByQnaId($qnaid); 
		if($reply_list){
			$addition = new ReplyAdditionDetails;
			foreach($reply_list as $n=>$reply){
				if(Cookie::has('userid')){
					$qna_follow = $follow->getFollowByQnaIdUserId($reply->qnaid, $userid);
					$reply_like = $likes->getLikeByQnaIdUserId($reply->replyid, $userid);
					$pending_info = $qna_pending->getPendingByUserId($reply->qnaid, $userid );
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
					if($reply_like){
						$reply_list[$n]['reply_like'] = 1;
					}else{
						$reply_list[$n]['reply_like'] = -1;
					}
					$reply_user_att = $att->getAttentionByUserId($reply->userid, $userid);
					if($reply_user_att){
						$reply_list[$n]['reply_user_att'] = 1;
					}else{
						$reply_list[$n]['reply_user_att'] = -1;
					}
				}
				$like_count = $likes->getLikeCount($reply->replyid);
				$reply->likeCountReply = $like_count->likeCount;
				$reply_userinfo = $user->getUserInfo($reply->userid);
				$reply->reply_userinfo = $reply_userinfo;
				$reply->addition = $addition->getReplyAdditions($reply->replyid);
			}
		}
		$this->assign('reply_list',$reply_list);
		$qnauser=new QnasUser();
		$qna_list=$qnauser->getNewQnas(); 
		if($qna_list){
			foreach ($qna_list as $n=>$qna){ 
				$qna->shortTitle = getContentText($qna->title,35);
			}
		}
		$this->assign('qna_list',$qna_list);
		if(Cookie::has('userid')){
			$this->assign('header_type','user');
			$user->chkReminder($userid);
			$userinfo = $user->getUserInfo($userid);
			$this->assign('userinfo',$userinfo);
			$this->assign('userid',$userid);
		}else{
			$this->assign('header_type','normal');
		}
		$ads = new Ads;
		$ad1 = $ads->getAdsByPosition(1);
		$ad2 = $ads->getAdsByPosition(2);
		$this->assign('ad1', $ad1); 
		$this->assign('ad2', $ad2); 
        return $this->fetch(); 
	}
	
	public function updateApply(){
		if(!Cookie::has('userid')){
			return $this->redirect('/index/login');
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
			return $this->redirect('/index/login');
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
			return $this->redirect('/index/login');
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