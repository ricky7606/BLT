<?php
namespace app\mobile\controller;
use think\Controller;//引入Controller类
use think\Session;
use app\mobile\model\QnasUser;
use app\mobile\model\QnasPending;
use app\mobile\model\QnasReply;
use app\mobile\model\Follow;
use app\mobile\model\Likes;
use app\mobile\model\Attention;
use app\mobile\model\Ads;
use think\Db;
use think\Request;
use think\Cookie;
use Qiniu\Auth as Auth;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;
use app\mobile\model\Users;

class Qnalist extends Controller
{
    public function index()
    {
		$qnauser=new QnasUser();
		$qna_list=$qnauser->getNewQnas();  
		$user = new Users;   
		$follow = new Follow;
		$likes = new Likes;
		$attention = new Attention;
		if(Cookie::has('userid')){
			$qna_pending=new QnasPending;
			if($qna_list){
				foreach ($qna_list as $n=>$qna){ 
					$pending_info = $qna_pending->getPendingByUserId($qna->qnaid, Cookie::get('userid'));
					$follow_info = $follow->getFollowByQnaIdUserId($qna->qnaid, Cookie::get('userid'));
					$qna_like = $likes->getLikeByQnaIdUserId($qna->qnaid, Cookie::get('userid'));
					$attention_info = $attention->getAttentionByUserId($qna->userid, Cookie::get('userid'));
					if($pending_info){
						$qna_list[$n]['pending_status'] = $pending_info->status;
						$qna_list[$n]['pendingid'] = $pending_info->pendingid;
					}else{
						$qna_list[$n]['pending_status'] = '-1';
						$qna_list[$n]['pendingid'] = '';
					}
					if($follow_info){
						$qna_list[$n]['follow'] = '1';
					}else{
						$qna_list[$n]['follow'] = '-1';
					}
					if($qna_like){
						$qna_list[$n]['qna_like'] = 1;
					}else{
						$qna_list[$n]['qna_like'] = -1;
					}
					if($attention_info){
						$qna_list[$n]['attention'] = '1';
					}else{
						$qna_list[$n]['attention'] = '-1';
					}
					$qna_list[$n]['shortTitle'] = getContentText($qna->title,40);
					$qna_list[$n]['formatCoins'] = floatval($qna->coins);
					$qna_list[$n]['userinfo'] = $user->getUserDetails($qna->userid);
					$follow_count = $follow->getFollowCount($qna->qnaid);
					$qna_list[$n]['followCount'] = $follow_count->followCount;
					$like_count = $likes->getLikeCount($qna->qnaid);
					$qna_list[$n]->likeCountQna = $like_count->likeCount;
				}
			}
		}else{
			if($qna_list){
				foreach ($qna_list as $n=>$qna){ 
					$qna_list[$n]['shortTitle'] = getContentText($qna->title,40);
					$qna_list[$n]['formatCoins'] = floatval($qna->coins);
					$qna_list[$n]['userinfo'] = $user->getUserDetails($qna->userid);
					$follow_count = $follow->getFollowCount($qna->qnaid);
					$qna_list[$n]['followCount'] = $follow_count->followCount;
					$like_count = $likes->getLikeCount($qna->qnaid);
					$qna_list[$n]->likeCountQna = $like_count->likeCount;
				}
			}
		}
		$this->assign('list',$qna_list);
		$this->assign('userid',Cookie::get('userid'));
		if(Cookie::has('userid')){
			$this->assign('header_type', 'user');
			$this->assign('userid', Cookie::get('userid'));
			$user = new Users;
			$user->chkReminder(Cookie::get('userid'));
			$userinfo = $user->getUserInfo(Cookie::get('userid'));
			$this->assign('userinfo',$userinfo);
		}else{
			$this->assign('header_type', 'normal'); 
		}
		$ads = new Ads;
		$ad1 = $ads->getAdsByPosition(1);
		$ad2 = $ads->getAdsByPosition(2);
		$this->assign('ad1', $ad1); 
		$this->assign('ad2', $ad2); 
        return $this->fetch();
    }
	
	public function saveApplyQna(){
		if(Cookie::has('userid')){
			$qnaid = Request::instance()->post('qnaid');
			$qna_pending=new QnasPending();
			return $qna_pending->saveApply($qnaid, Cookie::get('userid'));
		}else{
			return $this->redirect('/mobile/login');
		}
	}

