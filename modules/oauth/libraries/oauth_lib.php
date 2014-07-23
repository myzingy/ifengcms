<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class oauth_lib
{
	function oauth_lib(){
		// Get CI Instance
		// Get CI Instance
		$this->CI = &get_instance();
		include FCPATH."/modules/oauth/libraries/wechat.class.php";
	}
	//设置微信启动信息
	function setWechatOption($option=array()){
		//$option=$option?$option:$this->site;
		if(!$option){
			$option=array(
				'token'=>$this->site->token,
				'appid'=>$this->site->appid,
				'appsecret'=>$this->site->appsecret,
			);
		}
		$this->option=$option;
		$this->weObj = new Wechat($this->option);
	}
}