<?php
namespace app\mobile\controller;
use think\Controller;
use think\Request;
use think\Cookie;
use think\Session;
use Qiniu\Auth as Auth;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;
use app\mobile\model\Articles;
use app\mobile\model\ArticlesReply;
use app\mobile\model\Users;
use app\mobile\model\ArticleFollow;
use app\mobile\model\ArticleLikes;
use app\mobile\model\ArticleReport;
use app\mobile\model\Tags;

class Article extends Controller
{
    public function index()
    {
		if(!Cookie::has('userid')){
			return $this->redirect('/mobile/login');
		}
		$user = new Users;
		$user->chkReminder(Cookie::get('userid'));
		$rand_users = $user->getRandomUsers(6,Cookie::get('userid'));
        $this->assign('rand_users',$rand_users);
		$att_users = $user->getAttUsers(Cookie::get('userid'));
        $this->assign('att_users',$att_users);
		$userinfo = $user->getUserInfo(Cookie::get('userid'));
		
		$coins = floatval($userinfo->coins);
		$frozen_coins = floatval($userinfo->frozen_coins);
		$this->assign('userinfo', $userinfo);
		$this->assign('coins', $coins);
		$this->assign('frozen_coins', $frozen_coins);
        $this->assign('userid', Cookie::get('userid'));
        $this->assign('header_type', 'user');
		//获取顶级标签
		$tags = new Tags;
		$root_tags = $tags->getRootTags();
		$this->assign('root_tags',$root_tags);
		return $this->fetch();
	}

	public function saveArticle()
	{
		if(!Cookie::has('userid')){
			return $this->redirect('/mobile/login');
		}
		$userid = Cookie::get('userid');
		$title = Request::instance()->post('title');
		$content = Request::instance()->post('content');
		$content_text = Request::instance()->post('content_text');
		$title_img = Request::instance()->post('title_img');
		$tag_list = Request::instance()->post('tag_list');
		$content_text = getContentText($content_text,540);
		if($title!="" && $title_img!="" && $tag_list!=""){
			$article = new Articles;
			return $article->saveNewArticles($userid, $title, $content, $content_text, $title_img, $tag_list);
		}else{
			return "数据有误，请检查后重试";
		}
	}

	public function updateArticle()
	{
		if(!Cookie::has('userid')){
			return $this->redirect('/mobile/login');
		}
		$userid = Cookie::get('userid');
		$articleid = Request::instance()->post('articleid');
		$title = Request::instance()->post('title');
		$content = Request::instance()->post('content');
		$content_text = Request::instance()->post('content_text');
		$title_img = Request::instance()->post('title_img');
		$tag_list = Request::instance()->post('tag_list');
		$content_text = getContentText($content_text,540);
		if($title!="" && $tag_list!=""){
			$article = new Articles;
			return $article->updateArticles($articleid, $userid, $title, $content, $content_text, $title_img, $tag_list);
		}else{
			return "数据有误，请检查后重试";
		}
	}
	
	public function saveReplyArticle()
	{
		if(!Cookie::has('userid')){
			return $this->redirect('/mobile/login');
		}
		$userid = Cookie::get('userid');
		$articleid = Request::instance()->post('articleid');
		$replyid = Request::instance()->post('replyid');
		$content = Request::instance()->post('content');
		$content_text = Request::instance()->post('content_text');
		$thumb_img = getThumbImg($content);
		$content_text = getContentText($content_text,500);
		if($content!=""){
			$article = new ArticlesReply;
			return $article->saveReplyArticle($userid, $content, $content_text, $thumb_img, $articleid, $replyid);
		}else{
			return "数据有误，请检查后重试";
		}
	}
	
	//上传照片到七牛云
    public function uploadPic()
    {
		require_once APP_PATH . '/../vendor/qiniusdk/autoload.php';
		
		//获取文件后缀  
		function getExt($file) {  
		$tmp = explode('.',$file);  
		return strtolower(end($tmp));  
		}  
		//随机随机文件名  
		function randName() {  
		$str = 'abcdefghijkmnpqrstwxyz23456789';  
		return substr(str_shuffle($str),0,16);  
		}  

		$accessKey = 'Z4wgnwBl_94hiUth4VEgUiVaj0KQntxR7ZNGR19d';  
		$secretKey = 'SL_tEi0tauni6lvsFD894L62HlrGjTk7Qny8mQh5';  
		$bucket = 'images';  
		// 构建鉴权对象
		$auth = new Auth($accessKey, $secretKey);
		// 生成上传 Token
		$token = $auth->uploadToken($bucket);

		$name=$_FILES['upFiles']['name'];
		$filePath=$_FILES['upFiles']['tmp_name'];
		
		// 初始化 UploadManager 对象并进行文件的上传。
		$uploadMgr = new UploadManager();

		foreach ($name as $k=>$names){
			$type = strtolower(substr($names,strrpos($names,'.')+1));//得到文件类型，并且都转化成小写
			$allow_type = array('jpg','jpeg','gif','png','bmp'); //定义允许上传的类型
			//把非法格式的图片去除
			if (!in_array($type,$allow_type)){
				unset($name[$k]);
			}
		}
		$tmpStr2="{
	\"errno\": 0,
	\"data\": [";
		$tmpStr="";
		foreach ($name as $k=>$item){
			$type = getExt($item);//得到文件类型
			$newname = randName().".".$type;  //新文件名
			// 调用 UploadManager 的 putFile 方法进行文件的上传。
			list($ret, $err) = $uploadMgr->putFile($token, $newname, $filePath[$k]);
			if($tmpStr==""){
				$tmpStr="\r\n	\"http://images.beelintown.com.cn/$newname-upload_pic\"";
			}else{
				$tmpStr.=",\r\n	\"http://images.beelintown.com.cn/$newname-upload_pic\"";
			}
		}
		$tmpStr2.=$tmpStr;
		$tmpStr2.="
		]
	}"; 
