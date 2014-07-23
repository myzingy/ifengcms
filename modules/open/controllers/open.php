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
}