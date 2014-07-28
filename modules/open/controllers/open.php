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
	function fighter(){
		$this->load->library('open');
		$AppID='16da8c490cf88da1c78f6f3875a8dc72';
		$AppSecret='01126e1ca8ede9228ce0e658c0672c40';
		
		//登陆示例
		//生成带signature的URL
		$param=$this->open->hashUrlString(array(
		    'appid'=>$AppID,
		    'timestamp'=>TIME,
		    'nonce'=>rand(),
		    'type'=>'json',
		    'siteid'=>1
		),$AppSecret);
		$url="http://mophpweb.duapp.com/index.php/open/act/login?".$param;
		$cont=$this->open->http_post($url,"");
		var_dump($url,$cont);
	}
}