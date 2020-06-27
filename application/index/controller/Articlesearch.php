<?php
namespace app\index\controller;
use think\Controller;//引入Controller类
use think\Session;
use app\index\model\ArticlesUser;
use app\index\model\ArticlesReply;
use app\index\model\ArticleFollow;
use app\index\model\ArticleLikes;
use app\index\model\ArticleTagDetails;
use app\index\model\Attention;
use think\Db;
use think\Request;
use think\Cookie;
use Qiniu\Auth as Auth;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;
use app\index\model\Users;
use app\index\model\Ads;

class Articlesearch extends Controller
{
    public function index()
    {
		$keywords = Request::instance()->get('keywords');
		if($keywords == ''){
			return $this->redirect('/index/articlelist');
		}
		$no_result = false;
		$articleuser=new ArticlesUser();
		$article_list=$articleuser->searchArticles($keywords); 
		if(!$article_list){
			$article_list=$articleuser->getNewArticles(); 
			$no_result = true; 
		}
		$user = new Users;   
		$follow = new ArticleFollow;
		$likes = new ArticleLikes;
		$attention = new Attention;
		$article_tags = new ArticleTagDetails;
		if(Cookie::has('userid')){
			if($article_list){
				foreach ($article_list as $n=>$article){ 
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
					$article_list[$n]['shortTitle'] = getContentText($article->title,40);
					$article_list[$n]['formatCoins'] = floatval($article->coins);
					$article_list[$n]['userinfo'] = $user->getUserDetails($article->userid);
					$follow_count = $follow->getFollowCount($article->articleid);
					$article_list[$n]['followCount'] = $follow_count->followCount;
					$like_count = $likes->getLikeCount($article->articleid);
					$article_list[$n]->likeCountArticle = $like_count->likeCount;
				}
			}
		}
		$this->assign('list',$article_list);
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