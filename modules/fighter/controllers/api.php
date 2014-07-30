<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class api extends Public_Controller
{
	/**
	 * Constructor
	 */
	function __construct(){
		parent::Public_Controller();
		// Load the Auth_form_processing class
		$this->load->library('api_lib');
	}
	/*
	 * url 带？号后的参数可通过$_REQUEST获取
	 */
	function act(){
		$prarm=func_get_args();	
		$info=array(
			'get'=>$prarm
			,'post'=>$_POST
		);
		$this->api_lib->status($info,10000);
		
		$action=array_shift($prarm);
		
		$noCheckLoginAct=array();
		if(!in_array($action, $noCheckLoginAct)){
			$wechat=$this->api_lib->checkWechat();
			if($wechat['status']!=0){
				$this->api_lib->status($wechat,-1);
				die(json_encode($wechat));
			}
		}
		
		if(method_exists($this->api_lib,$action)){
			$info=call_user_func_array(array(&$this->api_lib, $action), $prarm);
			$info['error']=$info['error']?$info['error']:'';
			$info['act']=$action;
		}else{
			$this->api_lib->status($info,10001,__CLASS__.'->'.$action.'() 没有定义');
		}
		die(@json_encode($info));
	}
	
}