//		$tmpStr = 'http://images.beelintown.com.cn/afesq7c2bxwdh8jn.jpg';
//		$tmpArr = array($tmpStr);
//		$tmpArr2 = array('errno'=>0,'data'=>$tmpStr);
//		$tmpStr2 = json_encode($tmpArr2,JSON_UNESCAPED_SLASHES);
//		$result = json_decode($tmpStr2);
//		$tmpStr2 = json_encode($result,JSON_UNESCAPED_SLASHES);
		
		return($tmpStr2);
    }

	//上传照片到七牛云
    public function uploadTitlePic()
    {
		require_once APP_PATH . '/../vendor/qiniusdk/autoload.php';
		
		//获取文件后缀  
		function getExt($file) {  
		$tmp = explode('.',$file);  
		return strtolower(end($tmp));  
		}  
		//随机随机文件名  
		function randName() {  
		$str = 'abcdefghijkmnpqrstwxyz23456789';  
		return substr(str_shuffle($str),0,16);  
		}  

		$accessKey = 'Z4wgnwBl_94hiUth4VEgUiVaj0KQntxR7ZNGR19d';  
		$secretKey = 'SL_tEi0tauni6lvsFD894L62HlrGjTk7Qny8mQh5';  
		$bucket = 'images';  
		// 构建鉴权对象
		$auth = new Auth($accessKey, $secretKey);
		// 生成上传 Token
		$token = $auth->uploadToken($bucket);

		$name=$_FILES['upFiles']['name'];
		$filePath=$_FILES['upFiles']['tmp_name'];
		
		// 初始化 UploadManager 对象并进行文件的上传。
		$uploadMgr = new UploadManager();

		$type = strtolower(substr($name,strrpos($name,'.')+1));//得到文件类型，并且都转化成小写
		$allow_type = array('jpg','jpeg','gif','png','bmp'); //定义允许上传的类型
		//把非法格式的图片去除
		if (!in_array($type,$allow_type)){
			return false;;
		}
		$newname = randName().".".$type;  //新文件名
		// 调用 UploadManager 的 putFile 方法进行文件的上传。
		list($ret, $err) = $uploadMgr->putFile($token, $newname, $filePath);
		$tmpStr="http://images.beelintown.com.cn/$newname";
		$tmpStr .= "-article_title";

		return $tmpStr;
	}

	public function getRandUsers($limit = 6){
		$userid = Request::instance()->post('userid');
		$limit = Request::instance()->post('limit');
		$users = new Users;
		$rand_users = $users->getRandomUsers($limit, $userid);
		return collection($rand_users);
	}
	
	public function getSearchUser(){
		$userid = Request::instance()->post('userid');
		$username = Request::instance()->post('username');
		$users = new Users;
		$search_user = $users->getSearchUser($username, $userid);
		return $search_user;
	}
	
	public function follow(){
		if(!Cookie::has('userid')){
			return $this->redirect('/mobile/login');
		}
		$userid = Cookie::get('userid');
		$articleid = Request::instance()->post('articleid');
		if($articleid != ''){
			$follow = new ArticleFollow;
			$result = $follow->saveNewFollow($userid, $articleid);
			return $result;
		}else{
			return "数据错误";
		}
	}

	public function like(){
		if(!Cookie::has('userid')){
			return $this->redirect('/mobile/login');
		}
		$userid = Cookie::get('userid');
		$articleid = Request::instance()->post('articleid');
		if($articleid != ''){
			$likes = new ArticleLikes;
			$result = $likes->saveLike($userid, $articleid);
			return $result;
		}else{
			return "数据错误";
		}
	}
	
	public function report(){
		if(!Cookie::has('userid')){
			return $this->redirect('/mobile/login');
		}
		session_start();
		if(!empty($_SESSION['report_time'])){
			$current_time = date('Y-m-d H:i:s',time());
			$minute=floor((strtotime($current_time)-strtotime($_SESSION['report_time']))%86400/60);
			if($minute<10){
				return "请勿频繁举报";
			}
		}
		$userid = Cookie::get('userid'); 
		$articleid = Request::instance()->post('articleid');
		$report_type = Request::instance()->post('report_type');
		$article_type = Request::instance()->post('article_type');
		$report_comment = Request::instance()->post('report_comment');
		if($articleid == '' || $report_type == '' || $article_type == ''){
			return "数据错误";
		}
		$report = new ArticleReport();
		$result = $report->saveReport($userid, $articleid, $report_type, $article_type, $report_comment);
		if($result == "ok"){
			$_SESSION['report_time'] = date('Y-m-d H:i:s',time());
		}
		return $result;
	}
	
	public function addView($articleid=''){
		if($articleid==''){
			$articleid = Request::instance()->post('articleid');
		}
		session_start();
		if(!empty($_SESSION['view_'.$articleid.'_time'])){
			$current_time = date('Y-m-d H:i:s',time());
			$minute=floor((strtotime($current_time)-strtotime($_SESSION['view_'.$articleid.'_time']))%86400/60);
			if($minute<5){
				return false;
			}
		}
		$article = new Articles;
		$result = $article->addView($articleid);
		if($result){
			$_SESSION['view_'.$articleid.'_time'] = date('Y-m-d H:i:s',time());
		}
		return $result;
	}
}