<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class comment extends Public_Controller
{
	/**
	 * Constructor
	 */
	function comment(){
		parent::Public_Controller();
		// Load the Auth_form_processing class
		$this->load->library('comment_lib');
	}
	function index(){
		
	}
	function form(){
		$this->comment_lib->form($this->_container);
	}
}