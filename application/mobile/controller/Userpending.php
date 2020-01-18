<?php
namespace app\mobile\controller;
use think\Controller;
use think\Request;
use think\Db;
use think\Cookie;
use Qiniu\Auth as Auth;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;
use app\mobile\model\QnaPendingDetails;
use app\mobile\model\QnasPending;
use app\mobile\model\Users;
use app\mobile\model\UserTagDetails;
use app\mobile\model\ReplyAdditionDetails;
use app\mobile\model\QnasReplyDetails;

class Userpending extends Controller
{
    public function index()
    {
		if(!Cookie::has('userid')){
			return $this->redirect('/mobile/login');
		}
		$qna_pending = new QnaPendingDetails;
		$pending_list = $qna_pending->getPendingDetailsByUserId(Cookie::get('userid'),'1,31');
		if($pending_list){
			$reply = new QnasReplyDetails;
			$addition = new ReplyAdditionDetails;
			foreach ($pending_list as $n=>$pending){ 
				$reply_list = $reply->getReplyDetailsByQnaIdUserId($pending->qnaid,Cookie::get('userid'));
				$pending->formatCoins = floatval($pending->coins);
				if($reply_list){
					foreach($reply_list as $reply){
						$reply->addition = $addition->getReplyAdditions($reply->replyid);
					}
				}
				$pending->reply = $reply_list;
			}
		}
        $this->assign('pending_list',$pending_list);
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

	public function refusePending(){
		if(!Cookie::has('userid')){
			return $this->redirect('/mobile/login');
		}
		$pendingid = Request::instance()->post('pendingid');
		$pending = new QnasPending;
		return $pending->refusePending($pendingid);
	}

	public function arbitrate($pendingid){
		if(!Cookie::has('userid')){
			return $this->redirect('/mobile/login');
		}
		$pendingid = Request::instance()->post('pendingid');
		$pending = new QnasPending;
		return $pending->arbitrate($pendingid);
	}
}