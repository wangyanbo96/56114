<?php
/**
 * 支付宝支付
 */

return [
        //应用ID,您的APPID。
        'app_id' => "2016090900469776",

        //商户私钥, 请把生成的私钥文件中字符串拷贝在此
        'merchant_private_key' => "MIIEogIBAAKCAQEAtPzzEOWeMMntjhF5FxL4LT2yOJ/xelj0HnfJ3AohUHdw6woNLdgDUJAxd07cRYWq6Om/0rmPZn/gwDn06SRNKU6wHCtnC3s6fZtW/CC07x9K60bc+tB2akUmEgnqa+2kYGC5AEcBjnvhGPVHyxjO6OnDLBSsGkZmGaJfRueDuSkD0Kd581zr4tEqo3MKzQIUMMpCPDwgKJOzoOJcawSXZpMynzgrGzd73VE0jiS9SLt0+XZ79/iALGIjM6QhbVqhCdaKONh9xisAPJrDqnLTvNZStUSbWXZRLT7NI3jUAXsaDKI3Tp2N8TnrggVMA0JjkILLUDC/TBA3lAAmq0SuAwIDAQABAoIBAGX6bP7lUpgM/0RghglBUAM10zjirk6Q1qRgPKY2MwVC96YH2Nsu7tczGBwwnB60LVPleYdDtRA9F6dYQK4pHS0cQFDvB7XJbnCd2Ypg53IhALbHC/ZrBX3ljoj1e1fq5AbGGReV6sOc+31zn0tJpDRKmwU4dKytZBQnkGXER01JY1xe+UlNfaWfn19QVEbVbASijFYW9vZkNCiVWH671nkzsOtG4Z7Hm04JLixJ6O16gksQ96ZpM8TZYy3dODc5f1MwIpwqjRrXKpyagiPzX3qVP+7tMBHr+73S0ZwZg7lwgBoY+KWGj95rNNwt/yRuC3sH1A1hohP4iXMQVlpzzaECgYEA2hf8N/Q+LBCsze45S0Xpg3K+HTEsBB1D2qLZryK0qdgB61laXg357AFjcrOwlaAu6wjPcuV78ysf2flGU9QnZ5LWCymA6rNxyRYp665Z8SyhAwSZa8sql9v/maWiuZfGLHyiKeSZ6dsOaYCfYKTEPp1d/hmgqJbwEhupVhlXorECgYEA1HH2CsbJkZ8KPXodB3yoVamSUYEkzZCRslEcUsUahI42eXOGn9Gh511kQTHoab6AYXFY700WuwJfvhF34EWS/NBRfcLuDTO0+DQqoAykSiS/eW/ebnmvUB+czxnm7841AQLfcXgGB6xppIxEFgsQiJ6D8anMdF6hVt4aq+s5QPMCgYBdY03mK9j/h3hnie3QtLQkTFrqJycg7+MhWQB7xRG1dMCFpbJTegqdq46JDDa+K2RL0m76VRf5bWrrLJmXxc3FxROQngoM9h/wKmRy/iqXYjPkFfEYPlwTwOm3Qjzm2f3LCOdrpu7dO6b1OFWGzacW5M3dw92Os2tDZcLiEmH7UQKBgGym9D1CueiePHCAucQQf0+AcHL658WywLFARooWgJ921Gl9KkcmwfVAkHu/eKxMYAB2JhQNiyiN6EWfTX4IV0qxiFwXjAR4g3/Fvl8o2AWLkdob2tkJpx4FbP+GsdPH1Nf1ji3MQtk91rHvgwr7EbOtzrGUNBr4Iu/4nA99OET9AoGAETUFFqBid7fmIqeOC2JkK/LxXRaMUhFiX5H4JnBX0PE9aL8qIUJJ2jIpZFpwALAS7JF98h76xygIUi8jCU7oxEVbYXcvApVnbgkGpTvrRqdiGBLHQ8OpAoORqGqUjDM1iE3tKUnYlTn4E9GehmPWdr6e49GacwG/vTNgk7GH7FY=",

        //异步通知地址
        'notify_url' => "http://www.baidu.com",

        //同步跳转
        'return_url' => "http://www.baidu.com",

        //编码格式
        'charset' => "UTF-8",

        //签名方式
        'sign_type'=>"RSA2",

        //支付宝网关
        'gatewayUrl' => "https://openapi.alipaydev.com/gateway.do",

        //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
        'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAtBWUzbZCfUnyVqH/sl9vjTsyB+RWtKL/RMMw157434ZMLN/BQVmCocPDfXRsn/oAR80tyNIMAbyx+YSzH6s4QcDEAMQ9vSltk+tHxG8ExkvwPKQBlhwfVD29kGxj9x7zMF2yfnuu3cZUguhKyl0RKyB2kZkHnQR+vNCFyz7Gj7zoBP0BA/LGsCSXeV6Ggzi1gXgMTbrtLuIDZAcklvcJTTQe0zG/fplAcV/1SKwruaan27JLfviwBHd98U08Ni/Qgk/vEwkoaaYA9WyDLNeD7NBrecoWInAVSKKm94CWC34xRe1AAImCLG8sbqxjRNBhXJ3UBLugqpem+phFpCB6wQIDAQAB",
];
