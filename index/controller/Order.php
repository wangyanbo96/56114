<?php
namespace app\index\controller;

use think\Db;
use think\Model;
use think\Controller;
use think\View;
use think\Cache;




class Order extends Index{

	//发货人查看订单
    public function order_list($userid){
    	$data = db('shipments_order')->where('fhuser',$userid)->select();
    	$res['status'] = '200';
    	$res['message'] = '查询成功';
    	$res['data'] = $data;
    	return json_encode($res);
    }

    
    


    

}