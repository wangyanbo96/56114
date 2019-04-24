<?php
namespace app\index\controller;



use think\Db;
use think\Model;
use think\Controller;
use think\View;
use think\Cache;
//use app\index\controller;

class Test extends Index{


	public function ceshi($num,$sum){   
        $a=$_GET['num'];
        $b=$_GET['sum'];
        $data=[$a,$b];
        $data = $this->jsonaa('200','成功',$data);
        return $data;
    }


	public function xl(){
		return $this->fetch();
	}


    

    public function dbtest($id){
        $id=$_GET['id'];
        //$res =   Db::query('select * from test1 where id='.$id);
        $res = Db::table('test1')->where('id',1)->find();
        $data = $this->jsonaa('200','成功',$res);
        return $data;
    }

    //查物流公司
    public function dbtest2($name=''){
        $db = Db::connect('db_con2');
		$data = $db->query('select * from userinfo');
        
        var_dump($data);
    }


    //给地区表添加拼音
    public function pin(){
        $py = new Pinyin();
        //echo $py->getpy("爨底下",true);

        $data = db('area')->select();
        foreach ($data as $key => $value) {
            $name = $data[$key]['areaname'];
            $pinyin = $py->getpy($name,true);
            $res = [
                'pinyin' => $pinyin,

            ];
            db('area')->where('areaid',$data[$key]['areaid'])->update($res);
        }
        //var_dump($data);
        echo "hello";

        
    }

	//完善账号信息，物流公司、车主、配货信息部、国际物流企业、快递公司、搬家公司、发货企业或个人、物流设备企业、物流园区、停车场
	public function wsxx(){
		$tel = $_GET['tel'];
    	$num = $_GET['code'];
    	//$password = $_GET['password'];
    	$data = db('code')->where('tel',$tel)->find();
    	$code = $data['code'];
    	//设置万能验证码，调试用
    	$code=1234;
    	if($num==$code){
			//echo "<pre>";
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
				//var_dump($data);
				//$data=db('userinfo')->where('mobile',$tel)->update($data);
				//$sql=Db::table('contract')->getLastSql();
				//var_dump($sql);
				$data=db('userinfo')->where('mobile',$tel)
				->setField([
					'name' =>$_GET['name'],
					'address'=>$_GET['dizhi'],
					'company'=>$_GET['gsmc'],
				]);
				$sql=Db::table('contract')->getLastSql();
				//var_dump($sql);
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
				//var_dump($data);
				//$data=db('userinfo')->where('mobile',$tel)->update($data);
				//$sql=Db::table('contract')->getLastSql();
				//var_dump($sql);
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

		}else{
    		return $this->jsonaa('200','验证码不正确',$data);
    	}
	}


