<?php
namespace app\index\controller;
use think\Model;
use think\Db;
use think\Controller;
use think\View;

class Index extends Common
{
    public function _initialize(){
        parent::_initialize();
        header('Access-Control-Allow-Origin:*');//允许跨域
    }

    //格式化json数据
    public function jsonaa($status, $message = '', $data = array()) {
            
        $result = array (
            'status' => $status,
            'message' => $message,
            'data' => $data 
        );
        return json_encode ( $result);
    }

    //错误信息
    public function err($data = ''){
        $result = [
            'status' => '500',
            'message'=> '服务器错误，联系后台管理员',
            'data'   => $data,
        ];
        return json_encode ( $result);
    }

    public function index()
    {
        return '早上好，吃了吗';
    }



    //注册
    public function reg(){
        $name     = @$_POST['name'];
        $tel      = @$_POST['tel'];
        $num      = @$_POST['yanzhengma'];        
        $gs       = @$_POST['gs'];
        $password = @$_POST['password'];
        $address  = @$_POST['address'];
        $type     = @$_POST['type'];
        //验证该手机号是否已注册
        $data = db('userinfo')->where('mobile',$tel)->select();
        if(!empty($data)){
            return $this->jsonaa(202,'该手机号已注册',$data);
        }


        $data     = db('code')->where('tel',$tel)->find();
        $code     = $data['code'];
        //设置万能验证码，调试用
        $code = db('code')->where('tel',$tel)->value('code');
        //var_dump($code);die;
        $data = '';
        if($num == $code){
            @$res['modified_user'] = @$data['name'] = @$name;
            @$res['mobile'] = $data['mobile'] = $tel;
            $data['password'] = $password;
            $data['company'] = $gs;
            $data['address'] = $address;
            $res['type'] = '注册';
            $res['modified_time'] = date("Y-m-d H:i:s");
            if(empty($res['modified_user'])){
                //var_dump($name);
                $res['modified_user'] = '新用户';
            }

            db('userinfo')->insert($data);

            $res = db('logger')->insert($res);


            //注册成功，设置token
            $res = '';
            $data = db('userinfo')->where('mobile',$tel)->find();
            $data1['userid'] = $data['rid'];
            $userid = $data['rid'];

            $token  = base64_encode($data['rid'].time());

            $res = [
                'userid' => $userid,
                'token'  => $token,
                'time'   => date('Y-m-d H:i:s'),
            ];
            $num = db('token')->where('userid',$userid)->find();
            if(!$num){
                db('token') -> insert($res);
            }else{
                db('token') ->where('userid',$userid)-> update($res);
            }
            //var_dump($data);
            //验证成功
            $data1['token'] = $token;
            $data1['tel'] = $tel;
            $data1['name'] = $name;
            return $this->jsonaa(200,'注册成功',$data1);
        }else{
            return $this->jsonaa(201,'验证码不正确',$data);
        }       
        
    }

    //登录
    public function login(){

        $username = $_POST['tel'];
        $password = @$_POST['password'];
        $code = @$_POST['num'];

        if($code == ''){
            //按手机号登陆
            //var_dump($password);die;
            $data = db('userinfo')->where('mobile',$username)->find();

            if($password == $data['password']){
                //设置token

                $userid = $data['rid'];
                $token  = base64_encode($data['rid'].time());
                $res = [
                    'userid' => $userid,
                    'token'  => $token,
                    'time'   => date('Y-m-d H:i:s'),
                ];
                $num = db('token')->where('userid',$userid)->find();
                if(!$num){
                    db('token') -> insert($res);
                }else{
                    db('token') ->where('userid',$userid)-> update($res);
                }
                $res['tel'] = $username;
                $res['name'] = $data['name'];
                //setcookie('usertel',$username);
                return $this->jsonaa('200','登陆成功',$res);

            }else{
                return $this->jsonaa('201','登陆失败，密码错误',$data);
            }
        }elseif ($password == '') {
            //按验证码登陆
            $data = db('code')->where('tel',$username)->find();
            //var_dump($code);
            if($code == $data['code']){
                //设置token
                 $data = db('userinfo')->where('mobile',$username)->find();
                 $userid = $data['rid'];

                 $token  = base64_encode($data['rid'].time());

                 $res = [
                     'userid' => $userid,
                     'token'  => $token,
                     'time'   => date('Y-m-d H:i:s'),
                 ];
                 $num = db('token')->where('userid',$userid)->find();
                 if(!$num){
                     db('token') -> insert($res);
                 }else{
                     db('token') ->where('userid',$userid)-> update($res);
                 }
                $res['tel'] = $username;
                 setcookie('usertel',$username);
                 var_dump($_COOKIE);
                return $this->jsonaa('200','登陆成功',$res);
                //return "登陆成功";
            }else{
                return $this->jsonaa('202','登陆失败，验证码错误',$data);
            }
        }
    }

    //修改密码
    public function editPs($tel,$num,$newps){
        $code = db('code')->where('tel',$tel)->value('code');
        if($code == $num){
            //验证码正确
            $data = [
                'password'=>$newps,
            ];
            $res = db('userinfo')->where('mobile',$tel)->update($data);
            return $this->jsonaa('200','修改成功',$res);
        }else {
            return $this->jsonaa('201','验证码不正确',$num);
        }

    }

    

    
    //发送短信
    public function sendxin($tel){
        $tel=$_GET['tel'];
        $num = '';
        $num = rand(1000,9999);
        /*
        $str = ['0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'];
        for ($i=0; $i < 15; $i++) { 
            $sum = rand(0,61);
            $num = $num.$str[$sum];
        }
        */
        $data=array(
                    'tel'=>$tel,
                    'code'=>$num,
                    'time'=>date('Y-m-d H:i:s')
                );


        $res = db('code')->where('tel',$tel)->find();
            if(!empty($res)){
                $data=db("code")->where('tel',$tel)->update($data);//数据库没有就插入，有就更新
            }else{
                $data=db("code")->insert($data);
            }   
        $res = db('code')->where('tel',$tel)->value('code');
        return json_encode($res);
        
        // if(!empty($data[0]['code'])){
        //     array_unshift($data, array_pop($data));
        //     $sum = $time - $data[0]['time'];

        //     //判断验证码是否过期
        //     if($sum < 6000){
        //         die;
        //     }
        // }
        die;
        // var_dump($data);
        // var_dump($time);
        // var_dump("hello");**********************************************************************
        //die;
        header('content-type:text/html;charset=utf-8');
          
        $sendUrl = 'http://v.juhe.cn/sms/send'; //短信接口的URL

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

    
}
