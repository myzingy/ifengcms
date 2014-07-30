<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class article extends Public_Controller
{
	/**
	 * Constructor
	 */
	function article(){
		parent::Public_Controller();
		// Load the Auth_form_processing class
		$this->load->library('article_lib');
	}
	//点赞
	function applaud(){
		$this->article_lib->applaud();
	}
}