	//解析json文件导入数据库 
	//物多多
	public function demo(){
		$json_string = file_get_contents('D:\APP\APP\test\物多多\zhongshan.json'); 
		$data = json_decode($json_string, true); 

		$data1 = $data;
		foreach ($data as $key => $value) {			
			//查询是否已有该公司信息
			$id = db('company1')->where('companyId',$data[$key]['companyId'])->find();
			//var_dump($id);
			if(empty($id)){
				//不为空，说明没有该信息，继续执行
				$data1 = $data;		
				unset($data[$key]['branchCompanyList']);
				$res = db('company1')->insert($data[$key]);
					
				//插入该公司网点信息			
				$wangdian = $data1[$key]['branchCompanyList'];
				foreach ($wangdian as $k => $v) {
					$da = [
						'companyId' => $data[$key]['companyId'],
						'branchName' => $wangdian[$k]['branchName'],
						'address' => $wangdian[$k]['address'],
						'phone' => $wangdian[$k]['phone'],
						'telephone' => $wangdian[$k]['telephone'],
					];					
					$res = db('company_address')->insert($da);						
				}				
			}
		}
		return "hello";		
	}
	//物流帮帮
	public function demo1(){
		$json_string = file_get_contents('D:\APP\APP\test\wuliubangbang\new_data.json'); 
		$data = json_decode($json_string, true); 

		$data1 = $data;
		foreach ($data as $key => $value) {

			$data[$key]['address'] = $data[$key]['companyAddress'];

			unset($data[$key]['companyAddress']);

			
			//查询是否已有该公司信息
			$res = db('company1')->where('phone',$data[$key]['phone'])->find();
			
			//var_dump($res);
			if(empty($res)){
				//为空，说明没有该信息，继续执行
				$data1 = $data;		
				unset($data[$key]['branchCompanyList']);
				$res = db('company1')->insert($data[$key]);
				$companyId = db('company1')->getLastInsID();
				//var_dump($companyId);die;
					
				//插入该公司网点信息
			
				$wangdian = $data1[$key]['branchCompanyList'];
				foreach ($wangdian as $k => $v) {
					$da = [
						'companyId' => $companyId,
						//'branchName' => $wangdian[$k]['branchName'],
						'address' => $wangdian[$k]['address'],
						'phone' => $wangdian[$k]['phone'],
						'telephone' => $wangdian[$k]['telephone'],
					];
					
					$res = db('company_address')->insert($da);
						
				}				

			}else{
				//var_dump($data[$key]['address']);
				if($res['address'] !== $data[$key]['address']){
					$data1 = $data;		
					unset($data[$key]['branchCompanyList']);
					//var_dump($data1);
					$res = db('company1')->insert($data[$key]);
					//var_dump($res);
					$companyId = db('company1')->getLastInsID();
					//var_dump($companyId);die;
						
					//插入该公司网点信息
				
					$wangdian = $data1[$key]['branchCompanyList'];
					foreach ($wangdian as $k => $v) {
						$da = [
							'companyId' => $companyId,
							//'branchName' => $wangdian[$k]['branchName'],
							'address' => $wangdian[$k]['address'],
							'phone' => $wangdian[$k]['phone'],
							'telephone' => $wangdian[$k]['telephone'],
						];
						
						$res = db('company_address')->insert($da);
							
					}				
				}
			}
			
		}
		return "hello";		
	}
	//物流猫
	public function demo2(){
		$json_string = file_get_contents('D:\APP\APP\test\wuliumao.json'); 
		$data = json_decode($json_string, true); 

		$data1 = $data;
		foreach ($data as $key => $value) {
			
			$data1 = $data;	

			unset($data[$key]['branchCompanyList']);
			unset($data[$key]['companyNameShort']);
			unset($data[$key]['mainLine']);
			unset($data[$key]['companyDescribes']);

			$res = db('company1')->insert($data[$key]);
			//获取最新插入数据的id
			$companyId = db('company1')->getLastInsID();
					
			//插入该公司网点信息
			
			$wangdian = $data1[$key]['branchCompanyList'];
			foreach ($wangdian as $k => $v) {					
				$da = [
					'companyId' => $companyId,
					'branchName' => $wangdian[$k]['local'],
					'address' => $wangdian[$k]['address'],
					'phone' => $wangdian[$k]['phone'],
					'telephone' => $wangdian[$k]['telephone'],
					'content' => $wangdian[$k]['content'],
				];
					
				$res = db('company_address')->insert($da);
													
			}
			
		}
		return "hello";		
	}
	//永康物流网
	public function demo3(){
		$json_string = file_get_contents('D:\APP\APP\test\companyLineData.json'); 
		$data = json_decode($json_string, true); 

		$data1 = $data;
		foreach ($data as $key => $value) {
			
			$data1 = $data;	

			unset($data[$key]['lineAll']);				

			$res = db('company1')->insert($data[$key]);
			//获取最新插入数据的id
			$companyId = db('company1')->getLastInsID();
					
			//插入该公司网点信息
			
			$wangdian = $data1[$key]['lineAll'];
			foreach ($wangdian as $k => $v) {
				
				$da = [
					'companyId' => $companyId,
					'branchName' => $data1[$key]['companyName'],
					'address' => $wangdian[$k]['toAddress'],
					'phone' => $wangdian[$k]['toPhone'],
					'telephone' => $wangdian[$k]['toTelephone'],
					//'content' => $wangdian[$k]['content'],
				];
					
				$res = db('company_address')->insert($da);
						
			}			
		}
		return "hello";		
	}

	public function demo4(){
		echo input('?param.id');
	}

