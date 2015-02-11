<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class reply extends Public_Controller
{
	/**
	 * Constructor
	 */
	function reply(){
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
}