<?php
namespace app\index\controller;
use think\Controller;//引入Controller类
use think\Session;
use app\index\model\QnasUser;
use app\index\model\QnasPending;
use app\index\model\QnasReply;
use app\index\model\QnasReplyDetails;
use app\index\model\Follow;
use app\index\model\Likes;
use app\index\model\Attention;
use think\Db;
use think\Request;
use think\Cookie;
use Qiniu\Auth as Auth;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;
use app\index\model\Users;
use app\index\model\ReplyAdditionDetails;
use app\index\model\Ads;

class Feature extends Controller
{
    public function index()
    {
		$qnauser=new QnasUser();
		$qna_list=$qnauser->getNewQnas(); 
		$reply = new QnasReplyDetails;
		$reply_list = $reply->getUpdateReplyDetails(true); 
		if(count($reply_list)==0){
			$reply_list = $reply->getUpdateReplyDetails(false); 
		}
		$att = new Attention;
		$follow = new Follow;
		$likes = new Likes;
		if($reply_list){
			$addition = new ReplyAdditionDetails;
			foreach ($reply_list as $n=>$reply){ 
				if(Cookie::has('userid')){
					$userid = Cookie::get('userid');
					$qna_pending=new QnasPending;
					$qna_follow = $follow->getFollowByQnaIdUserId($reply->qnaid, $userid);
					$qna_like = $likes->getLikeByQnaIdUserId($reply->qnaid, $userid);
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
					if($qna_like){
						$reply_list[$n]['like'] = 1;
					}else{
						$reply_list[$n]['like'] = -1;
					}
					$reply_user_att = $att->getAttentionByUserId($reply->userid, $userid);
					if($reply_user_att){
						$reply_list[$n]['reply_user_att'] = 1;
					}else{
						$reply_list[$n]['reply_user_att'] = -1;
					}
					$qna_user_att = $att->getAttentionByUserId($reply->qna_userid, $userid);
					if($qna_user_att){
						$reply_list[$n]['qna_user_att'] = 1;
					}else{
						$reply_list[$n]['qna_user_att'] = -1;
					}
					$this->assign('userid',$userid);
				}
				$reply->formatCoins = floatval($reply_list[$n]['qna_coins']);
				$follow_count = $follow->getFollowCount($reply->qnaid);
				$reply->followCount = $follow_count->followCount;
				$like_count = $likes->getLikeCount($reply->qnaid);
				$reply->likeCount = $like_count->likeCount;
				$reply->addition = $addition->getReplyAdditions($reply->replyid);
			}
		}
		if(Cookie::has('userid')){
			$user = new Users;
			$user->chkReminder(Cookie::get('userid'));
			$userinfo = $user->getUserInfo(Cookie::get('userid'));
			$this->assign('userinfo',$userinfo);
			$this->assign('header_type','user');
			$this->assign('userid',Cookie::get('userid'));
		}else{
			$this->assign('userid','');
			$this->assign('header_type','normal');
		}
		$this->assign('reply_list',$reply_list);
		if($qna_list){
			foreach ($qna_list as $n=>$qna){ 
				$qna_list[$n]['shortTitle'] = getContentText($qna->title,35);
			}
		}
		$this->assign('qna_list',$qna_list);
		$this->assign('current_page','feature');
		$ads = new Ads;
		$ad1 = $ads->getAdsByPosition(1);
		$ad2 = $ads->getAdsByPosition(2);
		$this->assign('ad1', $ad1); 
		$this->assign('ad2', $ad2); 
        return $this->fetch(); 
	}
}