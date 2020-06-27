<?php
namespace app\mobile\controller;
use think\Controller;
use think\Request;
use think\Db;
use think\Cookie;
use app\mobile\model\ArticlesUser;
use app\mobile\model\ArticleTagDetails;
use app\mobile\model\ArticleFollow;
use app\mobile\model\ArticleLikes;
use app\mobile\model\ArticlesReply;
use app\mobile\model\Articles;
use app\mobile\model\Attention;
use app\mobile\model\Users;
use app\mobile\model\UserTagDetails;
use app\mobile\model\Tags;

class Userarticles extends Controller
{
    public function index()
    {
		if(!Cookie::has('userid')){
			return $this->redirect('/mobile/login');
		}
		$article_user = new ArticlesUser;
		$article_list = $article_user->getArticlesByUserId(Cookie::get('userid')); 
		if($article_list){
			$tags = new ArticleTagDetails;
			$follow = new ArticleFollow;
			$likes = new ArticleLikes;
			$reply = new ArticlesReply;
			foreach ($article_list as $n=>$article){ 
				$tag_list = $tags->getTagsByArticleId($article->articleid);
				$article_list[$n]->tags = $tag_list;
				$article_list[$n]->formatCoins = floatval($article->coins);
				$follow_count = $follow->getFollowCount($article->articleid);
				$article_list[$n]->followCount = $follow_count->followCount;
				$like_count = $likes->getLikeCount($article->articleid);
				$article_list[$n]->likeCount = $like_count->likeCount;
				$reply_count = $reply->getReplyCountByArticleId($article->articleid);
				$article_list[$n]->replyCount = $reply_count->replyCount;
			}
		}
        $this->assign('article_list',$article_list);
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

    public function edit()
    {
		if(!Cookie::has('userid')){
			return $this->redirect('/mobile/login');
		}
		$articleid = Request::instance()->get('articleid');
		if($articleid == ''){
			return $this->redirect('/mobile/userarticles');
		}
		
		$article = new Articles; 
		$article_detail = $article->getArticleDetailsByArticleId($articleid);
		$article_tags = new ArticleTagDetails;
		$tag_list = $article_tags->getTagsByArticleId($articleid);
		$article_detail->tags = $tag_list;
		
		if(!$article_detail){
			return $this->redirect('/mobile/userarticles');
		}
		$this->assign('articleinfo',$article_detail);
		//获取顶级标签
		$tags = new Tags;
		$root_tags = $tags->getRootTags();
		$this->assign('root_tags',$root_tags);
		
        $this->assign('userid', Cookie::get('userid'));
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

	public function delArticle(){
		if(!Cookie::has('userid')){
			return $this->redirect('/mobile/login');
		}
		$articleid = Request::instance()->post('articleid');
		if($articleid != ''){
			$article = new Articles;
			return $article->delArticle(Cookie::get('userid'),$articleid);
		}else{
			return "数据错误";
		}
	}
}