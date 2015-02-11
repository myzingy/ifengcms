<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class praise extends Admin_Controller
{
	/**
	 * Constructor
	 */
	function praise(){
		parent::Admin_Controller();
		// Load the Auth_form_processing class
		$this->load->library('praise_lib');
	}
	function index(){
		
	}
	function form(){
		$this->praise_lib->form($this->_container);
	}
}