<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Db;
use think\Cookie;
use app\index\model\Ads;

class Ad extends Controller
{
    public function index()
    {
		$id = Request::instance()->get('id');
		if($id != ''){
			$ads = new Ads;
			$ad = $ads->getAdsById($id);
			if($ad){
				$url = $ad->ads_link;
				$ads->adClick($id);
				return $this->redirect($url);
			}else{
				return $this->redirect('/index');
			}
		}else{
			return $this->redirect('/index');
		}
	}
}