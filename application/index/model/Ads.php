<?php
namespace app\index\model;

//导入模型类
use think\Model;

class Ads extends Model {
	
	// 设置当前模型对应的完整数据表名称
    protected $table = 'BLT_ads';

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

	public function getAdsByPosition($position){
		$ads = $this->where('position', $position)
		->where('enable', 1)
		->order('create_date','desc')
		->limit(1)
		->find();
		return $ads;
	}

	public function getAdsById($ads_id){
		$ads = $this->where('ads_id', $ads_id)
		->where('enable', 1)
		->limit(1)
		->find();
		return $ads;
	}

	public function adClick($ads_id){
		$this->where('ads_id', $ads_id)->setInc('clicks');
	}
}