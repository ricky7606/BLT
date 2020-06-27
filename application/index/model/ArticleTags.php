<?php
namespace app\index\model;

//导入模型类
use think\Model;
use app\index\model\Users;

class ArticleTags extends Model {
	
	// 设置当前模型对应的完整数据表名称
    protected $table = 'BLT_article_tags';

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
	
	public function saveArticleTags($articleid, $tagid){
		$this->articletagsid=uuid();
		$this->articleid=$articleid;
		$this->tagid=$tagid;
		return $this->isUpdate(false)->save();
	}

	public function deleteArticleTags($articleid){
		$result = $this->where('articleid', $articleid)
		->delete();
		return $result;
	}
	
	public function getTagsByArticleId($articleid){
		$result = $this->where('articleid', $articleid)
		->find();
		return $result;
	}
	
	public function users()
    {
        return $this->belongsTo('Users','userid');
    }
}