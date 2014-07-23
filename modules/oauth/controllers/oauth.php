<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class oauth extends Public_Controller
{
	/**
	 * Constructor
	 */
	function oauth(){
		parent::Public_Controller();
		// Load the Auth_form_processing class
		$this->load->library('oauth_lib');
	}
	function index(){
		//$url='http://121.199.26.107:9999/auth.php?redirect_uri='.base64_encode(site_url('oauth/show')).'&scope=snsapi_userinfo&state=123';
		$url='http://121.199.26.107:9999/auth.php?redirect_uri='.base64_encode(site_url('oauth/setUserCookie')).'&scope=snsapi_base&state=123';
		//$this->oauth_lib->setWechatOption();
		//var_dump($this->weObj);
		header("location:".$url);
	}
	function setUserCookie(){
		$query=$_SERVER['QUERY_STRING'];
		parse_str($query,$param);
		var_dump('<pre>',$param);
	}
}