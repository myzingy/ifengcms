<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class open extends Public_Controller
{
	/**
	 * Constructor
	 */
	function open(){
		parent::Public_Controller();
		// Load the Auth_form_processing class
		$this->load->library('open_lib');
	}
	function act(){
		$info=array();
		$this->open_lib->status($info,10000);
		if($this->open_lib->check()){
			$prarm=func_get_args();	
			$info=array(
				'get'=>$prarm
				,'post'=>$_POST
			);
			$this->open_lib->status($info,10000);
			
			$action=array_shift($prarm);
			
			if(method_exists($this->open_lib,$action)){
				$info=call_user_func_array(array(&$this->open_lib, $action), $prarm);
				$info['error']=$info['error']?$info['error']:'';
				$info['act']=$action;
			}else{
				$this->open_lib->status($info,10001,__CLASS__.'->'.$action.'() 没有定义');
			}
		}
		die(json_encode($info));
	}
	function fighter($siteid=1){
		$this->load->library('open');
		$AppID=$this->open_lib->AppID;
		$AppSecret=$this->open_lib->AppSecret;
		//登陆示例
		//生成带signature的URL
		$param=$this->open->hashUrlString(array(
		    'appid'=>$AppID,
		    'timestamp'=>TIME,
		    'nonce'=>rand(),
		    'type'=>'json',
		    'siteid'=>$siteid
		),$AppSecret);
		if(strstr($_SERVER['HTTP_HOST'],'192.168')){
			$url='http://'.$_SERVER['HTTP_HOST']."/fighter/index.php/open/act/login?".$param;
		}else{
			$url="http://fighter.wisheli.com/index.php/open/act/login?".$param;	
		}
		redirect($url);
	}
}