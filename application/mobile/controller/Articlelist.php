<?php
namespace app\mobile\controller;
use think\Controller;//引入Controller类
use think\Session;
use app\mobile\model\ArticlesUser;
use app\mobile\model\ArticlesReply;
use app\mobile\model\ArticleFollow;
use app\mobile\model\ArticleLikes;
use app\mobile\model\ArticleTagDetails;
use app\mobile\model\Attention;
use app\mobile\model\Ads;
use think\Db;
use think\Request;
use think\Cookie;
use Qiniu\Auth as Auth;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;
use app\mobile\model\Users;

class Articlelist extends Controller
{
    public function index()
    {
		$articleuser=new ArticlesUser();
		$article_list=$articleuser->getNewArticles();  
		$user = new Users;   
		$follow = new ArticleFollow;
		$likes = new ArticleLikes;
		$attention = new Attention;
		$article_tags = new ArticleTagDetails;
		$reply = new ArticlesReply;
		if(Cookie::has('userid')){
			if($article_list){
				foreach ($article_list as $n=>$article){ 
					$follow_info = $follow->getFollowByArticleIdUserId($article->articleid, Cookie::get('userid'));
					$article_like = $likes->getLikeByArticleIdUserId($article->articleid, Cookie::get('userid'));
					$attention_info = $attention->getAttentionByUserId($article->userid, Cookie::get('userid'));
					$tag_list = $article_tags->getTagsByArticleId($article->articleid);
					$article_list[$n]['tags'] = $tag_list;
					$reply_count = $reply->getReplyCountByArticleId($article->articleid)->replyCount;
					$article_list[$n]['reply_count'] = $reply_count;
					if($follow_info){
						$article_list[$n]['follow'] = '1';
					}else{
						$article_list[$n]['follow'] = '-1';
					}
					if($article_like){
						$article_list[$n]['article_like'] = 1;
					}else{
						$article_list[$n]['article_like'] = -1;
					}
					if($attention_info){
						$article_list[$n]['attention'] = '1';
					}else{
						$article_list[$n]['attention'] = '-1';
					}
					$article_list[$n]['shortTitle'] = getContentText($article->title,40);
					$article_list[$n]['formatCoins'] = floatval($article->coins);
					$article_list[$n]['userinfo'] = $user->getUserDetails($article->userid);
					$follow_count = $follow->getFollowCount($article->articleid);
					$article_list[$n]['followCount'] = $follow_count->followCount;
					$like_count = $likes->getLikeCount($article->articleid);
					$article_list[$n]->likeCount = $like_count->likeCount;
				}
			}
		}else{
			if($article_list){
				foreach ($article_list as $n=>$article){ 
					$reply_count = $reply->getReplyCountByArticleId($article->articleid)->replyCount;
					$article_list[$n]['reply_count'] = $reply_count;
					$tag_list = $article_tags->getTagsByArticleId($article->articleid);
					$article_list[$n]['tags'] = $tag_list;
					$article_list[$n]['shortTitle'] = getContentText($article->title,40);
					$article_list[$n]['formatCoins'] = floatval($article->coins);
					$article_list[$n]['userinfo'] = $user->getUserDetails($article->userid);
					$follow_count = $follow->getFollowCount($article->articleid);
					$article_list[$n]['followCount'] = $follow_count->followCount;
					$like_count = $likes->getLikeCount($article->articleid);
					$article_list[$n]->likeCount = $like_count->likeCount;
				}
			}
		}
		$this->assign('list',$article_list);
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

	public function getArticleListByTagId($tagid){
		if($tagid==''){
			$tagid = Request::instance()->post('tagid');
		}
		$page = Request::instance()->post('page');
		if($tagid != ''){
			$article_list = new ArticleTagDetails;
			$result = $article_list->getArticlesByTagId($tagid,10,$page);
			return $result;
		}else{
			return '';
		}
	}
	
	public function saveReplyArticle(){
		if(Cookie::has('userid')){
			$articleid = Request::instance()->post('articleid');
			$content = Request::instance()->post('content');
			$content_text = Request::instance()->post('content_text');
			$thumb_img = getThumbImg($content);
			$article_reply=new ArticlesReply();
			return $article_reply->saveReplyArticle(Cookie::get('userid'), $content, $content_text, $thumb_img, $articleid);
		}else{
			return $this->redirect('/mobile/login');
		}
	}

	public function ajaxMessage(){
		if(true){
			$articleuser=new ArticlesUser();
			$page = Request::instance()->get('page');
			$article_list=$articleuser->getArticlesByPage($page);  
			$user = new Users;   
			$follow = new ArticleFollow;
			$likes = new ArticleLikes;
			$attention = new Attention;
			$article_tags = new ArticleTagDetails;
			$reply = new ArticlesReply;
			if(Cookie::has('userid')){
			if($article_list){
				foreach ($article_list as $n=>$article){ 
					$reply_count = $reply->getReplyCountByArticleId($article->articleid)->replyCount;
					$article_list[$n]['reply_count'] = $reply_count;
					$follow_info = $follow->getFollowByArticleIdUserId($article->articleid, Cookie::get('userid'));
					$article_like = $likes->getLikeByArticleIdUserId($article->articleid, Cookie::get('userid'));
					$attention_info = $attention->getAttentionByUserId($article->userid, Cookie::get('userid'));
					$tag_list = $article_tags->getTagsByArticleId($article->articleid);
					$article_list[$n]['tags'] = $tag_list;
					if($follow_info){
						$article_list[$n]['follow'] = '1';
					}else{
						$article_list[$n]['follow'] = '-1';
					}
					if($article_like){
						$article_list[$n]['article_like'] = 1;
					}else{
						$article_list[$n]['article_like'] = -1;
					}
					if($attention_info){
						$article_list[$n]['attention'] = '1';
					}else{
						$article_list[$n]['attention'] = '-1';
					}
					$article_list[$n]['shortTitle'] = getContentText($article->title,40);
					$article_list[$n]['formatCoins'] = floatval($article->coins);
					$article_list[$n]['userinfo'] = $user->getUserDetails($article->userid);
					$follow_count = $follow->getFollowCount($article->articleid);
					$article_list[$n]['followCount'] = $follow_count->followCount;
					$like_count = $likes->getLikeCount($article->articleid);
					$article_list[$n]->likeCount = $like_count->likeCount;
				}
			}
			}else{
			if($article_list){
				foreach ($article_list as $n=>$article){ 
					$reply_count = $reply->getReplyCountByArticleId($article->articleid)->replyCount;
					$article_list[$n]['reply_count'] = $reply_count;
					$tag_list = $article_tags->getTagsByArticleId($article->articleid);
					$article_list[$n]['tags'] = $tag_list;
					$article_list[$n]['shortTitle'] = getContentText($article->title,40);
					$article_list[$n]['formatCoins'] = floatval($article->coins);
					$article_list[$n]['userinfo'] = $user->getUserDetails($article->userid);
					$follow_count = $follow->getFollowCount($article->articleid);
					$article_list[$n]['followCount'] = $follow_count->followCount;
					$like_count = $likes->getLikeCount($article->articleid);
					$article_list[$n]->likeCount = $like_count->likeCount;
				}
			}
			}
			$total['list'] = $article_list;

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
		}else{
			return $this->redirect('/mobile/login');
		}
	}
}