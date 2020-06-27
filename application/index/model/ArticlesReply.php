<?php
namespace app\index\model;

//导入模型类
use think\Model;
use app\index\model\ArticlesReplyDetails;
use app\index\model\Transactions;
use app\index\model\Users;
use app\index\model\Articles;

class ArticlesReply extends Model {
	
	// 设置当前模型对应的完整数据表名称
    protected $table = 'BLT_article_reply';

	//在子类重写父类的初始化方法initialize()
	protected function initialize(){
		
	  //继承父类中的initialize()
		parent::initialize();
		
	   //初始化数据表字段信息	
	   $this->field = $this->db()->getTableInfo('', 'fields');  
	
	   //初始化数据表字段类型
	   $this->type = $this->db()->getTableInfo('', 'type'); 
	
	   //初始化数据表主键
	   $this->pk = $this->db()->getTableInfo('', 'pk');     
		
		
		}

	public function getNewReply(){
        $new_articles = $this->with('users')->limit(10)
		->order('create_date','desc')
		->select();          // 查询所有用户的所有字段资料
        if (empty($new_articles)) {                 // 判断是否出错
            return false;
        }
        return $new_articles;   // 返回修改后的数据
	}
	
	public function saveReplyArticle($userid, $content, $content_text, $thumb_img, $articleid, $replyid){
		//开启事务
		$this->startTrans();
		$error_flag = false;

		$article = new Articles;
		$articleinfo = $article->getArticleDetailsByArticleId($articleid);

		$this->userid = $userid;
		$this->content = $content;
		$this->content_text = $content_text;
		$this->thumb_img = $thumb_img;
		$this->create_date = date('Y-m-d H:i:s',time());
		$new_replyid = uuid();
		$this->replyid = $new_replyid;
		if($articleinfo->userid == $userid){
			$this->reply_to_id = $replyid;
		}
		$this->articleid = $articleid;
		$result = $this->save();
		if($result === false){
			$error_flag = true;
		}
		
		if($articleinfo->userid != $userid){
			$user = new Users;
			$userinfo = $user->getUserDetails($userid);
			$message_text = "用户 “<a href=\"\\index\\userreplydetail?userid=".$userid."\" target=\"_blank\">".$userinfo->username."</a>” 刚刚评论了您的发布： “<a href=\"\\index\\articledetails?id=".$articleid."\" target=\"_blank\">".$articleinfo->title."</a>” 。";
			$message = new Message;
			$result_message = $message->saveNewMessage($articleinfo->userid, $message_text);
		}
		
		if($error_flag){
			$this->rollBack();
			return "error";
		}else{
			$user = new Users;
			$user->chkReminder($userid);
			$this->commit();
			return "ok";
		}
	}

	public function getReplyDetailsByReplyId($replyid){
        $reply = $this->where('replyid',$replyid)
		->field('replyid,articleid,userid,content,content_text,thumb_img')
		->find();          // 查询用户
        if (empty($reply)) {                 // 判断是否出错
            return false;
        }
        return $reply;   // 返回修改后的数据
	}

	public function getReplyCountByArticleId($articleid){
        $reply = $this->where('articleid',$articleid)
		->field('count(*) as replyCount')
		->find();          // 查询用户
        if (empty($reply)) {                 // 判断是否出错
            return false;
        }
        return $reply;   // 返回修改后的数据
	}

	public function users()
    {
        return $this->belongsTo('Users','userid');
    }
}