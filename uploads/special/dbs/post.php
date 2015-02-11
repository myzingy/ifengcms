<?php
define( 'APP_SINGUP', true );

require_once( dirname( dirname(__FILE__) ) . '/settings.php' );

if( $_POST ) {
	header('Content-type: text/html; charset=utf-8');

	echo '<script>alert("您的报名信息已提交！");window.location.href="'.$referer.'";</script>';
	exit;
	$error = create_signup_dbs();

	$referer = $_SERVER['HTTP_REFERER'];
	if( is_error($error) ) {
		echo '<script>alert("'.$error['message'].'");window.location.href="'.$referer.'";</script>';
	} else {
		echo '<script>alert("您的报名信息已提交！");window.location.href="'.$referer.'";</script>';
	}
}

?>