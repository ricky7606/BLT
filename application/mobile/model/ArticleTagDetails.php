<?php
namespace app\mobile\model;

//导入模型类
use think\Model;
use app\mobile\model\Users;

class ArticleTagDetails extends Model {
	
	// 设置当前模型对应的完整数据表名称
    protected $table = 'view_ArticleTagDetails';

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
	
	public function getTagsByArticleId($articleid){
		$result = $this->where('articleid', $articleid)
		->select();
		return $result;
	}
	
	public function getArticlesByTagId($tagid, $num = 10, $page = 1){
		$article_count = $this->where('tagid',$tagid)
		->field('count(*) as article_count')
		->find();
		if($article_count->article_count<1){
            return "";
        }
		$total_page = ceil($article_count->article_count / $num);
		if($page > $total_page){$page = $total_page;}
		if($page <1){$page = 1;}
		$result = $this->where('tagid', $tagid)
		->order('title','asc')
		->limit($num*($page-1), $num)
		->select();
		$tmpStr = $total_page."###".$page."___";
		if($result){
			foreach($result as $article){
				$tmpStr .= $article->articleid."###".$article->title."$$$";
			}
		}
        return $tmpStr;   // 返回修改后的数据
	}

	public function users()
    {
        return $this->belongsTo('Users','userid');
    }
}