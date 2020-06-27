<?php
namespace app\mobile\controller;
use think\Controller;
use think\Request;
use think\Db;
use think\Cookie;
use app\mobile\model\ArticlesUser;
use app\mobile\model\ArticlesReply;
use app\mobile\model\ArticlesReplyDetails;
use app\mobile\model\Attention;
use app\mobile\model\ArticleFollow;
use app\mobile\model\ArticleLikes;
use app\mobile\controller\Article;
use app\mobile\model\Users;
use app\mobile\model\UserTagDetails;
use app\mobile\model\Ads;

class ArticleReply extends Controller
{
    public function index()
    {
		$replyid = Request::instance()->get('id');
		if($replyid == ''){
			return $this->redirect('/mobile');
		}
		$reply = new ArticlesReplyDetails;
		$reply_detail = $reply->getTopReplyDetailsByReplyId($replyid); 
		$article = new Article;
		$article->addView($reply_detail->articleid);
		if(!$reply_detail){
			return $this->redirect('/mobile');
		}
		$att = new Attention;
		$follow = new ArticleFollow;
		$likes = new ArticleLikes;
		if(Cookie::has('userid')){
			$userid = Cookie::get('userid');
			$article_follow = $follow->getFollowByArticleIdUserId($reply_detail->articleid, $userid);
			$article_like = $likes->getLikeByArticleIdUserId($reply_detail->articleid, $userid);
			$reply_like = $likes->getLikeByArticleIdUserId($reply_detail->replyid, $userid);
			if($article_follow){
				$reply_detail['follow'] = 1;
			}else{
				$reply_detail['follow'] = -1;
			}
			if($article_like){
				$reply_detail['article_like'] = 1;
			}else{
				$reply_detail['article_like'] = -1;
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
			$article_user_att = $att->getAttentionByUserId($reply_detail->article_userid, $userid);
			if($article_user_att){
				$reply_detail['article_user_att'] = 1;
			}else{
				$reply_detail['article_user_att'] = -1;
			}
			$replyTo = $reply->getReplyToDetailsByReplyId($replyid);
			$reply_detail['replyTo']=$replyTo;
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
		$follow_count = $follow->getFollowCount($reply_detail->articleid);
		$reply_detail['followCount'] = $follow_count->followCount;
		$like_count = $likes->getLikeCount($reply_detail->articleid);
		$reply_detail->likeCountArticle = $like_count->likeCount;
		$like_count = $likes->getLikeCount($reply_detail->replyid);
		$reply_detail->likeCountReply = $like_count->likeCount;
		$user_tag = new UserTagDetails;
		$article_tag_list = $user_tag->getTagListByUserId($reply_detail->article_userid);
		$reply_tag_list = $user_tag->getTagListByUserId($reply_detail->userid);
		$this->assign('article_tag_list',$article_tag_list);
		$this->assign('reply_tag_list',$reply_tag_list);
		$userdetail = new Users;
		$article_userinfo = $userdetail->getUserInfo($reply_detail->article_userid);
		$reply_userinfo = $userdetail->getUserInfo($reply_detail->userid);
		$this->assign('article_userinfo',$article_userinfo);
		$this->assign('reply_userinfo',$reply_userinfo);
		$this->assign('reply_detail',$reply_detail);
		$ads = new Ads;
		$ad1 = $ads->getAdsByPosition(1);
		$ad2 = $ads->getAdsByPosition(2);
		$this->assign('ad1', $ad1); 
		$this->assign('ad2', $ad2); 
		$articleuser=new ArticlesUser();
		$article_list=$articleuser->getNewArticles();  
		if($article_list){
			foreach ($article_list as $n=>$article){ 
				$article_list[$n]['shortTitle'] = getContentText($article->title,40);
			}
		}
		$this->assign('list',$article_list);
        return $this->fetch(); 
	}
	
	public function updateReply(){
		if(!Cookie::has('userid')){
			return $this->redirect('/mobile/login');
		}
		$replyid = Request::instance()->post('replyid');
		$replystatus = Request::instance()->post('replystatus');
		if($replystatus == 1 || $replystatus == 2){
			$reply = new ArticlesReply;
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