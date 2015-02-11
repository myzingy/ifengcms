<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class xwzwb extends Admin_Controller
{
	/**
	 * Constructor
	 */
	function xwzwb(){
		parent::Admin_Controller();
		// Load the Auth_form_processing class
		$this->load->library('xwzwb_lib');
	}
	function index(){
		
	}
	function form(){
		$this->xwzwb_lib->form($this->_container);
	}
}