<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;



//全国地名
Route::get('56114/area','index/demo/area');

//用户登录
Route::get('hello/login','index/demo/login');

//按线路查找物流公司列表
Route::get('hello/xllist','index/demo/gs1');

//发送验证码
Route::get('hello/sendxin','index/demo/sendxin');

//用户注册
Route::get('hello/reg','index/demo/reg');

//添加线路
Route::get('56114/addxl','index/demo/addxl1');

//汽车列表
Route::get('56114/car','index/demo/car');


//公司列表
Route::get('56114/gs','index/demo/gs');

//省级列表
Route::get('56114/sheng','index/demo/diming');

//海外地区列表
Route::get('56114/waiguo','index/demo/diming1');

//Route::get('index/index/index','index/duanxin/send');