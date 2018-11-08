<?php
#蔚蓝传媒
//define( 'APPID', 'wx23d35cf9f1616aa2' );
//define( 'SECRET', '10771aa3c4f79222c0b65d5d6a059a05' );
#嘟嘟校园
define( 'APPID', 'wxeac5ee619fe202cb');
define( 'SECRET', '57982c3f587b987211eb0dbb62e4739c');

//define( 'APPID', 'wx4c8335254e4b5b93' );
//define( 'SECRET', '60c1e5a1d49e26ef549fc59a02cf1133' );
define( 'RESPONSE_TYPE', 'code' );

$redirect_uri	= isset( $_GET['redirect_uri'] ) ? $_GET['redirect_uri'] : false;
$scope			= isset( $_GET['scope'] ) ? $_GET['scope'] : false; // snsapi_base 不弹出，snsapi_userinfo 弹出窗口
$state			= isset( $_GET['state'] ) ? $_GET['state'] : uniqid();

header("Content-type: text/html; charset=utf-8");

if( !$redirect_uri ) {
	die('缺少回调地址：redirect_uri');
}

if( !$scope ) {
	die('缺少应用授权作用域：snsapi_base 或 snsapi_userinfo');
}

$wx_redirect_uri = urlencode( 'http://www.duduxy.com/wechat/auth_new.php?redirect_uri='.$redirect_uri. '&scope=' .$scope. '&state=' . $state );
if( isset($_GET['code'])) {

	require_once( dirname(__FILE__) . '/snoopy.class.php' );

	$snoopy = new Snoopy;

	$code = $_GET['code'];
	$snoopy->fetch('https://api.weixin.qq.com/sns/oauth2/access_token?appid='.APPID.'&secret='.SECRET.'&code='.$code.'&grant_type=authorization_code');
	$result = json_decode($snoopy->results);
	$redirect_uri = base64_decode($redirect_uri);

	if( !strpos($redirect_uri, '?') ) {
		$redirect_uri .= '?';
	}

	if( $scope != 'snsapi_userinfo' ) {

		header("Location: {$redirect_uri}&openid={$result->openid}&state=".$state);
		exit;
	}
	
	$snoopy->fetch('https://api.weixin.qq.com/sns/userinfo?access_token='.$result->access_token.'&openid='.$result->openid.'&lang=zh_CN');
	$result = json_decode($snoopy->results);

	header("Location: {$redirect_uri}&openid={$result->openid}&nickname={$result->nickname}&sex={$result->sex}&language={$result->language}&city={$result->city}&province={$result->province}&country={$result->country}&headimgurl={$result->headimgurl}");
	exit;

} else {
	header("Location: https://open.weixin.qq.com/connect/oauth2/authorize?appid=".APPID."&redirect_uri=".$wx_redirect_uri."&response_type=".RESPONSE_TYPE."&scope=".$scope."&state=".$state."#wechat_redirect");
	exit;
}

?>
