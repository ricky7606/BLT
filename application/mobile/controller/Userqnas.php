<?php
namespace app\mobile\controller;
use think\Controller;
use think\Request;
use think\Db;
use think\Cookie;
use app\mobile\model\QnasUser;
use app\mobile\model\QnaPendingDetails;
use app\mobile\model\QnasReply;
use app\mobile\model\QnasPending;
use app\mobile\model\QnasReplyDetails;
use app\mobile\model\ReplyAdditionDetails;
use app\mobile\model\ReplyAddition;
use app\mobile\model\Attention;
use app\mobile\model\Users;
use app\mobile\model\UserTagDetails;

class Userqnas extends Controller
{
    public function index()
    {
		if(!Cookie::has('userid')){
			return $this->redirect('/mobile/login');
		}
		$qna_user = new QnasUser;
		$qna_list = $qna_user->getQnasByUserId(Cookie::get('userid')); 
		if($qna_list){
			$pending = new QnaPendingDetails;
			$reply = new QnasReplyDetails;
			$addition = new ReplyAdditionDetails;
			foreach ($qna_list as $n=>$qna){ 
				$reply_list = $reply->getReplyDetailsByQnaId($qna->qnaid);
				if($reply_list){
					foreach($reply_list as $reply){
						$reply->addition = $addition->getReplyAdditions($reply->replyid);
						$reply->arbitrateCoins = floatval($reply->arbitrate_qnauser_coins);
					}
				}
				$qna_list[$n]->reply = $reply_list;
				$qna_list[$n]->formatCoins = floatval($qna->coins);
				$qna_list[$n]->apply = $pending->getPendingDetailsByQnaId($qna->qnaid);
				$qna_list[$n]->invite = $pending->getPendingInviteByQnaId($qna->qnaid);
			}
		}
        $this->assign('qna_list',$qna_list);
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

	public function cancelInvite(){
		if(!Cookie::has('userid')){
			return $this->redirect('/mobile/login');
		}
		$pendingid = Request::instance()->post('pendingid');
		if($pendingid != ''){
			$invite = new QnasPending;
			return $invite->updatePending($pendingid,-1);
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
		$share = Request::instance()->post('share');
		if($share == 'true'){
			$share = 1;
		}else{
			$share = 0;
		}
		if($replystatus == 1 || $replystatus == 2){
			$reply = new QnasReply;
			return $reply->updateReply($replyid,$replystatus,$share);
		}else{
			return "数据错误！";
		}
	}

	public function shareReply(){
		if(!Cookie::has('userid')){
			return $this->redirect('/mobile/login');
		}
		$replyid = Request::instance()->post('replyid');
		if($replyid != ''){
			$reply = new QnasReply;
			return $reply->shareReply($replyid);
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
	
	public function saveAdditionReply(){
		if(!Cookie::has('userid')){
			return $this->redirect('/mobile/login');
		}
		$replyid = Request::instance()->post('replyid');
		$pendingid = Request::instance()->post('pendingid');
		$content = Request::instance()->post('content');
		$content_text = Request::instance()->post('content_text');
		$addition_type = Request::instance()->post('addition_type');
		$thumb_img = getThumbImg($content);
		if($thumb_img != ''){
			$content_text = getContentText($content_text,385);
		}else{
			$content_text = getContentText($content_text,540);
		}
		if($replyid != '' && $pendingid != '' && ($addition_type == '1' or $addition_type == '2')){
			$addition = new ReplyAddition;
			return $addition->saveAdditionReply(Cookie::get('userid'), $replyid, $pendingid, $content, $content_text, $thumb_img, $addition_type);
		}else{
			return "数据错误";
		}
	}
}