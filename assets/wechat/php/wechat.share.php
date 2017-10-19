<?php

require_once "jssdk.php";
$jssdk = new JSSDK("wxc23b7df9375cc300", "d21884f6ad9f6408c0b69d6d0d970f76");
$signPackage = $jssdk->GetSignPackage();

$wechat_conf = array(
	'appId' => $signPackage["appId"],
	'timestamp'=>$signPackage["timestamp"],
	'nonceStr'=>$signPackage["nonceStr"],
	'signature'=>$signPackage["signature"]
);

$json=json_encode($wechat_conf);
if($_GET['callback']){
	die("{$_GET['callback']}($json);");
}
die($json);


?>