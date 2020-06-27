<?php
namespace app\mobile\controller;
use think\Controller;
use think\Request;
use think\Db;
use think\Cookie;
use app\mobile\model\Articles;
use app\mobile\model\ArticlesReply;
use app\mobile\model\ArticlesUser;
use app\mobile\model\ArticlesReplyDetails;
use app\mobile\model\Attention;
use app\mobile\model\ArticleFollow;
use app\mobile\model\ArticleLikes;
use app\mobile\model\Users;
use app\mobile\model\UserTagDetails;
use app\mobile\model\Ads;
use app\mobile\controller\Article;

class ArticleDetails extends Controller
{
    public function index()
    {
		$articleid = Request::instance()->get('id');
		if($articleid == ''){
			return $this->redirect('/');
		}
		
		$article = new Articles; 
		$article_detail = $article->getArticleDetailsByArticleId($articleid);
		if(!$article_detail){
			return $this->redirect('/mobile');
		}
		
		$article_info = new Article;
		$article_info->addView($article_detail->articleid);
		$userid = Cookie::get('userid');
		$this->assign('userid',$userid);
		$att = new Attention;
		$follow = new ArticleFollow;
		$likes = new ArticleLikes;
		if(Cookie::has('userid')){
			$article_follow = $follow->getFollowByArticleIdUserId($article_detail->articleid, $userid);
			$article_like = $likes->getLikeByArticleIdUserId($article_detail->articleid, $userid);
			if($article_follow){
				$article_detail['follow'] = 1;
			}else{
				$article_detail['follow'] = -1;
			}
			if($article_like){
				$article_detail['article_like'] = 1;
			}else{
				$article_detail['article_like'] = -1;
			}
			$article_user_att = $att->getAttentionByUserId($article_detail->userid, $userid);
			if($article_user_att){
				$article_detail['article_user_att'] = 1;
			}else{
				$article_detail['article_user_att'] = -1;
			}
		}
		$follow_count = $follow->getFollowCount($article_detail->articleid);
		$article_detail['followCount'] = $follow_count->followCount;
		$like_count = $likes->getLikeCount($article_detail->articleid);
		$article_detail->likeCountArticle = $like_count->likeCount;
		$this->assign('articleinfo',$article_detail);
		$user = new Users;
		$userinfo = $user->getUserInfo($article_detail->userid);
		$this->assign('article_userinfo',$userinfo);
		$user_tag = new UserTagDetails;
		$article_tag_list = $user_tag->getTagListByUserId($article_detail->userid);
		$this->assign('article_tag_list',$article_tag_list);
		
		$reply = new ArticlesReplyDetails;
		$reply_list = $reply->getReplyDetailsByArticleId($articleid); 
		if($reply_list){
			foreach($reply_list as $n=>$reply){
				$replyTo = $reply->getReplyToDetailsByReplyId($reply->replyid);
				$reply_list[$n]['replyTo'] = $replyTo;
				if(Cookie::has('userid')){
					$article_follow = $follow->getFollowByArticleIdUserId($reply->articleid, $userid);
					$reply_like = $likes->getLikeByArticleIdUserId($reply->replyid, $userid);
					if($article_follow){
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
					//楼主回复是否已点赞
					if($replyTo){
						$replyTo_like = $likes->getLikeByArticleIdUserId($replyTo->replyid, $userid);
						if($replyTo_like){
							$reply_list[$n]['replyTo_like'] = 1;
						}else{
							$reply_list[$n]['replyTo_like'] = -1;
						}
					}
				}
				$like_count = $likes->getLikeCount($reply->replyid);
				$reply->likeCountReply = $like_count->likeCount;
				if($replyTo){
					$replyTo_like_count = $likes->getLikeCount($replyTo->replyid);
					$reply->ReplyTolikeCountReply = $replyTo_like_count->likeCount;
				}
				$reply_userinfo = $user->getUserInfo($reply->userid);
				$reply->reply_userinfo = $reply_userinfo;
			}
		}
		$this->assign('reply_list',$reply_list);
		$articleuser=new ArticlesUser();
		$article_list=$articleuser->getNewArticles(); 
		if($article_list){
			foreach ($article_list as $n=>$article){ 
				$article->shortTitle = getContentText($article->title,35);
			}
		}
		$this->assign('article_list',$article_list);
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