<?php
namespace app\mobile\model;

//导入模型类
use think\Model;
use app\mobile\model\Transactions;
use app\mobile\model\Users;
use app\mobile\model\Message;
use app\mobile\model\ArticleTags;
use app\mobile\model\UsersTags;

class Articles extends Model {
	
	// 设置当前模型对应的完整数据表名称
    protected $table = 'BLT_article';

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
	public function getNewArticles(){
        $new_articles = $this->with('users')->limit(10)
		->where('deleteFlag', 0)
		->order('create_date','desc')
		->select();          // 查询所有用户的所有字段资料
        if (empty($new_articles)) {                 // 判断是否出错
            return false;
        }
        return $new_articles;   // 返回修改后的数据
	}
	
	public function saveNewArticles($userid, $title, $content, $content_text, $title_img, $tag_list){
		$error_flag = false;
		
		//开启事务
		$this->startTrans();
		$this->userid = $userid;
		$this->title = $title;
		$this->content = $content;
		$this->content_text = $content_text;
		$this->title_img = $title_img;
		$this->create_date = date('Y-m-d H:i:s',time());
		$new_articleid = uuid();
		$this->articleid = $new_articleid;
		$result_article = $this->save();
		
		if(!$result_article){
			$error_flag = true;
		}
		
		$tags = new ArticleTags;
		$user_tag = new UsersTags;
		$tagArr = explode(',',$tag_list);
		foreach($tagArr as $tagid){
			$tags_result = $tags->saveArticleTags($new_articleid, $tagid);
			$user_tag->addTag($userid, $tagid);
			if($tags_result === false){
				$error_flag = true;
			}
		}
		
		if(!$error_flag){
			$this->commit();
			return "ok";
		}else{
			$this->rollBack();
			return "error";
		}
	}

	public function updateArticles($articleid, $userid, $title, $content, $content_text, $title_img, $tag_list){
		$error_flag = false;
		
		//开启事务
		$this->startTrans();
		$this->articleid = $articleid;
		$this->title = $title;
		$this->content = $content;
		$this->content_text = $content_text;
		if($title_img!=''){
			$this->title_img = $title_img;
		}
		$this->create_date = date('Y-m-d H:i:s',time());
		$result_article = $this->isUpdate(true)->save();
		
		if(!$result_article){
			$error_flag = true;
		}
		
		//更新发布的标签
		$tags = new ArticleTags;
		$tags->deleteArticleTags($articleid);
		$user_tag = new UsersTags;
		$tagArr = explode(',',$tag_list);
		foreach($tagArr as $tagid){
			$tags_result = $tags->saveArticleTags($articleid, $tagid);
			$user_tag->addTag($userid, $tagid);
			if($tags_result === false){
				$error_flag = true;
			}
		}
		
		if(!$error_flag){
			$this->commit();
			return "ok";
		}else{
			$this->rollBack();
			return "error";
		}
	}

	public function getArticleDetailsByArticleId($articleid){
        $article = $this->where('articleid',$articleid)
		->where('report_disabled', 0)
		->where('deleteFlag', 0)
		->field('articleid,userid,title,content,content_text,title_img,status,view_count,view_coin_status')
		->limit(1)
		->find();          // 查询用户
        if (empty($article)) {                 // 判断是否出错
            return false;
        }
        return $article;   // 返回修改后的数据
	}
	
	public function addView($articleid){
		$result = $this->where('articleid',$articleid)
		->setInc('view_count');
		$article_info = $this->getArticleDetailsByArticleId($articleid);
		if($article_info->view_coin_status<3){
			$message = new Message;
			if($article_info->view_count>=100 && $article_info->view_coin_status==0){
				$this->update(['articleid' => $articleid, 'view_coin_status' => 1, 'coins' => 1]);
				$transaction = new Transactions;
				$result_trans = $transaction->saveTransaction($article_info->userid, 1, 17, $article_info->articleid);
				$message_text = "您发布的 “<a href='articledetails?id=".$article_info->articleid."' target='article'>".$article_info->title."</a>” 已经达到了100次以上的阅读量，系统奖励给您1个比邻币，希望您继续发布高质量的内容。";
				$message = new Message;
				$message_result = $message->saveNewMessage($article_info->userid, $message_text);
			}
			//强制暂停一点时间以确保交易记录先后发生
			usleep(10);
			if($article_info->view_count>=10000 && $article_info->view_coin_status<2){
				$this->update(['articleid' => $articleid, 'view_coin_status' => 2, 'coins' => 11]);
				$transaction = new Transactions;
				$result_trans = $transaction->saveTransaction($article_info->userid, 10, 17, $article_info->articleid);
				$message_text = "您发布的 “<a href='articledetails?id=".$article_info->articleid."' target='article'>".$article_info->title."</a>” 已经达到了10000次以上的阅读量，系统奖励给您10个比邻币，希望您继续发布高质量的内容。";
				$message = new Message;
				$message_result = $message->saveNewMessage($article_info->userid, $message_text);
			}
			//强制暂停一点时间以确保交易记录先后发生
			usleep(10);
			if($article_info->view_count>=1000000 && $article_info->view_coin_status<3){
				$this->update(['articleid' => $articleid, 'view_coin_status' => 3, 'coins' => 111]);
				$transaction = new Transactions;
				$result_trans = $transaction->saveTransaction($article_info->userid, 100, 17, $article_info->articleid);
				$message_text = "您发布的 “<a href='articledetails?id=".$article_info->articleid."' target='article'>".$article_info->title."</a>” 已经达到了1000000次以上的阅读量，系统奖励给您100个比邻币，希望您继续发布高质量的内容。";
				$message = new Message;
				$message_result = $message->saveNewMessage($article_info->userid, $message_text);
			}
		}
		if($result === false){
			return false;
		}else{
			return true;
		}
	}

	public function delArticle($userid, $articleid){
		$result_article = $this->where('userid', $userid)->where('articleid', $articleid)->update(['deleteFlag' => 1]);
        if (empty($result_article)) {                 // 判断是否出错
            return false;
        }
        return "ok";   
	}
	
	public function users()
    {
        return $this->belongsTo('Users','userid');
    }
}