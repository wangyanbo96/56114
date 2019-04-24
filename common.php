<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件


//快递鸟电商ID
defined('EBusinessID') or define('EBusinessID', '1381045');

//快递鸟AppKey
defined('AppKey') or define('AppKey', '4ab514d2-51be-4a49-8214-6c440409cc5e');

//快递鸟查物流信息url
defined('ReqURL') or define('ReqURL', 'http://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx');


//腾讯云短信appid
defined('appid') or define('appid', '1400202194'); // 1400开头

// 短信应用SDK AppKey
defined('appkey') or define('appkey', '0e8da3f44924808a266b174ff8553c34'); 

// 短信模板ID，需要在短信应用中申请
defined('templateId') or define('templateId', '316851'); // NOTE: 这里的模板ID`7839`只是一个示例，真实的模板ID需要在短信控制台中申请

// 短信签名
defined('smsSign') or define('smsSign', '其声呜呜然');