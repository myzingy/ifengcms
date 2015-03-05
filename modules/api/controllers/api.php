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
		parse_str($_SERVER['QUERY_STRING'], $_GET);
		$prarm=func_get_args();	
		$info=array(
			'get'=>$prarm
			,'post'=>$_POST
		);
		$this->api_lib->status($info,10000);
		
		$action=array_shift($prarm);
		
		$noCheckLoginAct=array('register','forget','sendsms','login','avatar'
		#统计代码
		,'totalcode','totalcodeActivity'
		#抽奖
		,'dodraw','updateDrawUser'
		#答题
		,'quesUser','doques','getDayQuestions'
		#获取用户授权及授权跳转
		,'getWechatAuth','setWechatAuth','getWechatJS'
		);
		if(!in_array($action, $noCheckLoginAct)){
			if(!$this->api_lib->check()){
				$this->api_lib->status($info,-1);
				die(json_encode($info));
			}
		}
		if(method_exists($this->api_lib,$action)){
			$info=call_user_func_array(array(&$this->api_lib, $action), $prarm);
			$info['error']=$info['error']?$info['error']:'';
			$info['act']=$action;
		}else{
			$this->api_lib->status($info,10001,__CLASS__.'->'.$action.'() 没有定义');
		}
		$result=@json_encode($info);
		if(empty($_GET['callback'])){
			die($result);
		}else{
			die($_GET['callback']."($result);");
		}
		
	}
	
}