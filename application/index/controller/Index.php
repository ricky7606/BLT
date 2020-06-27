<?php
namespace app\index\controller;
use think\Controller;//引入Controller类
use think\Session;
use app\index\model\ArticlesUser;
use app\index\model\ArticlesReply;
use app\index\model\ArticlesReplyDetails;
use app\index\model\ArticleFollow;
use app\index\model\ArticleLikes;
use app\index\model\Attention;
use think\Db;
use think\Request;
use think\Cookie;
use Qiniu\Auth as Auth;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;
use app\index\model\Users;
use app\index\model\Tags;
use app\index\model\ArticleTagDetails;

class Index extends Controller
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
					$tag_list = $article_tags->getTagsByArticleId($article->articleid);
					$article_list[$n]['tags'] = $tag_list;
					$reply_count = $reply->getReplyCountByArticleId($article->articleid)->replyCount;
					$article_list[$n]['reply_count'] = $reply_count;
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
		//获取顶级标签
		$tags = new Tags;
		$root_tags = $tags->getRootTags();
		$this->assign('root_tags',$root_tags);
//暂时不需要页面右侧的最新提问列表		
//		$qnauser=new QnasUser();
//		$qna_list=$qnauser->getNewQnas(); 
//		if($qna_list){
//			foreach ($qna_list as $n=>$qna){ 
//				$qna_list[$n]['shortTitle'] = getContentText($qna->title,40);
//			}
//		}
//		$this->assign('qna_list',$qna_list);
        return $this->fetch(); 
	}
	
	public function aboutus(){
		if(Cookie::has('userid')){
			$user = new Users;
			$userid = Cookie::get('userid');
			$user->chkReminder($userid);
			$userinfo = $user->getUserInfo($userid);
			$this->assign('userid',$userid);
			$this->assign('userinfo',$userinfo);
			$this->assign('header_type','user');
		}else{
			$this->assign('userid','');
			$this->assign('header_type','normal');
		}
        return $this->fetch('aboutus'); 
	}

	public function disclaimer(){
		if(Cookie::has('userid')){
			$user = new Users;
			$userid = Cookie::get('userid');
			$user->chkReminder($userid);
			$userinfo = $user->getUserInfo($userid);
			$this->assign('userid',$userid);
			$this->assign('userinfo',$userinfo);
			$this->assign('header_type','user');
		}else{
			$this->assign('userid','');
			$this->assign('header_type','normal');
		}
        return $this->fetch('disclaimer'); 
	}
	
	public function helping(){
		if(Cookie::has('userid')){
			$user = new Users;
			$userid = Cookie::get('userid');
			$user->chkReminder($userid);
			$userinfo = $user->getUserInfo($userid);
			$this->assign('userid',$userid);
			$this->assign('userinfo',$userinfo);
			$this->assign('header_type','user');
		}else{
			$this->assign('userid','');
			$this->assign('header_type','normal');
		}
        return $this->fetch('helping'); 
	}
}