<?php
namespace app\mobile\model;

//导入模型类
use think\Model;
use app\mobile\model\Transactions;
use app\mobile\model\Users;

class ArticlesReplyDetails extends Model {
	
	// 设置当前模型对应的完整数据表名称
    protected $table = 'view_ArticleReplyDetails';

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
	
	public function getReplyDetailsByUserId($userid){
        $new_reply = $this->where('userid', $userid)
		->where('reply_report_disabled', 0)
		->where('article_report_disabled', 0)
		->order('create_date','desc')
		->select();          // 查询所有用户的所有字段资料
        if (empty($new_reply)) {                 // 判断是否出错
            return false;
        }
        return $new_reply;   // 返回修改后的数据
	}

	public function getReplyDetails($limit = NULL){
		if($limit == NULL){
			$limit = 100000;
		}
		$reply = $this->limit($limit)
		->order('create_date','desc')
		->paginate(10);          // 查询所有用户的所有字段资料
        if (empty($reply)) {                 // 判断是否出错
            return false;
        }
        return $reply;   // 返回修改后的数据
	}

	public function getReplyDetailsByArticleId($articleid, $limit = NULL){
		if($limit == NULL){
			$limit = 100000;
		}
		$reply = $this->where('articleid',$articleid)
		->where('reply_report_disabled',0)
		->where('reply_to_id', 'NULL')
		->limit($limit)
		->order('create_date','desc')
		->paginate(5,false,['query'=>request()->param()]);          
        if (empty($reply)) {                 // 判断是否出错
            return false;
        }
        return $reply;   // 返回修改后的数据
	}

	public function getReplyDetailsByArticleIdUserId($articleid, $userid){
		$reply = $this->where('articleid', $articleid)
		->where('userid', $userid)
		->order('create_date','asc')
		->select();
        if (empty($reply)) {                 // 判断是否出错
            return false;
        }
        return $reply;   // 返回修改后的数据
	}

	public function getTopReplyDetailsByReplyId($replyid){
		$reply = $this->where('replyid',$replyid)
		->limit(1)
		->order('create_date','desc')
		->find();          // 查询所有用户的所有字段资料
        if (empty($reply)) {                 // 判断是否出错
            return false;
        }
        return $reply;   // 返回修改后的数据
	}

	public function getReplyDetailsByReplyId($replyid){
        $reply = $this->where('replyid',$replyid)
		->where('reply_report_disabled', 0)
		->find();          // 查询所有用户的所有字段资料
        if (empty($reply)) {                 // 判断是否出错
            return false;
        }
        return $reply;   // 返回修改后的数据
	}

	//查询楼主回复
	public function getReplyToDetailsByReplyId($reply_to_id){
        $reply = $this->where('reply_to_id',$reply_to_id)
		->where('reply_report_disabled', 0)
		->limit(1)
		->find();          // 查询所有用户的所有字段资料
        if (empty($reply)) {                 // 判断是否出错
            return false;
        }
        return $reply;   // 返回修改后的数据
	}

	public function users()
    {
        return $this->belongsTo('Users','userid');
    }

	public function getUpdateReplyDetails($limit = 10){
		$article_reply_list = $this->where('reply_report_disabled', 0)
		->where('article_report_disabled', 0)
		->where('reply_to_id', 'NULL')
		->group('articleid')
		->field('replyid,reply_to_id,articleid,article_userid,userid,article_username,thumb_img,reply_username,article_coins,content,content_text,view_count,title,create_date,article_personal_pic,reply_personal_pic')
		->order('max(create_date)', 'desc')
		->paginate($limit);
        if (empty($article_reply_list)) {                 // 判断是否出错
            return false;
        }
        return $article_reply_list;   // 返回修改后的数据
	}

	public function getUpdateReplyDetailsByUserId($userid, $limit = 10){
		$article_reply_list = $this->where('userid', $userid)
		->where('reply_report_disabled', 0)
		->where('article_report_disabled', 0)
		->group('articleid')
		->paginate($limit);
        if (empty($article_reply_list)) {                 // 判断是否出错
            return false;
        }
        return $article_reply_list;   // 返回修改后的数据
	}
}