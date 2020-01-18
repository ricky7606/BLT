<?php
namespace app\mobile\model;

//导入模型类
use think\Model;
use app\mobile\model\Users;

class Likes extends Model {
	
	// 设置当前模型对应的完整数据表名称
    protected $table = 'BLT_user_qna_like';

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

	public function getLikeCount($qnaid){
		$likes = $this->where('qnaid', $qnaid)
		->field('count(*) as likeCount')
		->find();
		return $likes;
	}
	
	public function getLikeByQnaIdUserId($qnaid, $userid){
		$likes = $this->where('qnaid', $qnaid)
		->where('userid', $userid)
		->limit(1)
		->find();
		return $likes;
	}
	
	public function saveLike($userid, $qnaid){
		$likes = $this->where('userid', $userid)
		->where('qnaid', $qnaid)
		->limit(1)
		->find();
		if(!$likes){
			$this->user_qna_likeid = uuid();
			$this->userid = $userid;
			$this->qnaid = $qnaid;
			$this->like_date = date('Y-m-d H:i:s',time());
			$result = $this->isUpdate(false)->save();
			if($result){
				return "点赞成功";
			}else{
				return "发生错误";
			}
		}else{
			$this->where('user_qna_likeid',$likes->user_qna_likeid)->delete();
			return "取消点赞成功";
		}
	}
}