<?php
namespace app\index\controller;

use think\Db;
use think\Model;
use think\Controller;
use think\View;
use think\Cache;

class Demo extends Index
{
	//省级地区信息    
	public function diming(){
		$data = db('area')->limit(34)->select();
		return $this->jsonaa(200,'成功',$data);
	}


	//查询线路
	public function xl1($sta,$end){

		$sta=$_GET['sta'];$end=$_GET['end'];
		$where=['sta'=>$sta, 'end'=>$end];

		$id = $sta;
		$res = db('area')->where('areaname',$id)->find();
		$sta=$res['areaid'];//

		$id = $end;
		$res = db('area')->where('areaname',$id)->find();
		$end=$res['areaid'];


		$where['sta']=$sta;
		$where['end']=$end;
		$where['del_flag']=0;
		
		$data = db('line')->where($where)->select();

		foreach ($data as $key => $value) {
			$id = $data[$key]['wl_id'];
			$res = db('wlcompany')->where('id',$id)->find();
			$data[$key]['wl_id']=$res['name'];
			$data[$key]['qz']=$res['qz'];

			$id = $data[$key]['sta'];
			$res = db('area')->where('areaid',$id)->find();
			$data[$key]['sta']=$res['areaname'];

			$id = $data[$key]['end'];

			$res = db('area')->where('areaid',$id)->find();
			$data[$key]['end']=$res['areaname'];		
			
		}
		//按照数组某个元素升序排列
		array_multisort(array_column($data,'qz'),SORT_ASC,$data);
		return json_encode($data);
	}

	//添加线路
	public function addxl(){
		$data = db('area')->limit(32)->select();
		$this->assign('data',$data);
		return $this->fetch('',$data);
	}

	//添加线路入库
	public function addxl1($sta,$end,$gs){
		$sta=$_GET['sta'];$end=$_GET['end'];$gs=$_GET['gs'];
		if($sta=='' || $end == '' || $gs == ''){
			$this->error('不能为空');
		}
		$data=[
			'sta'=>$sta,
			'end'=>$end,
			'wl_id'=>$gs,
		];
		//验证该公司是否已有该线路
		$num=db('line')->where($data)->count();
		if($num!=''){
			return "该线路已存在";
		}else{
			$res = db('line')->insert($data);
			return $this->jsonaa('200','插入成功',$res);
		}
	}

	public function area1($name){    	
    	if($_GET['name']==''){
    		$data = db('area')->limit(32)->select();
    	}else{
    		$name=$_GET['name'];
	    	$id= db('area')->where('areaid',$name)->value('areaid');
	    	$data = db('area')->where('parentId',$id)->select();
    	}
    	return $data;
    }

