<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class reply_yidong extends Public_Controller
{
	/**
	 * Constructor
	 */
	function reply_yidong(){
		parent::Public_Controller();
		// Load the Auth_form_processing class
		$this->load->library('reply_lib');
	}
	function api(){
		$query=$_SERVER['QUERY_STRING'];
		parse_str($query,$_GET);
		$this->site=$this->reply_lib->init();
		$this->reply_lib->apply();
	}
	function getNick($id=0){
		if($id<1) die('over...');
		$this->reply_lib->init();
		$this->reply_lib->getNick($id);
	}
}