	//导出json地区信息
 	public function demo5(){
 		$res = $this->area(1);
 		 var_dump($res);
 		echo "<pre>";
  		for($i=2;$i<35;$i++){
		var_dump($res);
			$name = $i;
			$data = $this->area($name);

			//$data = json_decode($data, true);

			array_unshift($res, $data);	

			//$txt = $res."\n";
			
			//var_dump($data);die;
			// $json_string = file_get_contents('C:\Users\Administrator\Desktop\area.json'); 
			// $data = json_decode($json_string, true); 
  		}



		// $name = 7;
		// $data = $this->area($name);
		$res = json_encode($res);
		$myfile = fopen("C:\Users\Administrator\Desktop\area.json", "w") or die("Unable to open file!");
		fwrite($myfile, var_export($res, true));
		fclose($myfile);
		var_dump($res);
     	//return $res;
    }

    //测试缓存
    public function demo6(){
    	$data = Cache::get('demo6');//去缓存
    	//var_dump($data);
        if(!$data){
            Db::table('area')->cache('demo6',60)->limit(10)->select();
            $data = Cache::get('demo6');           
	 		
        }
        return $this->jsonaa(200,'查询成功',$data);

    }

    //修改公司表图片地址
    public function demo7(){
    	$data = Db::table('company1_copy1')->select();
    	$num = 0;
    	foreach ($data as $key => $value) {
    		$str = $data[$key]['detailPicture'];

    		$str = str_replace("https://attach.wudodo.cn//logisticsCompany/440000/440600/detail/", "http://192.168.0.109/uploads/img/",  $str);
    		$data[$key]['detailPicture'] = $str;
    		$res=db('company1_copy1')->where('companyId',$data[$key]['companyId'])
				->setField([
					'detailPicture' => $str,
				]);
			$num = $num + $res;
    	}
    	var_dump($num);
    	//return View('show');    	
    }

    //生成线路
	public function demo8(){
		$data = db('company_address')->limit(26890,2020)->field('id,branchName,companyId,address')->select();
		//var_dump($data);
		foreach ($data as $key => $value) {
			$str = $data[$key]['branchName'];
			// $str =  str_replace('网点','',$str);
			// $str =  str_replace('办事处','',$str);
			// $str =  str_replace('1','',$str);
			// $str =  str_replace('2','',$str);
			// $str =  str_replace('总部','',$str);
			// $str =  str_replace('网点1','',$str);
			// $str =  str_replace('网点2','',$str);
			$str = mb_substr($str,0,2,'utf-8');
			$str = $this->area2($str);
			$data[$key]['branchName'] = $str;
		}
		$res = [];
		foreach ($data as $k => $v) {
			$key = $data[$k]['companyId'];
			if(empty($res[$key])){
				$res[$key]=[];
			}
			array_push($res[@$key], $data[$k]);
		}
		foreach ($res as $a => $b) {
			$data = $res[$a];
			$conn = '';
			foreach ($data as $key => $value) {
				
				foreach ($data as $k => $v) {
					if($data[$key]['id']!=$data[$k]['id']){
						$conn = [
							'sta' => $data[$key]['branchName'],
							'end' => $data[$k]['branchName'],
							'wl_id'=>$data[$key]['id'],
						];
						//var_dump($conn);
						$result = db('line')->insert($conn);
					}
				}
			}
		}
		var_dump($result);
	}

	//根据地名返回地区id
	public function area2($name=''){
	    //$name = @$_GET['name'];
	    //var_dump($name);die;
	    $name = trim($name);
	   	$conn = '';		        
		$conn = '';
		$key = '';
		//转拼音
		// $aa = new Pinyin;
		// $name = $aa->getpy($name,true);
		//$key['pinyin'] = array('like', $name . '%');
		$key['areaname'] = array('like',  $name . '%');
		$conn = db('area')->field('areaid,areaname')->where($key)->select();//查询数据
		//var_dump($conn);
		if(empty($conn)){
			return $name;
		}else{
			$name= $conn[0]['areaid'];
		    //var_dump($conn);
		    return  $name;
		} 
    }

