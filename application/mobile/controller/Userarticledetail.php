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
use app\mobile\model\Users;
use app\mobile\model\UserTagDetails;
use app\mobile\model\Ads;

class UserArticleDetail extends Controller
{
    public function index()
    {
		$userid = Request::instance()->get('userid');
		if($userid == ''){
			return $this->redirect('/index');
		}

		$user = new Users;
		$userdetail = $user->getUserInfo($userid);
		if(!$userdetail){
			return $this->redirect('/index');
		}
		$user_att = "";
		if(Cookie::has('userid')){
			$login_userid = Cookie::get('userid');
			$att = new Attention;
			$user_att = $att->getAttentionByUserId($userdetail->userid, $login_userid);
			if($user_att){
				$user_att = 1;
			}else{
				$user_att = -1;
			}
		}
		$this->assign('userdetail',$userdetail);
		$this->assign('user_att',$user_att);

		$user_tag = new UserTagDetails;
		$user_tag_list = $user_tag->getTagListByUserId($userid);
		$this->assign('user_tag_list',$user_tag_list);
		
		$reply = new ArticlesReplyDetails;
		$reply_list = $reply->getUpdateReplyDetailsByUserId($userid);
		foreach($reply_list as $n=>$reply){
			$reply_list[$n]['shortTitle'] = getContentText($reply->title,36);
		}
		$this->assign('reply_list',$reply_list);

		$article = new ArticlesUser;
		$article_list = $article->getArticlesByUserId($userid);
		if($article_list){
			foreach($article_list as $n=>$article){
				if(Cookie::has('userid')){
					$follow = new ArticleFollow;
					$article_follow = $follow->getFollowByArticleIdUserId($article->articleid, $login_userid);
					if($article_follow){
						$article_list[$n]['follow'] = 1;
					}else{
						$article_list[$n]['follow'] = -1;
					}
				}
				$article_list[$n]['formatCoins'] = floatval($article->coins);
			}
		}
		$this->assign('article_list',$article_list);

		if(Cookie::has('userid')){
			$this->assign('header_type','user');
			$user->chkReminder($login_userid);
			$userinfo = $user->getUserInfo($login_userid);
			$this->assign('userinfo',$userinfo);
			$this->assign('login_userid',$login_userid);
		}else{
			$this->assign('header_type','normal');
			$this->assign('login_userid','');
		}
		$ads = new Ads;
		$ad1 = $ads->getAdsByPosition(1);
		$ad2 = $ads->getAdsByPosition(2);
		$this->assign('ad1', $ad1); 
		$this->assign('ad2', $ad2); 
        return $this->fetch(); 
	}
}