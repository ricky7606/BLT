<?php
namespace app\mobile\model;

//导入模型类
use think\Model;

class ArticlesUser extends Model {
	
	// 设置当前模型对应的完整数据表名称
    protected $table = 'view_ArticleUser';

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
        $new_articleuser = $this->order('create_date','desc')
		->where('report_disabled', 0)
		->where('deleteFlag', 0)
		->paginate(10);          
        if (empty($new_articleuser)) {                 // 判断是否出错
            return false;
        }
        return $new_articleuser;   // 返回修改后的数据
	}

	public function getArticlesByPage($page, $num = 10){
		$page_count = $this->where('report_disabled',0)
		->where('deleteFlag', 0)
		->field('count(*) as search_count')
		->find();
		if($page_count->search_count<1){
            return false;
        }
		$total_page = ceil($page_count->search_count / $num);
		if($page > $total_page){return false;}
		if($page <1){$page = 1;}
		$new_articleuser = $this->order('create_date','desc')
		->where('report_disabled', 0)
		->where('deleteFlag', 0)
		->page($page, $num)
		->select();		
		return $new_articleuser;
	}

	public function getArticlesByUserId($userid){
        $articleuser = $this->where('userid',$userid)
		->where('report_disabled', 0)
		->where('deleteFlag', 0)
		->order('create_date','desc')
		->paginate(10);          // 查询所有用户的所有字段资料
        if (empty($articleuser)) {                 // 判断是否出错
            return false;
        }
        return $articleuser;   // 返回修改后的数据
	}
	
	public function getRandomArticlesUsers($limit){
        $random_users = $this->limit($limit)
		->field('distinct username')
		->order('ROUND(RAND()*1000)','desc')
		->select();          // 查询随机用户
        if (empty($random_users)) {                 // 判断是否出错
            return false;
        }
        return $random_users;   // 返回修改后的数据
	}    
	
	public function searchArticles($keywords){
		$search_articles = $this->where('title','like','%'.$keywords.'%')
		->where('report_disabled', 0)
		->where('deleteFlag', 0)
		->order('create_date','desc')
		->paginate(10,false,['query'=>request()->param()]);  
        if (empty($search_articles)) {                 // 判断是否出错
            return false;
        }
        return $search_articles;   // 返回修改后的数据
	}
}