    //去除无用网点
    public function demo9(){
    	$data = db('company_address')->select();
    	
    	foreach ($data as $key => $value) {
    		$id = $value['companyId'];
    		$res = db('company1_copy1')->where('companyId',$id)->select();
    		if(empty($res)){
    			$a = db('company_address')->where('companyId',$id)->delete();
    		}
    	}
    	var_dump($a);
    }
    //去除无用线路
    public function demo10(){
    	echo "<pre>";
    	set_time_limit(0);
    	$a = 0;
    	$data = db('line')->field('wl_id')->limit(142000,1000)->select();
    	// var_dump($data);
    	// die;
    	
    	foreach ($data as $key => $value) {
    		$id = $value['wl_id'];
    		$res = db('company_address')->where('id',$id)->select();
    		if(empty($res)){
    			db('line')->where('wl_id',$id)->delete();
    			$a++;
    		}
    	}
    	var_dump($a);
    }

    //生成总部到下属网点线路
    public function demo11(){
    	set_time_limit(0);
    	$data = db('company1_copy1')->limit(10000,5000)->field('companyId,companyName')->select();
		
		foreach ($data as $key => $value) {
			$str = mb_substr($value['companyName'],0,2,'utf-8');
			$str = $this->area2($str);//总部所在地地区id
			$sta = $str;
			$res = db('company_address')->where('companyId',$value['companyId'])->select();
			foreach ($res as $k => $v) {
				$str = $res[$k]['branchName'];
				$str =  str_replace('网点','',$str);
				$str =  str_replace('办事处','',$str);
				$str =  str_replace('1','',$str);
				$str =  str_replace('2','',$str);
				$str =  str_replace('总部','',$str);
				$str =  str_replace('网点1','',$str);
				$str =  str_replace('网点2','',$str);
				$str = mb_substr($str,0,2,'utf-8');
				$str = $this->area2($str);
				$end = $str;var_dump($end);
				if(is_int($end)){
					$conn = [
							'sta'  => $sta,
							'end'  => $end,
							'wl_id'=>$value['companyId'],
							'type' => '2',
						];
						//var_dump($conn);
						$result = db('line')->insert($conn);
				}
			}
			

		}
		var_dump($result);
		die;
		foreach ($res as $a => $b) {
			$data = $res[$a];
			$conn = '';
			foreach ($data as $key => $value) {
				
				foreach ($data as $k => $v) {
					if($data[$key]['id']!=$data[$k]['id']){
						$conn = [
							'sta' => $data[$key]['branchName'],
							'end' => $data[$k]['branchName'],
							'wl_id'=>$data[$key]['id'],
						];
						//var_dump($conn);
						$result = db('line')->insert($conn);
					}
				}
			}
		}
		var_dump($result);
    }
    public function demo12(){
    	//<a href="http://192.168.0.109/index/test/demo13">aaaaaaaaaaaa</a>
    	echo "<a href='http://192.168.0.109/index/test/demo15'>跳转</a>";
    }
    public function demo13(){
    	var_dump($_SERVER['HTTP_REFERER']);
    }

