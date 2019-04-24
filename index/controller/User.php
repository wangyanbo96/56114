<?php
namespace app\index\controller;

use think\Db;
use think\Model;
use think\Controller;
use think\View;
use think\Cache;

/*
 *需要登陆才可以进行的操作
 */
class User extends Index
{

    public function _initialize(){
        parent::_initialize();
        $this->userid = $userid    = @$_POST['userid'];
        $token    = @$_POST['token'];

        $t = db('token')->where('userid',$userid)->value('token');

        if($t != $token){
            $data['status'] = '500';
            $data['message'] = '登录状态异常，请重新登录';
            //echo  json_encode($data);exit;
        }
    }

    //个人订单列表
    public function order_list(){

        $p = @$_POST['p']?$_POST['p']:'1';
        //$userid = $_POST['userid'];
        $data = db('shipments_order')->where('userid',$this->userid)->limit('5')->page($p)->order('time desc')->select();
        foreach ($data as $key => $value){
            $data[$key]['fhaddress'] = explode(' ',$data[$key]['fhaddress']);

            $data[$key]['fhaddress'] = @$data[$key]['fhaddress']['0'].@$data[$key]['fhaddress']['1'];

            $data[$key]['shaddress'] = explode(' ',$data[$key]['shaddress']);
            $data[$key]['shaddress'] = @$data[$key]['shaddress']['0'].@$data[$key]['shaddress']['1'];
            unset($data[$key]['fhaddress1']);
            unset($data[$key]['fhphone']);
            unset($data[$key]['shphone']);
            unset($data[$key]['shaddress1']);
            unset($data[$key]['goodsnum']);
            unset($data[$key]['weight']);
            unset($data[$key]['volume']);
            unset($data[$key]['chetype']);
            unset($data[$key]['remark']);
            unset($data[$key]['long']);
            unset($data[$key]['timeout']);
        }
        $res['status'] = '200';
        $res['message']= '查询成功';

        $res['data']   = $data;
        return json_encode($res);
    }
    //我要发货--预约
    public function delivery(){
        $tracking = rand(100,999).time().rand(100,999);
        $data=[
            'fhuser' => $_POST['fhuser'],//发货人
            'fhphone'  		=> $_POST['fhphone'],//发货人电话
            'fhaddress' => $_POST['fhaddress'],//发货地址-省市县
            'fhaddress1' => $_POST['fhaddress1'],//发货地址
            'shuser' 	=> $_POST['shuser'],//收货人
            'shphone' 	=> $_POST['shphone'],//收货人电话
            'shaddress' => $_POST['shaddress'],//收货地址-省市县
            'shaddress1' => $_POST['shaddress1'],//收货地址
            'goodsname' 		=> $_POST['goodsname'],//货物名字
            'goodsnum' 		=> $_POST['goodsnum'],//数量
            'weight' 	=> @$_POST['weight'],//重量
            'volume' 		=> @$_POST['volume'],//体积    重量体积二选一
            'time' 		=> $_POST['time'],//预约时间
            'chetype' 		=> $_POST['chetype'],//车型
            'remark' 	=> $_POST['remark'],//备注
            'tracking'  => $tracking ,//订单号
            'long'      => $_POST['long'],
            'userid'    => @$this->userid,
        ];

        $res = db('shipments_order')->insert($data);
        //$Db::name('user')->insertGetId($data);
        $tracking = ['dingdanhao'=>$tracking];
        return $this->jsonaa('200','生成订单成功',$tracking);

    }
    //对公司发货下单
    public function delivery1(){
        //header('Access-Control-Allow-Origin:*');//允许跨域
        $tracking = rand(100,999).time().rand(100,999);

        $data=[
            'fhuser' =>   @$_POST['fhuser'],//发货人
            'fhphone'  		=> @$_POST['fhphone'],//发货人电话
            'fhaddress' => @$_POST['fhaddress'],//发货地址-省市县
            'fhaddress1' => @$_POST['fhaddress1'],//发货地址

            'shaddress' => @$_POST['shaddress'],//收货地址-省市县
            'shaddress1' => @$_POST['shaddress1'],//收货地址
            'goodsname' => @$_POST['goodsname'],//货物名字
            'goodsnum' 	=> @$_POST['goodsnum'],//数量
            'weight' 	=> @$_POST['weight'],//重量
            'volume' 	=> @$_POST['volume'],//体积    重量体积二选一
            'time' 		=> date("Y-m-d"),//预约时间
            'remark' 	=> @$_POST['remark'],//备注
            'companyId' => @$_POST['companyId'],//物流公司ID
            'tracking'  => @$tracking ,//订单号
            'userid'    => @$_POST['userid'],
        ];

        $res = db('shipments_order')->insert($data);
        //$Db::name('user')->insertGetId($data);
        $tracking = ['dingdanhao'=>$tracking];
        return $this->jsonaa('200','生成订单成功',$tracking);

    }
}