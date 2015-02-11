<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class fighter extends Public_Controller
{
	/**
	 * Constructor
	 */
	function fighter(){
		parent::Public_Controller();
		// Load the Auth_form_processing class
		$this->load->library('fighter_lib');
	}
	function index($appid='',$siteid=0){
		$arr=array();
		if($appid) $arr['appid']=$appid;
		if($siteid) $arr['siteid']=$siteid;
		if($arr){
			$this->fighter_lib->setFighterCookie($arr);
		}
		//检查cookie
		$cookie=$this->fighter_lib->getFighterCookie();
		if(($cookie['siteid']>0 && $cookie['appid']) || $arr){
			//检查用户状态
			$status=$this->fighter_lib->getFighterStatus();
			//[-1,0,1] 空记录，正常，待审核
			if($status==-1){
				$url='http://121.199.26.107:9999/auth.php?redirect_uri='.base64_encode(site_url('fighter/setUserCookie')).'&scope=snsapi_base&state=123';
			}elseif($status==1){
				//$url=base_url().'/mobile/battleplane/reg.html';
				$url=base_url().'/mobile/battleplane/';
			}else{
				$url=base_url().'/mobile/battleplane/';
			}
			header("location:".$url);
			exit;
		}
		//重新获取授权
		$url='http://121.199.26.107:9999/auth.php?redirect_uri='.base64_encode(site_url('fighter/setUserCookie')).'&scope=snsapi_base&state=123';
		header("location:".$url);
	}
	function form(){
		$this->fighter_lib->form($this->_container);
	}
	function setUserCookie(){
		$query=$_SERVER['QUERY_STRING'];
		parse_str($query,$param);
		$this->fighter_lib->setFighterCookie($param);
		$url=base_url().'/mobile/battleplane/';
		//$cookie=$this->fighter_lib->getFighterCookie();
		//var_dump("<pre>",$param,$cookie);
		header("location:".$url);
		exit;
	}
}