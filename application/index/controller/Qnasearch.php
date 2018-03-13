<?php
namespace app\index\controller;
use think\Controller;//引入Controller类
use think\Session;
use app\index\model\QnasUser;
use app\index\model\QnasPending;
use app\index\model\QnasReply;
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
use app\index\model\Ads;

class Qnasearch extends Controller
{
    public function index()
    {
		$keywords = Request::instance()->get('keywords');
		if($keywords == ''){
			return $this->redirect('/index/qnalist');
		}
		$no_result = false;
		$qnauser=new QnasUser();
		$qna_list=$qnauser->searchQnas($keywords); 
		if(!$qna_list){
			$qna_list=$qnauser->getNewQnas(); 
			$no_result = true; 
		}
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
		$this->assign('no_result',$no_result);
		if(Cookie::has('userid')){
			$this->assign('header_type', 'user');
			$user->chkReminder(Cookie::get('userid'));
			$userinfo = $user->getUserInfo(Cookie::get('userid'));
			$this->assign('userinfo',$userinfo);
		}else{
			$this->assign('header_type', 'normal'); 
		}
		$this->assign('userid', Cookie::get('userid'));
		$ads = new Ads;
		$ad1 = $ads->getAdsByPosition(1);
		$ad2 = $ads->getAdsByPosition(2);
		$this->assign('ad1', $ad1); 
		$this->assign('ad2', $ad2); 
        return $this->fetch();
    }
}