	public function saveReplyQna(){
		if(Cookie::has('userid')){
			$qnaid = Request::instance()->post('qnaid');
			$pendingid = Request::instance()->post('pendingid');
			$content = Request::instance()->post('content');
			$content_text = Request::instance()->post('content_text');
			$thumb_img = getThumbImg($content);
			$qna_reply=new QnasReply();
			return $qna_reply->saveReplyQna(Cookie::get('userid'), $content, $content_text, $thumb_img, $qnaid, $pendingid);
		}else{
			return $this->redirect('/mobile/login');
		}
	}

	public function ajaxMessage(){
		if(true){
			$qnauser=new QnasUser();
			$page = Request::instance()->get('page');
			$qna_list=$qnauser->getQnasByPage($page);  
			$user = new Users;   
			$follow = new Follow;
			$likes = new Likes;
			$attention = new Attention;
			if(Cookie::has('userid')){
				$qna_pending=new QnasPending;
				if($qna_list){
					foreach ($qna_list as $n=>$qna){ 
						$pending_info = $qna_pending->getPendingByUserId($qna->qnaid, Cookie::get('userid'));
						$follow_info = $follow->getFollowByQnaIdUserId($qna->qnaid, Cookie::get('userid'));
						$qna_like = $likes->getLikeByQnaIdUserId($qna->qnaid, Cookie::get('userid'));
						$attention_info = $attention->getAttentionByUserId($qna->userid, Cookie::get('userid'));
						if($pending_info){
							$qna_list[$n]['pending_status'] = $pending_info->status;
							$qna_list[$n]['pendingid'] = $pending_info->pendingid;
						}else{
							$qna_list[$n]['pending_status'] = '-1';
							$qna_list[$n]['pendingid'] = '';
						}
						if($follow_info){
							$qna_list[$n]['follow'] = '1';
						}else{
							$qna_list[$n]['follow'] = '-1';
						}
						if($qna_like){
							$qna_list[$n]['qna_like'] = 1;
						}else{
							$qna_list[$n]['qna_like'] = -1;
						}
						if($attention_info){
							$qna_list[$n]['attention'] = '1';
						}else{
							$qna_list[$n]['attention'] = '-1';
						}
						$qna_list[$n]['shortTitle'] = getContentText($qna->title,40);
						$qna_list[$n]['formatCoins'] = floatval($qna->coins);
						$qna_list[$n]['userinfo'] = $user->getUserDetails($qna->userid);
						$follow_count = $follow->getFollowCount($qna->qnaid);
						$qna_list[$n]['followCount'] = $follow_count->followCount;
						$like_count = $likes->getLikeCount($qna->qnaid);
						$qna_list[$n]->likeCountQna = $like_count->likeCount;
					}
				}
			}else{
				if($qna_list){
					foreach ($qna_list as $n=>$qna){ 
						$qna_list[$n]['shortTitle'] = getContentText($qna->title,40);
						$qna_list[$n]['formatCoins'] = floatval($qna->coins);
						$qna_list[$n]['userinfo'] = $user->getUserDetails($qna->userid);
						$follow_count = $follow->getFollowCount($qna->qnaid);
						$qna_list[$n]['followCount'] = $follow_count->followCount;
						$like_count = $likes->getLikeCount($qna->qnaid);
						$qna_list[$n]->likeCountQna = $like_count->likeCount;
					}
				}
			}
			$total['list'] = $qna_list;

			if(Cookie::has('userid')){
				$metadata['header_type'] = 'user';
				$metadata['selfuserid'] = Cookie::get('userid');
				$user = new Users;
				$user->chkReminder(Cookie::get('userid'));
				$userinfo = $user->getUserInfo(Cookie::get('userid'));
				$metadata['selfuserinfo'] =$userinfo;
				$metadata['page'] = $page;
			}else{
				$metadata['header_type'] = 'normal'; 
				$metadata['page'] = $page;
			}
			$total['meta'] = $metadata;
			return $total;
			$this->assign('list',$qna_list);
			$this->assign('userid',Cookie::get('userid'));
			if(Cookie::has('userid')){
				$this->assign('header_type', 'user');
				$this->assign('userid', Cookie::get('userid'));
				$user = new Users;
				$user->chkReminder(Cookie::get('userid'));
				$userinfo = $user->getUserInfo(Cookie::get('userid'));
				$this->assign('userinfo',$userinfo);
			}else{
				$this->assign('header_type', 'normal'); 
			}
			$ads = new Ads;
			$ad1 = $ads->getAdsByPosition(1);
			$ad2 = $ads->getAdsByPosition(2);
			$this->assign('ad1', $ad1); 
			$this->assign('ad2', $ad2); 
	        return $this->fetch();
		}else{
			return $this->redirect('/mobile/login');
		}
	}
}