    //刷新排名
    public function sx($id){
    	$wl_id = db('line')->where('id',$id)->value('wl_id');
    	//查询余额，如果有就执行
    	$balance = db('wallet')->where('companyId',$wl_id)->value('balance');
    	if($balance < 1){
    		return jsonaa(200,'余额不足',$balance);
    	}
    	

		//精确十三位时间戳
		list($msec, $sec) = explode(' ', microtime());
		$msectime =  (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
		$times = substr($msectime,0,13);
		
     	$data = db('line')->where('id',$id)->update(['qz'=>$times]);
     	
     	//更新钱包余额
     	$res = db('wallet')->where('companyId',$wl_id)->update(['balance' => Db::raw('balance-1')]);
     	//$balance = db('wallet')->where('companyId',$wl_id)->value('balance');
     	//添加日志
     	$data = [
     		'edittime' => date("Y-m-d H:i:s"),
     		'walletId' => $wl_id,
     		'type'	   => '刷新',
     		'sum'      => '1',
     		'balance'  => $balance-1,
     	];
     	$res = db('wallet_log')->insert($data);
    	//var_dump($res);die;
    	//$data=db('company_address')->where('id','<>', $id)->where('qz','<', $qz)->update(['qz' => Db::raw('qz+1')]);
    	return $this->jsonaa('200','刷新成功',$data);

    }

	//汽车车型
	public function car(){
		$data = db('trucktype')->select();
		return $this->jsonaa('200','查询成功',$data);
	}//

	//查询单号
	public function tracking(){
		$tracking  = $_GET['num'];
	}


	//搜索地区
    public function area($name=''){
    	//Cache::set('name',@$value,3600);
    	//dump(Cache::get('name')); 

	    //$name = @$_GET['name'];
	    //var_dump($name);die;
	    $name = trim($name);

	   	$conn = '';
	    if (!empty($name)) {
	    	$where['areaid'] = $name;
	    	//var_dump($name);die;


	    	$data = db('area')->where($where)->select();
	    	$res = db('area')->where('parentId',$name)->select();
	    	//var_dump($where);die;
	    	$arr = [];
		    foreach ($res as $key => $value) {
				$arr[$key] = $res[$key];			    			
		    	$s = db('area')->where('parentId',$res[$key]['areaid'])->select();

		    	//$arr[] = $s;			    			
		    	//array_push($arr[$key], $s);
		    	array_unshift($arr[$key], $s);	
		    	//array_push($arr[$key], $s);
		    	//var_dump($arr[$key]['0']);
		    	$arr[$key]['diqu']=$arr[$key]['0'];
		    	unset($arr[$key]['0']);

		    }
	    	$conn = $arr;
	    	$res = '';
	    	

	    	if(empty($conn)){

	    		 $conn = db('area')->field('areaid,areaname,parentId')->where($where)->select();//查询数据

		        //下面是输地名查
		        if(empty($conn)){
		        	//var_dump($conn);
		        	$conn = '';
		        	$key = '';
		        	//$aa = new Pinyin;
		        	//$name = $aa->getpy($name,true);

		        	$key['areaname'] = array('like',  $name . '%');

		        	$conn = db('area')->field('areaid,areaname,parentId')->where($key)->select();//查询数据

		        	foreach ($conn as $key => $value) {
		        		if($conn[$key]['areaid'] < 35){
		        			unset($conn[$key]);
		        		}
		        	}
					
		        	//var_dump($conn);
		        	// $sql=Db::table('area')->getLastSql();
		        	// var_dump($sql);
		        }
		        //按拼音查
		        if(empty($conn)){
		        	//var_dump($conn);
		        	$conn = '';
		        	$key = '';
		        	//转拼音


		        	$key['pinyin'] = array('like', $name . '%');

		        	$conn = db('area')->field('areaid,areaname,parentId')->where($key)->select();//查询数据
		        	foreach ($conn as $key => $value) {
		        		if($conn[$key]['areaid'] < 35){
		        			unset($conn[$key]);
		        		}
		        	}
		        }
		        //按别名查
		        if(empty($conn)){
		        	//var_dump($conn);
		        	$conn = '';
		        	$key = '';
		        	//转拼音

		        	$key['alias'] = array('like', '%'.$name . '%');

		        	$conn = db('area')->field('areaid,areaname,parentId')->where($key)->select();//查询数据
		        	$sql=Db::table('area')->getLastSql();
		        	//var_dump($sql);die;
		        	foreach ($conn as $key => $value) {
		        		if($conn[$key]['areaid'] < 35){
		        			unset($conn[$key]);
		        		}
		        	}
		        }
	    	}		        
	    }
	    //echo "<pre>";
	    foreach ($conn as $key => $value) {
	    	if($conn[$key]['areaname']=='全境'){
	    		unset($conn[$key]);
	    	}
	    }
	    //var_dump($conn);
		$conn = array_merge($conn);

	    if ($conn) {
	        $res['code'] = 1;
	        $res['msg'] = '查询成功';
	        $res['parent'] = db('area')->where('areaid',$conn[0]['parentId'])->value('areaname');
	        $res['data'] = $conn;
	        
	    } else {
	    	$res['code'] = 0;
	        $res['msg'] = '查询失败';
	        $res['data'] = $conn;
	    }

	    return json_encode($res);
 
    }

   
    //物流公司列表
    public function gs($p='1',$name=''){
    	$p=$_GET['p'];
    	$data = Cache::get('gs');//获取缓存
    	//var_dump($data);die;
    	if(!$data){

	    	//var_dump($name);
	    	if(empty($name)){
	    		$data = db('company1_copy1')->limit(5)->page($p)->select();
	    	}else{
	    		//var_dump($name);
	    		$where['companyName'] = array('like', '%' . $name . '%');
	    		Db::table('company1_copy1')->cache('gs',60)->limit(5)->page($p)->where($where)->select();
	    		$data = Cache::get('gs'); 
	    		$sql=Db::table('area')->getLastSql();
	    		//var_dump($sql);
	    	}	
    	}
    	//var_dump($data);die;
    	
    	//$data1 = db('wlcompany')->order(['qz'=>'asc'])->where('tg',0)->select();
    	//$data = array_merge($data,$data1);

    	return $this->jsonaa('200','成功',$data);
    }

    public function uploadphoto(){
    	return $this->fetch();
    }
    public function uploadphoto1(){
		// 获取表单上传文件
	    $file = request()->file('image');
	    
	    // 移动到框架应用根目录/public/uploads/ 目录下
	    if($file){
	        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
	        if($info){
	            // 成功上传后 获取上传信息
	            // 输出 jpg
	            echo $info->getExtension();
	            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
	            echo $info->getSaveName();
	            // 输出 42a79759f284b767dfcb2a0197904287.jpg
	            echo $info->getFilename(); 
	        }else{
	            // 上传失败获取错误信息
	            echo $file->getError();
	        }
	    }
    
    }

    //查询地区信息，除去乡镇
    public function xs(){
		$data = db('area')->select();
		var_dump($data);
    }



    //公司修改信息
    public function editdatil(){
		$tel = $_GET['tel'];
    	$num = $_GET['code'];
    	$data = db('code')->where('tel',$tel)->find();
    	$code = $data['code'];
		$id=$_GET['id'];
		//1为国内物流公司
		if($id==1||$id==3||$id==4||$id==5||$id==6||$id==7||$id==8){
			$tel=$_GET['tel'];
			$data='';

			$gsmc=$_GET['gsmc'];
			$dizhi=$_GET['dizhi'];
			$data['company']=$gsmc;
			$data['address']=$dizhi;
			$data['name']=$_GET['name'];
			$data=db('userinfo')->where('mobile',$tel)
				->setField([
					'name' =>$_GET['name'],
					'address'=>$_GET['dizhi'],
					'company'=>$_GET['gsmc'],
				]);
			$sql=Db::table('contract')->getLastSql();

			$data=db('userinfo')->where('mobile',$tel)->find();
			//var_dump($data);
			return $this->jsonaa('200','更新成功',$data);
		}
		//2为车主
		if($id==2){
			$tel=$_GET['tel'];
			$data='';

			//$gsmc=$_GET['gsmc'];
			$dizhi=$_GET['dizhi'];
			//$data['company']=$gsmc;
			$data['address']=$dizhi;
			$data['name']=$_GET['name'];
			$data=db('userinfo')->where('mobile',$tel)
				->setField([
					'name' =>$_GET['name'],
					'address'=>$_GET['dizhi'],
					//'company'=>$_GET['gsmc'],
				]);
			$sql=Db::table('contract')->getLastSql();
			//var_dump($sql);
			$data=db('userinfo')->where('mobile',$tel)->find();
			//var_dump($data);
			return $this->jsonaa('200','更新成功',$data);
		}
    		
    }    

    //海外地区信息
    public function diming1(){
		$data = db('area')->limit(38,13)->select();
		return $this->jsonaa(200,'成功',$data);
	}

	//添加定时刷新
	public function dingshi($id='', $minutes='', $days='', $num = ''){
        //查询物流币
        $data = db('line')->where('id',$id)->select();
        $wl_id=$data['0']['wl_id'];
        $data = db('wallet')->where('companyId',$wl_id)->select();
        $money= $data['0']['balance'];
        $days = empty($days)?('365'):($days);
        $lineId = @$id;
		//单独刷新一条线路 次数
		//id为线路id，$minutes为刷新间隔时间
		//$sum = '30';

		
		$time = date("Y-m-d");//起始时间
		$time = $time.' 08:00';//时间一
		$end = strtotime(date("Y-m-d H:i")." +"."4"." hours");
		$end = date('Y-m-d H:i',$end);//结束时间



		$a=0;
		$data = '';
		for ($j=0; $j <$days ; $j++) { 			
			$time1 = $time;
			$end1 = $end;		
			for ($i=0; $i < 10; $i++) { 
				if($a==@$money || $a==$num){
					break;
				}
				$time1= strtotime($time1." +".$minutes." minutes");
				var_dump($time1);
				$end1 = strtotime($end1);
				if($time1 <= $end1){
					$time1 = date('Y-m-d H:i',$time1);
					$data['lineId'] = $lineId;
					$data['time']	= $time1;

					db('dingshi')->insert($data);
					$end1 = date('Y-m-d H:i',$end1);
					$a++;
				}else{
					$time1 = date('Y-m-d H:i',$time1);
					$end1 = date('Y-m-d H:i',$end1);
				}
			}
			$time = strtotime($time." +"."1 days");			
			$time = date('Y-m-d H:i',$time);				
			$end = strtotime($end." + 1 days");
			$end = date('Y-m-d H:i',$end);
		}
		return $a;

	}
	//网点批量添加定时刷新
	public function piliangdingshi($id='', $minutes='60',$days='10'){
		//id为网点id
		$data = db('line')->where('wl_id',$id)->select();

		foreach ($data as $key => $value) {
			$id = $data[$key]['id'];
		//var_dump($id);var_dump($days);
			$this->dingshi($id,$minutes,$days);
		}
		//var_dump($data);
	}
	//物流公司批量添加定时刷新
	public function gsdingshi($id='', $minutes='60',$days='10'){
		//id为公司id
		$res = db('company_address')->where('companyId',$id)->select();
		foreach ($res as $key => $value) {
			$wl_id = $res[$key]['id'];
			$data = db('line')->where('wl_id',$wl_id)->select();

			foreach ($data as $key => $value) {
				$id = $data[$key]['id'];
				$data = $this->dingshi($id,$minutes,$days);
			}
		}
		return json_encode($data);

	}

	//执行定时刷新
	public function dingshi1(){
		$time = date("Y-m-d H:i");
		$num = 0;
		$data = db('dingshi')->where('time',$time)->select();
		//var_dump($time);
		foreach ($data as $key => $value) {
			$id = $data[$key]['lineId'];
			$res = $this->sx($id);
			$where=[
				'time'=>$time,
				'lineId'=>$id,								
			];
			$da=['status'=>1];
			db('dingshi')->where($where)->update($da);				
			$num++;		
		}
		return json_encode($data);
	}

	//专线详情
	public function zxdetail($id = '1'){
		$data = db('line')->where('id',$id)->select();
		//var_dump($data);
		$sta = $data['0']['sta'];
		$sta = db('area')->where('areaid',$sta)->value('areaname');
		$end = $data['0']['end'];
		$end = db('area')->where('areaid',$end)->value('areaname');
		$wl_id = $data['0']['wl_id'];
		$data = db('company_address')->where('id',$wl_id)->select();
		$sta_wangdian = $data;
		//到达地网点信息暂用出发地网点信息
		$companyId = $data['0']['companyId'];
		$companyName = db('company1_copy1')->where('companyId',$companyId)->value('companyName');

		$end =  str_replace('市','',$end);
		$end =  str_replace('县','',$end);
		$end =  str_replace('区','',$end);
		$end =  str_replace('地区','',$end);	
		$end =  str_replace('乡','',$end);	
		$end =  str_replace('镇','',$end);		

		$where = [
			'branchName' => array('like',  $end . '%'),
			'companyId'  => $companyId,
		];
		$data1 = db('company_address')->where($where)->select();
		$end_wangdian = $data1;

		if(empty($data1)){
			$data1 = $data;
		}

		$phone = $data['0']['phone'];
		

		$data1 = $this->qitaxl1($companyId);
		unset($sta_wangdian['0']['longitude'],$sta_wangdian['0']['dimensions'],$sta_wangdian['0']['branchprovince'],$sta_wangdian['0']['branchcity'],$sta_wangdian['0']['brancharea'],$sta_wangdian['0']['qz'],$sta_wangdian['0']['sta_time'],$sta_wangdian['0']['end_time'],$end_wangdian['0']['longitude'],$end_wangdian['0']['dimensions'],$end_wangdian['0']['branchprovince'],$end_wangdian['0']['branchcity'],$end_wangdian['0']['brancharea'],$end_wangdian['0']['qz'],$end_wangdian['0']['sta_time'],$end_wangdian['0']['end_time']);

		//运价（暂无）
		//
		$price = [
			'easy' => '100/吨',
			'heavy' => '50/吨',
			'aging' => '一周',
			'frequency' => '1次/天',
		];
		$data = [
			'status' => '200',
			'companyName' => $companyName,
			'phone' => $phone,
			'sta' => @$sta,
			'sta_wangdian' => $sta_wangdian,
			'end_wangdian' => $end_wangdian,
			'end' => @$end,
			//'lianxi' => $data,
			'price' => $price,
			'line' => $data1,

		];



		return json_encode($data);
	}

	//查询公司主营线路
	public function qitaxl($id = '1'){
		$companyId = $id;
		//$wl_id = db('line')->where('id',$id)->value('wl_id');
		//$companyId = db('company_address')->where('id',$wl_id)->value('companyId');
		$wd = db('company_address')->where('companyId',$companyId)->select();//该公司所有网点
		if(empty($wd)){
			return $this->jsonaa('201','数据为空',NULL);
		}
		$line = [];
		foreach ($wd as $key => $value) {
			$line1 = db('line')->where('wl_id',$wd[$key]['id'])->select();
			if(!empty($line1)){
				array_unshift($line, $line1);
			}
			
		}
		foreach ($line as $key => $value) {
			$line[$key]=$line[$key]['0'];
		}
		$data = $line;
		foreach ($data as $key => $value) {
			$sta = db('area')->where('areaid',$data[$key]['sta'])->value('areaname');
			$end = db('area')->where('areaid',$data[$key]['end'])->value('areaname');
			$data[$key]['sta'] = $sta;
			$data[$key]['end'] = $end;
			unset($data[$key]['del_flag']);
			unset($data[$key]['qz']);
		}
		//$data['companyName']= db('company1_copy1')->where('companyId',$companyId)->value('companyName');
		
		return $this->jsonaa('200','查找成功',$data);
	}

	//热搜线路
	public function firexl(){
		$where['parentId']=array('lt',35);
		$data = db('area')->where($where)->select();

		$data=[];
		for ($i=0; $i < 35; $i++) { 
			$data1 = db('area')->where('parentId',$i)->limit(1)->select();
			array_unshift($data, $data1);
		}
		
		$num = rand(0,31);
		$sta = $data[$num]['0'];
		$num = rand(0,31);
		$end = $data[$num]['0'];

		$data=[
			'sta'=>$sta,
			'end'=>$end,
		];
		$res = $this->gs1(35,36);
		//var_dump($sta['areaid']);
		return $res;
	}

	//评价物流公司
	public function evaluate($user_id,$companyId,$content){
		$data = [
			'userid' => $user_id,
			'addtime'=> date('Y-m-d H:i:s'),
			'companyId'=> $companyId,
			'content' => $content,
		];
		$res = db('company_evaluate')->insert($data);
		return $this->jsonaa('200','评价成功',$res);
	}
	//展示公司评价
	public function showEvaluate($companyId){
        $companyId = 222;
		$where = [
			'companyId'=>$companyId,
			'status' => '已审核',
		];
		$data = db('company_evaluate')->limit(5)->where($where)->select();
		foreach ($data as $k => $v){
		    $username = db('userinfo')->where('rid',$data[$k]['userid'])->value('name');
		    $data[$k]['username'] = $username;
        }
        return $data;
	}

	//设置网点工作状态时间
	public function set_time(){
		$sta_time = date("H:i");
		$end_time = date("H:i");
		$id = 4296;
		$data=[
			'sta_time' => $sta_time,
			'end_time' => $end_time,
		];
		$res = db('company_address')->where('id',$id)->update($data);
		var_dump($res);
	}

	//公司信息
	public function gsdetail($companyId){
		$data = db('company1_copy1')->where('companyId',$companyId)->select();
		$data = $data['0'];
		$data1 = $this->qitaxl1($companyId);
		$evaluate = $this->showEvaluate($companyId);

		$data = [
			'lianxi' => $data,
			'line' => $data1,
            'evaluate'=>$evaluate,
		];
		return $this->jsonaa('200','查找成功',$data);
	}
	public function qitaxl1($id = '1'){
		$companyId = $id;
		//$wl_id = db('line')->where('id',$id)->value('wl_id');
		//$companyId = db('company_address')->where('id',$wl_id)->value('companyId');
		$wd = db('company_address')->where('companyId',$companyId)->select();//该公司所有网点
		if(empty($wd)){
			return $this->jsonaa('201','数据为空',NULL);
		}
		$line = [];
		foreach ($wd as $key => $value) {
			$line1 = db('line')->where('wl_id',$wd[$key]['id'])->select();
			if(!empty($line1)){
				array_unshift($line, $line1);
			}
			
		}
		foreach ($line as $key => $value) {
			$line[$key]=$line[$key]['0'];
		}
		$data = $line;
		foreach ($data as $key => $value) {
			$sta = db('area')->where('areaid',$data[$key]['sta'])->value('areaname');
			$end = db('area')->where('areaid',$data[$key]['end'])->value('areaname');
			$data[$key]['sta'] = $sta;
			$data[$key]['end'] = $end;
			unset($data[$key]['del_flag']);
			unset($data[$key]['qz']);
		}
		//$data['companyName']= db('company1_copy1')->where('companyId',$companyId)->value('companyName');
		
		return $data;
	}

	//按线路搜索物流公司列表
    public function gs1($sta,$end,$p=1){
    	@$sta1 = $sta ;
    	@$end1 = $end ;

    	$where = [
    		'sta' => $sta,
    		'end' => $end,
    	];
    	$res = db('line') -> where($where) ->limit(5)->page($p)-> select();
    	
    	$data = [];

    	array_multisort(array_column($data,'qz'),SORT_DESC,$data);
    	if(empty($res)){
    		$mes = "没有数据";
    		return $this->jsonaa('203',@$mes,$res);
    	}
    	foreach ($res as $key => $value) {
    		if($res[$key]['type'] == 2){
    			$data1 = db('company1_copy1')->where('companyId',$res[$key]['wl_id'])->select();
    		}else{
    			$data = db('company_address')->where('id',$res[$key]['wl_id'])->select();
    			if(empty($data)){
		    		$mes = "查询成功";
		    		db('line')->where('id',$res[$key]['id'])->delete();
		    		unset($res[$key]);

		    		return $this->jsonaa('200',@$mes,$res);
		    	}
    			//查询时间判断是否休息
    			$time = date("H:i");//当前时间
    			//var_dump($data);
    			$sta_time = $data['0']['sta_time'];
    			$end_time = $data['0']['end_time'];
    			
    			//判断该公司是否处于工作时间
    			if($time < $sta_time && $time > $end_time){
    				$data['0']['branch_status'] = 0;
    				$a = [
    					'branch_status' => $data['0']['branch_status']
    				];
    				db('company_address')->where('id',$data['0']['id'])->update($a);
    			}else{
    				$data['0']['branch_status'] = 1;
    				$a = [
    					'branch_status' => $data['0']['branch_status']
    				];
    				db('company_address')->where('id',$data['0']['id'])->update($a);
    			}

    			$parentId = @$data['0']['companyId'];
	    		$data1 = db('company1_copy1')->where('companyId',$parentId)->select();

    		}
    		
 
    		if(!empty($data1)){
	    		if(empty($data1)){
	    			//没有父级公司
	    			$res[$key]['tel'] = $data['0']['phone'];
	    			$res[$key]['wl_name'] = $data['0']['branchName'];
	    			$res[$key]['companyId'] = $data['0']['id'];
	    			$res[$key]['picture'] = '';
	    			$res[$key]['address'] = $data['0']['address'];
	    			unset($res[$key]);
	    			continue;
	    		}else{
	    			$res[$key]['tel'] = $data1['0']['phone'];
	    			$res[$key]['wl_name'] = $data1['0']['companyName'];

	    			$res[$key]['companyId'] = $data1['0']['companyId'];
	    			$res[$key]['picture'] = $data1['0']['detailPicture'];
	    			$res[$key]['address'] = $data['0']['address'];
		    	}
		    	//$res[$key]['wl_name'] = $data['0']['branch_status'];
    			$res[$key]['branch_status'] = $data['0']['branch_status'];
    			if($res[$key]['branch_status']==0){
    				$res[$key]['branch_status'] = '工作中';
    			}else{
    				$res[$key]['branch_status'] = '休息中';
    			}
    			

    			if(@$data1['0']['del_flag'] == 2){
    				unset($res[$key]);
    			}

    			//$res[$key]['att'] = $data1['0']['att'];
    			if($data1['0']['att']==0){
    				$res[$key]['att'] = '未认证';
    			}else{
    				$res[$key]['att'] = '已认证';
    			}
    			unset($res[$key]['qz']);unset($res[$key]['del_flag']);
    			
    			//$res[$key]['wd_name'] = $data[$key]['branchName'];
    			$res[$key]['sta1'] = db('area')->where('areaid',$res[$key]['sta'])->value('areaname');
    			$res[$key]['end1'] = db('area')->where('areaid',$res[$key]['end'])->value('areaname');
    			unset($res[$key]['type']);
    			unset($res[$key]['sta']);
    			unset($res[$key]['end']);
    		}else{
    			//var_dump($res[$key]);
    			db('line')->where('id',$res[$key]['id'])->delete();
    			unset($res[$key]);
    		}
    	}
    	return $this->jsonaa('200',@$mes,$res);
    }

    //公司所有网点
	public function wangdian($id=''){
		//header("Content-Type: application/json;");
		$data = db('company_address')->where('companyId',$id)->select();
		if(empty($data)){
			return $this->jsonaa('201','数据为空',NULL);
		}

		if($data){
			foreach ($data as $key => $value) {
				unset($data[$key]['longitude']);
				unset($data[$key]['dimensions']);
				unset($data[$key]['branchprovince']);
				unset($data[$key]['branchcity']);
				unset($data[$key]['brancharea']);
				unset($data[$key]['qz']);
			}
			
			
			$companyName = db('company1_copy1')->where('companyId',$data['0']['companyId'])->value('companyName');
			
			//$data = '';
			//$data = ['a'=> 'n'];
			//return json_encode($data);
			return $this->jsonaa('200','aaaa',$data);
		}else{
			return $this->err($data);
		}		
	}

	//公司评价
    public function evaluate1($companyId = '',$p='1'){
	    $companyId = @$_POST['companyId'];
	    $companyId = 222;
	    $where=[
            'companyId'=>$companyId,
            'status' => '已审核',
        ];
	    $data = db('company_evaluate')->field('userid,addtime,content')->limit(5)->page($p)->where($where)->select();
	    foreach ($data as $k => $v){
	        $data[$k]['tel']= db('userinfo')->where('rid',$data[$k]['userid'])->value('mobile');
            $data[$k]['username'] = db('userinfo')->where('rid',$data[$k]['userid'])->value('name');
        }
        if(empty($data)){
            $res['status'] = '201';
            $res['message']= '没有了';
            $res['data']   = $data;
            return json_encode($res);
        }
        $res['status'] = '200';
        $res['message']= '查询成功';
        $res['data']   = $data;
	    return json_encode($res);
    }
	
}
