<?php
namespace app\index\model;

//导入模型类
use think\Model;
use app\index\model\Users;
use app\index\model\ArticleReportDetails;

class ArticleReport extends Model {
	
	// 设置当前模型对应的完整数据表名称
    protected $table = 'BLT_article_report';

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

	public function getFollowDetailsByUserId($userid){
		$follow = new ArticleFollowDetails;
		$follow_list = $follow->where('userid', $userid)
		->order('follow_date', 'desc')
		->paginate(20);
		return $follow_list;
	}
	
	public function getFollowByArticleIdUserId($articleid, $userid){
		$result = $this->where('articleid', $articleid)
		->where('userid', $userid)
		->limit(1)
		->find();
		return $result;
	}

	public function saveReport($userid, $articleid, $report_type, $article_type, $report_comment){
		$result = $this->where('userid', $userid)
		->where('articleid', $articleid)
		->limit(1)
		->find();
		if($result){
			return "您已经举报了该文章";
		}else{
			$this->reportid = uuid();
			$this->userid = $userid; 
			$this->articleid = $articleid;
			$this->report_type = $report_type;
			$this->article_type = $article_type;
			$this->report_comment = $report_comment;
			$this->report_date = date('Y-m-d H:i:s',time());
			$result = $this->isUpdate(false)->save();
			if($result){
				return "ok";
			}else{
				return "发生错误";
			}
		}
	}
	
	public function deleteFollow($followid){
		$result = $this->where('followid', $followid)
		->delete();
		return $result;
	}
	
	public function getFollowCount($articleid){
		$result = $this->where('articleid', $articleid)
		->field('count(*) as followCount')
		->find();
		return $result;
	}

	public function users()
    {
        return $this->belongsTo('Users','userid');
    }
}