    //聚时数据发送短信
    public function sendxin($tel){
    	$tel=$_GET['tel'];
    	$time=time();
    	//var_dump($time);die;
    	$data = db('code')->where('tel',$tel)->select();
    	return $data['0']['code'];
    	//var_dump($data);die;    	
    	//var_dump($data);
    	if(!empty($data[0]['code'])){
    		array_unshift($data, array_pop($data));
    		$sum = $time - $data[0]['time'];
    		//var_dump($data[0]['code']);
    		//判断验证码是否过期
    		if($sum < 6000){
    			die;
    		}
    	}
    	// var_dump($data);
    	// var_dump($time);
    	// var_dump("hello");**********************************************************************
    	//die;
		header('content-type:text/html;charset=utf-8');
		  
		$sendUrl = 'http://v.juhe.cn/sms/send'; //短信接口的URL
		$num = rand(1000,9999);
		$smsConf = array(
		    'key'   => 'bad6df95eb0714e4b26a6cc0c95423b5', //您申请的APPKEY
		    'mobile'    => $tel, //接受短信的用户手机号码
		    'tpl_id'    => '145986', //您申请的短信模板ID，根据实际情况修改
		    'tpl_value' =>'#code#='.$num.'&#m#=60' //您设置的模板变量，根据实际情况修改
		    //
		);
		 
		$content = $this->juhecurl($sendUrl,$smsConf,1); //请求发送短信		 
		if($content){
		    $result = json_decode($content,true);
		    $error_code = $result['error_code'];
		    if($error_code == 0){
		    	$data=array(
		    		'tel'=>$tel,
		    		'code'=>$num,
		    		'time'=>time()
		    	);
		    	$res=db('code')->where('tel',$tel)->find();
		    	if(!empty($res)){
		    		$data=db("code")->where('tel',$tel)->update($data);//数据库没有就插入，有就更新
		    	}else{
		    		$data=db("code")->insert($data);
		    	}		    	
		        //状态为0，说明短信发送成功
		        return $num;
		        //echo "短信发送成功,短信ID：".$result['result']['sid'];
		    }else{
		        //状态非0，说明失败
		        $msg = $result['reason'];
		        echo "短信发送失败(".$error_code.")：".$msg;
		    }
		}else{
		    //返回内容异常，以下可根据业务逻辑自行修改
		    echo "请求发送短信失败";
		}
		
    }
    public function juhecurl($url,$params=false,$ispost=0){
		/**
		 * 请求接口返回内容
		 * @param  string $url [请求的URL地址]
		 * @param  string $params [请求的参数]
		 * @param  int $ipost [是否采用POST形式]
		 * @return  string
		 */
		$httpInfo = array();
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
		curl_setopt( $ch, CURLOPT_USERAGENT , 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22' );
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 30 );
		curl_setopt( $ch, CURLOPT_TIMEOUT , 30);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );

		if( $ispost ){
		    curl_setopt( $ch , CURLOPT_POST , true );
		    curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
		    curl_setopt( $ch , CURLOPT_URL , $url );
		} else {
		    if($params){
		        curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
		    }else{
		        curl_setopt( $ch , CURLOPT_URL , $url);
		    }
	 	}

		$response = curl_exec( $ch );
		if ($response === FALSE) {
		    //echo "cURL Error: " . curl_error($ch);
		    return false;
		}
		$httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
		$httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
		curl_close( $ch );
		return $response;
	}

    //锦程物流网（国际物流）
    public function demo14(){
        $json_string = file_get_contents('D:\APP\APP\test\jincheng.json');
        $data = json_decode($json_string, true);
        //var_dump($json_string);die;
        $data1 = $data;
        foreach ($data as $key => $value) {
            //查询是否已有该公司信息
            //$id = db('international')->where('companyName',$data[$key]['companyName'])->find();
            //var_dump($id);

            if(empty($id)){
                //为空，说明没有该信息，继续执行
                $data1 = $data;

                $res = db('international')->insert($data[$key]);


            }
        }
        return "hello";
    }


    public function demo15()
    {
        header('Access-Control-Allow-Origin:*');//允许跨域

        $data = file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=' . $_SERVER['REMOTE_ADDR']);
        $dizhi = json_decode($data, $assoc = true);
        $ip = $dizhi['data']['ip'];
        $region = $dizhi['data']['region'];
        $city = $dizhi['data']['city'];
        $agent = $_SERVER["HTTP_USER_AGENT"];

        if (strpos($agent, 'MSIE') !== false || strpos($agent, 'rv:11.0')) //ie11判断
            $source = "ie";
        else if (strpos($agent, 'Firefox') !== false)
            $source = "firefox";
        else if (strpos($agent, 'Chrome') !== false)
            $source = "chrome";
        else if (strpos($agent, 'Opera') !== false)
            $source = 'opera';
        else if ((strpos($agent, 'Chrome') == false) && strpos($agent, 'Safari') !== false)
            $source = 'safari';
        else
            $source = 'unknown';
        //var_dump(date('Y-m-d H:i:s'));
        $data = [
            'source' => $_SERVER['HTTP_REFERER'],//来源
            //'explorer' => $_SERVER['HTTP_USER_AGENT'],//系统信息
            'explorer' => $source,//浏览器信息
            'ip' => $ip,
            'region' => $region,
            'city' => $city,
            'sta_time' => date('Y-m-d H:i:s'),
        ];
        var_dump($data);die;
        db('statistics')->insert($data);
    }
}