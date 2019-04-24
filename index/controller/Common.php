<?php
namespace app\index\controller;
use think\Model;
use think\Db;
use think\Controller;

class Common extends Controller
{
    //记录访问日志
    public function _initialize()
    {
        header('Access-Control-Allow-Origin:*');//允许跨域

        //var_dump($_COOKIE);die;

        $ip = $this->get_real_ip();

        //$data = file_get_contents("http://ip.ws.126.net/ipquery?ip=" . $ip);
        $data = file_get_contents("http://api.map.baidu.com/?qt=dec&oue=1&callback=v2ex");

        $data = substr("$data",17);
        $data = substr($data,0,strlen($data)-1);
        $data = '['.$data.']';
        $data = json_decode($data);
        $data = $data['0'];
        $data = get_object_vars($data);
        $content = get_object_vars($data['content']);
        $city = $content['cname'];
        $content = get_object_vars($data['current_city']);
        $region = $content['up_province_name'];




        $ip = $_SERVER['REMOTE_ADDR'];

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
        $data = [
            'source' => @$_SERVER['HTTP_REFERER'],//来源
            'url' => 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
            //'explorer' => $_SERVER['HTTP_USER_AGENT'],//系统信息
            'explorer' => $source,//浏览器信息
            'ip' => $ip,
            'region' => @$region,
            'city' => @$city,
            'sta_time' => date('Y-m-d H:i:s'),
        ];
        //var_dump($data);die;
        db('statistics')->insert($data);
    }

    public function get_real_ip(){
        static $realip;
        if(isset($_SERVER)){
            if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
                $realip=$_SERVER['HTTP_X_FORWARDED_FOR'];
            }else if(isset($_SERVER['HTTP_CLIENT_IP'])){
                $realip=$_SERVER['HTTP_CLIENT_IP'];
            }else{
                $realip=$_SERVER['REMOTE_ADDR'];
            }
        }else{
            if(getenv('HTTP_X_FORWARDED_FOR')){
                $realip=getenv('HTTP_X_FORWARDED_FOR');
            }else if(getenv('HTTP_CLIENT_IP')){
                $realip=getenv('HTTP_CLIENT_IP');
            }else{
                $realip=getenv('REMOTE_ADDR');
            }
        }
        return $realip;
    }
    public function get_real_ip1(){
        if(getenv('HTTP_CLIENT_IP')) {
            $onlineip = getenv('HTTP_CLIENT_IP');
        } elseif(getenv('HTTP_X_FORWARDED_FOR')) {
            $onlineip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif(getenv('REMOTE_ADDR')) {
            $onlineip = getenv('REMOTE_ADDR');
        } else {
            $onlineip = $HTTP_SERVER_VARS['REMOTE_ADDR'];
        }
        echo $onlineip;
    }
}