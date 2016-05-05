<?php 
return array (
  'alipay'=>array(
		'partner'=>'208832342334223333',//合作身份者id，以2088开头的16位纯数字
		'seller_email'=>'smallbatlxh@163.com',//收款支付宝账号
		'key'=>'',//安全检验码，以数字和字母组成的32位字符
		'sign_type'=>strtoupper('MD5'),//签名方式 不需修改
		'input_charset'=>strtolower('utf-8'),//字符编码格式 目前支持 gbk 或 utf-8
		'cacert'=>getcwd().'/application/Alipay/Extend/Payment/Alipay'.'\\cacert.pem',//ca证书路径地址，用于curl中ssl校验,请保证cacert.pem文件在当前文件夹目录中
		'transport'=>'http',//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
	),
);