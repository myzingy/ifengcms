<?php

require_once "jssdk.php";
$jssdk = new JSSDK("wxeac5ee619fe202cb", "57982c3f587b987211eb0dbb62e4739c");
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